<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Advert;
use App\Models\Advert_image;
use Illuminate\Http\Request;

class ProcessDealerFeedController extends Controller
{
    public function process($dealerId)
    {
        $user = User::where('dealer_id', $dealerId)->first();
        if (!$user) {
           
            return response()->json(['error' => 'Dealer not found'], 404);
        }

        $url = env('DEALER_FEED_URL');
        try {
            $response = Http::get($url);
            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch data feed'], 500);
            }

            $data = $response->body();
            $rows = array_map('str_getcsv', explode("\n", $data));
            $header = array_shift($rows);
            $csvData = [];

            foreach ($rows as $row) {
                if (count($row) === count($header)) {
                    $csvData[] = array_combine($header, $row);
                }
            }

            if (empty($csvData)) {
                return response()->json(['error' => 'No valid data in feed'], 422);
            }

            DB::beginTransaction();
            try {
                $processed = 0;
                $skipped = 0;
                $markedAsSold = 0;

               
                $feedVehicleIds = [];
                foreach ($csvData as $carData) {
                    if (!empty($carData['VehicleID'])) {
                        $feedVehicleIds[] = $carData['VehicleID'];
                    }
                }

        
                $carsNotInFeed = Car::whereHas('advert', function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->where('status', '!=', 'sold'); 
                })
                ->whereNotIn('vehicle_id', $feedVehicleIds)
                ->get();

           
                foreach ($carsNotInFeed as $car) {
                    try {
                        $advert = Advert::where('advert_id', $car->advert_id)->first();
                        if ($advert) {
                            $advert->update(['status' => 'sold']);
                            $markedAsSold++;
                          
                        }
                    } catch (\Exception $e) {
                       
                    }
                }

               
                foreach ($csvData as $carData) {
                    if (empty($carData['VehicleID']) || empty($carData['Make']) || empty($carData['Model'])) {
                        $skipped++;
                        continue;
                    }

                    $existingCar = Car::where('vehicle_id', $carData['VehicleID'])->first();
                    if ($existingCar) {
                      
                        $existingAdvert = Advert::where('advert_id', $existingCar->advert_id)->first();
                        if ($existingAdvert && $existingAdvert->status === 'sold') {
                            $existingAdvert->update(['status' => 'active']);
                        }
                        $skipped++;
                        continue;
                    }

                    $adExpiryDays = 365;
                    $expiryDate = Carbon::now()->addDays($adExpiryDays)->format('Y-m-d');
                    $customImageUrl = 'https://purecar.co.uk/assets/coming_soon.png'; 
                    $firstImageUrl = null;
                    if (!empty($carData['Images'])) {
                        $images = explode(',', $carData['Images']);
                        $firstImageUrl = trim($images[0]);
                    }

                    $advertData = [
                        'user_id' => $user->id,
                        'name' => $carData['Make'] . ' ' . $carData['Model'],
                        'license_plate' => $carData['Reg'] ?? null,
                        'miles' => isset($carData['Mileage']) ? (int)$carData['Mileage'] : null,
                        'engine' => "2l",
                        'owner' => "1",
                        'description' => $carData['Description'] ?? null,
                        'expiry_date' => $expiryDate,
                        'status' => 'active',
                        'image' => !empty($firstImageUrl) ? $firstImageUrl : $customImageUrl,
                        'main_image' => !empty($firstImageUrl) ? $firstImageUrl : $customImageUrl,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    try {
                        $advert = Advert::create($advertData);

                        if (!$advert || !$advert->advert_id) {
                            $skipped++;
                            continue;
                        }

                  

                        if (!empty($carData['Images'])) {
                            $images = explode(',', $carData['Images']);
                            foreach ($images as $imageUrl) {
                                $imageUrl = trim($imageUrl);
                                if (!empty($imageUrl)) {
                                    try {
                                        Advert_image::create([
                                            'advert_id' => $advert->advert_id,
                                            'image_url' => $imageUrl,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ]);
                                    } catch (\Exception $imageException) {
                                    }
                                }
                            }
                        }

                        $carDataRecord = [
                            'advert_id' => $advert->advert_id,
                            'vehicle_id' => $carData['VehicleID'],
                            'model' => $carData['Model'] ?? null,
                            'make' => $carData['Make'] ?? null,
                            'fuel_type' => $carData['Fuel'] ?? null,
                            'transmission_type' => $carData['Transmission'] ?? 'N/A',
                            'body_type' => $carData['BodyStyle'] ?? 'N/A',
                            'variant' => $carData['Variant'] ?? null,
                            'keyword' => '',
                            'price' => isset($carData['Price']) ? (float)$carData['Price'] : null,
                            'year' => isset($carData['Year']) ? (int)$carData['Year'] : null,
                            'seller_type' => $user->role,
                            'image' => !empty($firstImageUrl) ? $firstImageUrl : $customImageUrl,
                            'main_image' => !empty($firstImageUrl) ? $firstImageUrl : $customImageUrl,
                            'miles' => isset($carData['Mileage']) ? (int)$carData['Mileage'] : null,
                            'engine_size' => (isset($carData['CC']) && is_numeric($carData['CC'])) 
                                ? round($carData['CC'] / 1000, 1) . 'L' 
                                : null,
                            'doors' => isset($carData['Doors']) ? (int)$carData['Doors'] : null,
                            'seats' => isset($carData['Seats']) ? (int)$carData['Seats'] : null,
                            'colors' => $carData['Colour'] ?? null,
                            'license_plate' => $carData['Reg'] ?? null,
                            'Bhp' => isset($carData['BHP']) ? (int)$carData['BHP'] : null,
                            'Rpm' => $carData['Rpm'] ?? null,
                            'RigidArtic' => $carData['RigidArtic'] ?? null,
                            'BodyShape' => $carData['BodyShape'] ?? null,
                            'NumberOfAxles' => null,
                            'FuelTankCapacity' => $carData['FuelTankCapacity'] ?? null,
                            'FuelCatalyst' => $carData['FuelCatalyst'] ?? null,
                            'Aspiration' => $carData['Aspiration'] ?? null,
                            'FuelSystem' => $carData['FuelSystem'] ?? null,
                            'FuelDelivery' => $carData['FuelDelivery'] ?? null,
                            'NumberOfCylinders' => isset($carData['NumberOfCylinders']) ? (int)$carData['NumberOfCylinders'] : null,
                            'gear_box' => $carData['Transmission'] ?? 'N/A',
                            'DriveType' => $carData['DriveType'] ?? null,
                            'Range' => $carData['Range'] ?? null,
                            'Trim' => $carData['Trim'] ?? null,
                            'Scrapped' => $carData['Scrapped'] ?? 0,
                            'Imported' => $carData['Imported'] ?? 0,
                            'ExtraUrban' => $carData['ExtraUrbanMPG'] ?? 0,
                            'UrbanCold' => $carData['UrbanMPG'] ?? 0,
                            'Combined' => $carData['CombinedMPG'] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        try {
                            $car = Car::create($carDataRecord);

                            if (!$car || !$car->id) {
                                $skipped++;
                                continue;
                            }

                            $processed++;

                        } catch (\Exception $carException) {
                            $skipped++;
                            continue;
                        }

                    } catch (\Exception $advertException) {
                        $skipped++;
                        continue;
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => 'All adverts processed successfully',
                    'processed' => $processed,
                    'skipped' => $skipped,
                    'marked_as_sold' => $markedAsSold,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error processing data feed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data feed'], 500);
        }
    }
}