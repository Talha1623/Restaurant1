<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing API endpoint...\n";
    
    // Test database connection
    $restaurantCount = \App\Models\Restaurant::count();
    echo "Restaurants in database: {$restaurantCount}\n";
    
    $menuCount = \App\Models\Menu::count();
    echo "Menus in database: {$menuCount}\n";
    
    $certificateCount = \App\Models\Certificate::count();
    echo "Certificates in database: {$certificateCount}\n";
    
    // Test the specific restaurant ID 3
    $restaurant = \App\Models\Restaurant::find(3);
    if ($restaurant) {
        echo "Restaurant 3 found: {$restaurant->business_name}\n";
        
        // Test menus query
        $menus = \App\Models\Menu::where('restaurant_id', 3)->get();
        echo "Menus for restaurant 3: {$menus->count()}\n";
        
        // Test certificates query
        $certificates = \App\Models\Certificate::where('restaurant_id', 3)->get();
        echo "Certificates for restaurant 3: {$certificates->count()}\n";
        
    } else {
        echo "Restaurant 3 not found\n";
        $allRestaurants = \App\Models\Restaurant::all();
        echo "Available restaurants:\n";
        foreach ($allRestaurants as $r) {
            echo "- ID: {$r->id}, Name: {$r->business_name}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
