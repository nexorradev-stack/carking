<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Car extends Model
{
    protected $table = 'cars';
    protected $primaryKey = 'car_id';
    
    protected $fillable = [
        'advert_id',
        'vehicle_id',
        'dealer_id',
        'model',
        'make',
        'fuel_type',
        'transmission_type',
        'body_type',
        'variant',
        'keyword',
        'price',
        'year',
        'seller_type',
        'image',
        'main_image',
        'miles',
        'engine_size',
        'doors',
        'seats',
        'colors',
        'gear_box',
        'license_plate',
        'Rpm',
        'RigidArtic',
        'BodyShape',
        'NumberOfAxles',
        'FuelTankCapacity',
        'GrossVehicleWeight',
        'FuelCatalyst',
        'Aspiration',
        'FuelSystem',
        'FuelDelivery',
        'Bhp',
        'Kph',
        'Transmission',
        'EngineCapacity',
        'NumberOfCylinders',
        'DriveType',
        'Trim',
        'Range',
        'Scrapped',
        'Imported',
        'ExtraUrban',
        'UrbanCold',
        'Combined',
        'registered',
        'origin',
        'owners',
        'slug', // Add slug to fillable if you want to set it manually
    ];

    public function advert()
    {
        return $this->belongsTo(Advert::class, 'advert_id', 'advert_id');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Advert::class, 'advert_id', 'id', 'advert_id', 'user_id');
    }

    public function advert_images()
    {
        return $this->hasManyThrough(Advert_image::class, Advert::class, 'advert_id', 'advert_id', 'advert_id', 'advert_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($car) {
            try {
                $car->slug = static::generateSlug($car);
            } catch (\Exception $e) {
                // Log the error but don't fail the car creation
                \Log::error('Error generating slug for car: ' . $e->getMessage(), [
                    'car_data' => $car->toArray()
                ]);
                // Fallback slug
                $car->slug = 'car-' . uniqid();
            }
        });
        
        static::updating(function ($car) {
            // Only update slug if relevant fields changed
            if ($car->isDirty('make') || $car->isDirty('model') || $car->isDirty('year')) {
                try {
                    $car->slug = static::generateSlug($car);
                } catch (\Exception $e) {
                    // Log but don't fail the update
                    \Log::error('Error updating slug for car: ' . $e->getMessage());
                }
            }
        });
    }

    protected static function generateSlug($car)
    {
        // Use safe defaults and avoid accessing non-existent properties
        $make = !empty($car->make) ? Str::slug($car->make) : 'make-' . rand(1000, 9999);
        $model = !empty($car->model) ? Str::slug($car->model) : 'model-' . rand(1000, 9999);
        $year = !empty($car->year) ? $car->year : rand(2000, 2024);
        
        // Simplified slug without location since it's not available
        $baseSlug = "{$make}-{$model}-{$year}";
        
        // Ensure slug isn't too long (most DB varchar limits are 255)
        $baseSlug = substr($baseSlug, 0, 200);
        
        // Check for existing slugs to make it unique
        $count = 1;
        $originalSlug = $baseSlug;
        
        while (static::where('slug', $baseSlug)
               ->where('car_id', '!=', $car->car_id ?? 0)
               ->exists()) {
            $baseSlug = $originalSlug . '-' . $count;
            $count++;
            
            // Prevent infinite loop
            if ($count > 1000) {
                $baseSlug = $originalSlug . '-' . uniqid();
                break;
            }
        }
        
        return $baseSlug;
    }
}