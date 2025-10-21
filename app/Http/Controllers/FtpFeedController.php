<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use App\Models\Advert;
use App\Models\Advert_image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FtpFeedController extends Controller
{
    public function processFtpFeeds()
    {
        $possiblePaths = [
            public_path('feed-csv-files'), 
            base_path('public/feed-csv-files')
        ];
        
        $feedPath = null;
        foreach ($possiblePaths as $path) {
            if (File::exists($path) && File::isDirectory($path)) {
                $feedPath = $path;
                break;
            }
        }
        
        if (!$feedPath) {
        
            return response()->json(['error' => 'Feeds directory not found'], 404);
        }
        
        $allFiles = File::files($feedPath);
        $csvFiles = array_filter($allFiles, function ($file) {
            return in_array(strtolower($file->getExtension()), ['csv', 'CSV']);
        });

        if (empty($csvFiles)) {
        
            return response()->json(['message' => 'No CSV files to process'], 200);
        }

        $totalProcessed = 0;
        $totalSkipped = 0;
        $totalMarkedAsSold = 0;
        $processedFiles = [];

        foreach ($csvFiles as $csvFile) {
            try {
                $result = $this->processCSVFile($csvFile->getPathname());
                $totalProcessed += $result['processed'];
                $totalSkipped += $result['skipped'];
                $totalMarkedAsSold += $result['marked_as_sold'];
                $processedFiles[] = $csvFile->getFilename();

                File::delete($csvFile->getPathname());

                
            } catch (\Exception $e) {
                
                continue;
            }
        }

       

        return response()->json([
            'message' => 'FTP feeds processed successfully',
            'total_processed' => $totalProcessed,
            'total_skipped' => $totalSkipped,
            'total_marked_as_sold' => $totalMarkedAsSold,
            'processed_files' => $processedFiles
        ]);
    }

    private function processCSVFile($filePath)
    {
        $processed = 0;
        $skipped = 0;
        $markedAsSold = 0;

        if (!File::exists($filePath)) {
      
            throw new \Exception('File not found: ' . $filePath);
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
        
            throw new \Exception('Cannot open file: ' . $filePath);
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
         
            throw new \Exception('Cannot read headers from file: ' . $filePath);
        }

        $headers = array_map('trim', $headers);
        $csvFormat = $this->detectCsvFormat($headers);

        $allVehicles = [];
        $dealerVehicles = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($headers)) {
                
                $skipped++;
                continue; 
            }

            $vehicleData = [];
            for ($i = 0; $i < count($headers); $i++) {
                $vehicleData[$headers[$i]] = isset($row[$i]) ? trim($row[$i]) : '';
            }

            $normalizedData = $this->normalizeVehicleData($vehicleData, $csvFormat);
            $dealerId = $this->getDealerId($normalizedData);
            $vehicleId = $this->getVehicleId($normalizedData);

            if (empty($dealerId) || empty($vehicleId)) {
               
                $skipped++;
                continue;
            }

            if (!isset($dealerVehicles[$dealerId])) {
                $dealerVehicles[$dealerId] = [];
            }
            
            $dealerVehicles[$dealerId][] = $normalizedData;
            $allVehicles[] = $normalizedData;
        }

        fclose($handle);

        if (empty($allVehicles)) {
            
            return ['processed' => 0, 'skipped' => 0, 'marked_as_sold' => 0];
        }

        DB::beginTransaction();

        try {
            foreach ($dealerVehicles as $dealerId => $vehicles) {
                $user = User::where('dealer_id', $dealerId)
                           ->where('source_type', 'ftp_feed')
                           ->first();

                if (!$user) {
                   
                    $skipped += count($vehicles);
                    continue;
                }

                $feedVehicleIds = array_column($vehicles, 'Vehicle_ID');
                $soldCount = $this->markSoldVehicles($user, $feedVehicleIds, count($vehicles));
                $markedAsSold += $soldCount;

                foreach ($vehicles as $vehicleData) {
                    $result = $this->processVehicle($vehicleData, $user);
                    if ($result) {
                        $processed++;
                        
                    } else {
                        $skipped++;
                      
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
           
            throw $e;
        }

        
        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'marked_as_sold' => $markedAsSold
        ];
    }

   private function processVehicle($vehicleData, $user)
{
    try {
        if (empty($vehicleData['Vehicle_ID']) || empty($vehicleData['Make']) || empty($vehicleData['Model'])) {
            \Log::warning('Missing required vehicle data', [
                'vehicle_id' => $vehicleData['Vehicle_ID'] ?? 'missing',
                'make' => $vehicleData['Make'] ?? 'missing',
                'model' => $vehicleData['Model'] ?? 'missing'
            ]);
            return false;
        }

        $vehicleId = trim($vehicleData['Vehicle_ID']);
        $dealerId = $this->getDealerId($vehicleData);

        $existingCar = Car::where('vehicle_id', $vehicleId)
                          ->where('dealer_id', $dealerId)
                          ->first();
        
        if ($existingCar) {
            $existingAdvert = Advert::where('advert_id', $existingCar->advert_id)->first();
            if ($existingAdvert) {
                if ($existingAdvert->status === 'sold') {
                    $existingAdvert->update(['status' => 'active']);
                }
                $existingAdvert->touch();
            }
            return false;
        }

       
        $images = [];
        $mainImage = 'https://purecar.co.uk/assets/coming_soon.png';
        
        if (!empty($vehicleData['PictureRefs'])) {
            $imageUrls = explode(',', $vehicleData['PictureRefs']);
            $images = array_map('trim', $imageUrls);
            if (!empty($images[0])) {
                $mainImage = $images[0];
            }
        }

        $adExpiryDays = 365;
        $expiryDate = Carbon::now()->addDays($adExpiryDays)->format('Y-m-d');
        
        $advertData = [
            'user_id' => $user->id,
            'name' => trim($vehicleData['Make']) . ' ' . trim($vehicleData['Model']),
            'license_plate' => !empty($vehicleData['FullRegistration']) ? $vehicleData['FullRegistration'] : null,
            'miles' => !empty($vehicleData['Mileage']) && is_numeric($vehicleData['Mileage']) ? (int)$vehicleData['Mileage'] : null,
            'engine' => $this->formatEngineSize($vehicleData['EngineSize'] ?? null),
            'owner' => !empty($vehicleData['PreviousOwners']) && is_numeric($vehicleData['PreviousOwners']) ? (int)$vehicleData['PreviousOwners'] : 1,
            'description' => !empty($vehicleData['Comments']) ? $vehicleData['Comments'] : null,
            'expiry_date' => $expiryDate,
            'status' => 'active',
            'image' => $mainImage,
            'main_image' => $mainImage,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $advert = Advert::create($advertData);
        if (!$advert || !$advert->advert_id) {
          
            return false;
        }

        // Create advert images
        foreach ($images as $imageUrl) {
            if (!empty($imageUrl)) {
                try {
                    Advert_image::create([
                        'advert_id' => $advert->advert_id,
                        'image_url' => trim($imageUrl),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                   
                    continue;
                }
            }
        }

    
        $carData = [
            'advert_id' => $advert->advert_id,
            'vehicle_id' => $vehicleId,
            'dealer_id' => $dealerId,
            'model' => !empty($vehicleData['Model']) ? $vehicleData['Model'] : null,
            'make' => !empty($vehicleData['Make']) ? $vehicleData['Make'] : null,
            'fuel_type' => !empty($vehicleData['FuelType']) ? $vehicleData['FuelType'] : null,
            'transmission_type' => !empty($vehicleData['Transmission']) ? $vehicleData['Transmission'] : 'N/A',
            'body_type' => !empty($vehicleData['Bodytype']) ? $vehicleData['Bodytype'] : 'N/A',
            'variant' => !empty($vehicleData['Variant']) ? $vehicleData['Variant'] : null,
            'keyword' => '',
            'price' => !empty($vehicleData['Price']) && is_numeric($vehicleData['Price']) ? (float)$vehicleData['Price'] : null,
            'year' => !empty($vehicleData['Year']) && is_numeric($vehicleData['Year']) ? (int)$vehicleData['Year'] : null,
            'seller_type' => $user->role,
            'image' => $mainImage,
            'main_image' => $mainImage,
            'miles' => !empty($vehicleData['Mileage']) && is_numeric($vehicleData['Mileage']) ? (int)$vehicleData['Mileage'] : null,
            'engine_size' => $this->formatEngineSize($vehicleData['EngineSize'] ?? null),
            'doors' => !empty($vehicleData['Doors']) && is_numeric($vehicleData['Doors']) ? (int)$vehicleData['Doors'] : null,
            'seats' => !empty($vehicleData['NumberOfSeats']) && is_numeric($vehicleData['NumberOfSeats']) ? (int)$vehicleData['NumberOfSeats'] : null,
            'colors' => !empty($vehicleData['Colour']) ? $vehicleData['Colour'] : null,
            'license_plate' => !empty($vehicleData['FullRegistration']) ? $vehicleData['FullRegistration'] : null,
            'Bhp' => null,
            'Rpm' => null,
            'RigidArtic' => null,
            'BodyShape' => !empty($vehicleData['Bodytype']) ? $vehicleData['Bodytype'] : null,
            'NumberOfAxles' => null,
            'FuelTankCapacity' => null,
            'FuelCatalyst' => null,
            'Aspiration' => null,
            'FuelSystem' => null,
            'FuelDelivery' => null,
            'NumberOfCylinders' => null,
            'gear_box' => !empty($vehicleData['Transmission']) ? $vehicleData['Transmission'] : 'N/A',
            'DriveType' => (!empty($vehicleData['FourWheelDrive']) && $vehicleData['FourWheelDrive'] === 'Y') ? '4WD' : null,
            'Range' => null,
            'Trim' => !empty($vehicleData['Variant']) ? $vehicleData['Variant'] : null,
            'Scrapped' => 0,
            'Imported' => 0,
            'ExtraUrban' => 0,
            'UrbanCold' => 0,
            'Combined' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

      
        if (isset($vehicleData['RegisteredDate']) && 
            !is_null($vehicleData['RegisteredDate']) && 
            trim($vehicleData['RegisteredDate']) !== '' && 
            !in_array(trim($vehicleData['RegisteredDate']), ['0', '0.0', '0.00', '0.000'])) {
            
            $carData['registered'] = $this->formatDate($vehicleData['RegisteredDate']);
        }
        
        if (isset($vehicleData['Origin']) && 
            !is_null($vehicleData['Origin']) && 
            trim($vehicleData['Origin']) !== '' && 
            !in_array(trim($vehicleData['Origin']), ['0', '0.0', '0.00', '0.000'])) {
            $carData['origin'] = $vehicleData['Origin'];
        }
        
        if (isset($vehicleData['PreviousOwners']) && 
            !is_null($vehicleData['PreviousOwners']) && 
            trim($vehicleData['PreviousOwners']) !== '' && 
            !in_array(trim($vehicleData['PreviousOwners']), ['0', '0.0', '0.00', '0.000']) && 
            !(is_numeric($vehicleData['PreviousOwners']) && floatval($vehicleData['PreviousOwners']) == 0)) {
            $carData['owners'] = $vehicleData['PreviousOwners'];
        }

     
        $car = Car::create($carData);

        if (!$car || !$car->car_id) {
           
            
         
            $advert->delete();
            return false;
        }

        

        return true;

    } catch (\Exception $e) {
        
        return false;
    }
}


private function formatDate($dateString)
{
    if (empty($dateString) || trim($dateString) === '') {
        return null;
    }

    $dateString = trim($dateString);
    
    try {
        
        $formats = [
            'd/m/Y',    
            'm/d/Y',    
            'Y-m-d',    
            'd-m-Y',    
            'm-d-Y',    
            'd/m/y',     
            'm/d/y',     
            'Y/m/d',    
        ];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
               
                $year = (int)$date->format('Y');
                if ($year >= 1900 && $year <= date('Y') + 1) {
                    return $date->format('Y-m-d');
                }
            }
        }
        
       
        $carbonDate = Carbon::parse($dateString);
        $year = $carbonDate->year;
        
        if ($year >= 1900 && $year <= date('Y') + 1) {
            return $carbonDate->format('Y-m-d');
        }
        
    } catch (\Exception $e) {
        
    }
    
   
    return null;
}

    private function detectCsvFormat($headers)
    {
        if (in_array('Dealer_ID', $headers) && in_array('Vehicle_ID', $headers)) {
            return 'format1';
        }
        if (in_array('Feed_Id', $headers) && in_array('Vehicle_Id', $headers)) {
            return 'format2';
        }
        return 'format1';
    }

    private function normalizeVehicleData($vehicleData, $csvFormat)
    {
        $normalized = [];

        if ($csvFormat === 'format2') {
            $mapping = [
                'Dealer_ID' => $vehicleData['Feed_Id'] ?? '',
                'Vehicle_ID' => $vehicleData['Vehicle_Id'] ?? '',
                'FullRegistration' => $vehicleData['FullRegistration'] ?? '',
                'Colour' => $vehicleData['Colour'] ?? '',
                'FuelType' => $vehicleData['FuelType'] ?? '',
                'Year' => $vehicleData['Year'] ?? '',
                'Mileage' => $vehicleData['Mileage'] ?? '',
                'Bodytype' => $vehicleData['Bodytype'] ?? '',
                'Doors' => $vehicleData['Doors'] ?? '',
                'Make' => $vehicleData['Make'] ?? '',
                'Model' => $vehicleData['Model'] ?? '',
                'Variant' => $vehicleData['Variant'] ?? '',
                'EngineSize' => $vehicleData['EngineSize'] ?? '',
                'Price' => $vehicleData['Price'] ?? '',
                'Transmission' => $vehicleData['Transmission'] ?? '',
                'PictureRefs' => $vehicleData['PictureRefs'] ?? '',
                'ServiceHistory' => $vehicleData['ServiceHistory'] ?? '',
                'PreviousOwners' => $vehicleData['PreviousOwners'] ?? '',
                'Category' => $vehicleData['Category'] ?? '',
                'FourWheelDrive' => $vehicleData['FourWheelDrive'] ?? '',
                'Options' => $vehicleData['Options'] ?? '',
                'Comments' => $vehicleData['Comments'] ?? '',
                'New' => $vehicleData['New'] ?? '',
                'Used' => $vehicleData['Used'] ?? '',
                'Site' => $vehicleData['Site'] ?? '',
                'Origin' => $vehicleData['Origin'] ?? '',
                'V5' => $vehicleData['V5'] ?? '',
                'Condition' => $vehicleData['Condition'] ?? '',
                'ExDemo' => $vehicleData['ExDemo'] ?? '',
                'FranchiseApproved' => $vehicleData['FranchiseApproved'] ?? '',
                'TradePrice' => $vehicleData['TradePrice'] ?? '',
                'TradePriceExtra' => $vehicleData['TradePriceExtra'] ?? '',
                'ServiceHistoryText' => $vehicleData['ServiceHistoryText'] ?? '',
                'Cap_Id' => $vehicleData['Cap_Id'] ?? '',
                'NumberOfSeats' => $vehicleData['NumberOfSeats'] ?? '',
                'RegisteredDate' => '',
                'AttentionGrabber' => '',
                'Glass_Id' => '',
                'Video_URL' => '',
                'Website_Url' => ''
            ];

            foreach ($mapping as $standardKey => $value) {
                $normalized[$standardKey] = $value;
            }
        } else {
            $normalized = $vehicleData;
        }

        return $normalized;
    }

    private function getDealerId($vehicleData)
    {
        return trim($vehicleData['Dealer_ID'] ?? '');
    }

    private function getVehicleId($vehicleData)
    {
        return trim($vehicleData['Vehicle_ID'] ?? '');
    }

    private function markSoldVehicles($user, $feedVehicleIds, $feedVehicleCount)
    {
        $markedAsSold = 0;
        $dealerId = $user->dealer_id;

        if ($feedVehicleCount > 0) {
            $carsNotInFeed = Car::whereHas('advert', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', '!=', 'sold');
            })
            ->where('dealer_id', $dealerId)
            ->whereNotIn('vehicle_id', $feedVehicleIds)
            ->get();

            foreach ($carsNotInFeed as $car) {
                try {
                    $advert = Advert::where('advert_id', $car->advert_id)->first();
                    if ($advert) {
                        $advertAge = Carbon::parse($advert->updated_at)->diffInHours(Carbon::now());
                        $hasSubstantialResponse = $feedVehicleCount >= 1;

                        if ($advertAge >= 1 && $hasSubstantialResponse) {
                            $advert->update(['status' => 'sold']);
                            $markedAsSold++;
                          
                        }
                    }
                } catch (\Exception $e) {
                   
                    continue;
                }
            }
        }

        return $markedAsSold;
    }

    private function formatEngineSize($engineSize)
    {
        if (empty($engineSize) || !is_numeric($engineSize)) {
            return null;
        }

        $size = (float)$engineSize;
        if ($size > 10) {
            $size = $size / 1000;
        }
        
        return round($size, 1) . 'L';
    }

    public function debugFeedsDirectory()
    {
        $feedPath = public_path('feeds');
        
        $debugInfo = [
            'feeds_path' => $feedPath,
            'path_exists' => File::exists($feedPath),
            'is_directory' => File::isDirectory($feedPath),
            'is_readable' => is_readable($feedPath),
            'permissions' => substr(sprintf('%o', fileperms($feedPath)), -4) ?? 'unknown',
            'all_files' => [],
            'csv_files' => []
        ];

        if (File::exists($feedPath) && File::isDirectory($feedPath)) {
            try {
                $allFiles = File::files($feedPath);
                foreach ($allFiles as $file) {
                    $fileInfo = [
                        'name' => $file->getFilename(),
                        'extension' => $file->getExtension(),
                        'size' => $file->getSize(),
                        'path' => $file->getPathname(),
                        'is_readable' => is_readable($file->getPathname())
                    ];
                    
                    $debugInfo['all_files'][] = $fileInfo;
                    
                    if (in_array(strtolower($file->getExtension()), ['csv', 'CSV'])) {
                        $debugInfo['csv_files'][] = $fileInfo;
                    }
                }
            } catch (\Exception $e) {
                $debugInfo['error'] = $e->getMessage();
            }
        }

      
        return response()->json($debugInfo);
    }
}