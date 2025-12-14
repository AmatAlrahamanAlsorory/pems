<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationGPSSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            ['name' => 'استوديو الرياض', 'lat' => 24.7136, 'lng' => 46.6753],
            ['name' => 'موقع جدة', 'lat' => 21.4858, 'lng' => 39.1925],
            ['name' => 'استوديو الدمام', 'lat' => 26.4207, 'lng' => 50.0888],
            ['name' => 'موقع مكة', 'lat' => 21.3891, 'lng' => 39.8579],
            ['name' => 'استوديو المدينة', 'lat' => 24.5247, 'lng' => 39.5692],
        ];

        foreach ($locations as $locationData) {
            $location = Location::where('name', 'LIKE', '%' . $locationData['name'] . '%')->first();
            
            if (!$location) {
                $location = Location::first();
            }
            
            if ($location && !$location->latitude) {
                $location->update([
                    'latitude' => $locationData['lat'],
                    'longitude' => $locationData['lng'],
                    'map_url' => "https://www.google.com/maps?q={$locationData['lat']},{$locationData['lng']}"
                ]);
            }
        }
    }
}