<?php

// Debug script to check menu addons
// DELETE THIS FILE after debugging for security reasons

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Menu;
use Illuminate\Support\Facades\DB;

header('Content-Type: application/json');

try {
    // Get menu 83
    $menuId = 83;
    $menu = Menu::find($menuId);
    
    if (!$menu) {
        echo json_encode([
            'error' => "Menu $menuId not found",
            'available_menus' => Menu::pluck('id')->toArray()
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    // Check cold_drinks_addons
    $coldDrinksAddons = $menu->cold_drinks_addons;
    
    // Check addons relationship
    $addonsCount = $menu->addons()->count();
    $addons = $menu->addons;
    
    // Check pivot table entries
    $pivotEntries = DB::table('menu_addon')
        ->where('menu_id', $menuId)
        ->get();
    
    // Try to sync manually
    $syncResult = null;
    if (!empty($coldDrinksAddons) && is_array($coldDrinksAddons)) {
        $menu->addons()->detach();
        $menu->addons()->attach($coldDrinksAddons);
        $menu->refresh();
        
        $syncResult = [
            'synced' => true,
            'addon_ids_synced' => $coldDrinksAddons,
            'new_addons_count' => $menu->addons()->count()
        ];
    }
    
    echo json_encode([
        'menu_id' => $menuId,
        'menu_name' => $menu->name,
        'cold_drinks_addons' => $coldDrinksAddons,
        'cold_drinks_addons_type' => gettype($coldDrinksAddons),
        'cold_drinks_addons_is_array' => is_array($coldDrinksAddons),
        'addons_relationship_count' => $addonsCount,
        'addons_data' => $addons->map(function($addon) {
            return [
                'id' => $addon->id,
                'name' => $addon->name,
                'price' => $addon->price
            ];
        })->toArray(),
        'pivot_table_entries' => $pivotEntries,
        'manual_sync_result' => $syncResult,
        'note' => 'If manual sync worked, the addons should now show in menu list'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}

