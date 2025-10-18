<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\RestaurantAddon;
use App\Models\MenuCategory;
use App\Models\SecondFlavor;
use App\Models\MenuImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiMenuController extends Controller
{
    /**
     * Get form data for menu creation (categories, addons, static options)
     */
    public function getFormData($restaurantId)
    {
        try {
            $restaurant = Restaurant::findOrFail($restaurantId);
            
            // Get global menu categories from settings
            $categories = MenuCategory::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'description', 'image']);
            
            // Format categories with full image URL
            $formattedCategories = $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'image' => $category->image,
                    'image_url' => $category->image ? asset('storage/' . $category->image) : null
                ];
            });
            
            // Get global second flavors from settings
            $secondFlavors = SecondFlavor::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'image']);
            
            // Format second flavors with full image URL
            $formattedSecondFlavors = $secondFlavors->map(function($flavor) {
                return [
                    'id' => $flavor->id,
                    'name' => $flavor->name,
                    'image' => $flavor->image,
                    'image_url' => $flavor->image ? asset('storage/' . $flavor->image) : null
                ];
            });
            
            // Get addons for this restaurant
            $addons = RestaurantAddon::where('restaurant_id', $restaurantId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'description', 'image']);
            
            // Format addons with full image URL
            $formattedAddons = $addons->map(function($addon) {
                return [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price ? (float)$addon->price : 0,
                    'description' => $addon->description,
                    'image' => $addon->image,
                    'image_url' => $addon->image ? asset('storage/' . $addon->image) : null
                ];
            });
            
            // Static options - Complete form data
            $formOptions = [
                'currencies' => [
                    ['value' => 'GBP', 'label' => 'GBP (£)'],
                    ['value' => 'USD', 'label' => 'USD ($)'],
                    ['value' => 'EUR', 'label' => 'EUR (€)'],
                    ['value' => 'PKR', 'label' => 'PKR (₨)']
                ],
                'status_options' => [
                    ['value' => 'active', 'label' => 'Active ✓'],
                    ['value' => 'inactive', 'label' => 'Inactive ✗']
                ],
                'availability_options' => [
                    ['value' => true, 'label' => 'Available ✓'],
                    ['value' => false, 'label' => 'Not Available ✗']
                ],
                'spice_levels' => [
                    ['value' => 0, 'label' => 'No Spice'],
                    ['value' => 1, 'label' => 'Mild (1⭐)'],
                    ['value' => 2, 'label' => 'Medium (2⭐)'],
                    ['value' => 3, 'label' => 'Hot (3⭐)'],
                    ['value' => 4, 'label' => 'Very Hot (4⭐)'],
                    ['value' => 5, 'label' => 'Extreme (5⭐)']
                ],
                'dietary_flags' => [
                    ['value' => 'Vegetarian', 'label' => '🥗 Vegetarian'],
                    ['value' => 'Vegan', 'label' => '🌱 Vegan'],
                    ['value' => 'Halal', 'label' => '☪️ Halal'],
                    ['value' => 'Kosher', 'label' => '✡️ Kosher'],
                    ['value' => 'Gluten-Free', 'label' => '🌾 Gluten-Free'],
                    ['value' => 'Dairy-Free', 'label' => '🥛 Dairy-Free'],
                    ['value' => 'Nut-Free', 'label' => '🥜 Nut-Free'],
                    ['value' => 'Non-Vegetarian', 'label' => '🍖 Non-Vegetarian'],
                    ['value' => 'Contains Gluten', 'label' => 'Contains Gluten'],
                    ['value' => 'Contains Dairy', 'label' => 'Contains Dairy'],
                    ['value' => 'Contains Nuts', 'label' => 'Contains Nuts']
                ],
                'tags' => [
                    ['value' => 'Popular', 'label' => '⭐ Popular'],
                    ['value' => 'Premium', 'label' => '👑 Premium'],
                    ['value' => 'Spicy', 'label' => '🌶️ Spicy'],
                    ['value' => 'Healthy', 'label' => '💚 Healthy'],
                    ['value' => 'Kids Favorite', 'label' => '👶 Kids Favorite'],
                    ['value' => 'New', 'label' => '🆕 New'],
                    ['value' => 'Bestseller', 'label' => '🔥 Bestseller'],
                    ['value' => 'Chef Special', 'label' => '👨‍🍳 Chef Special'],
                    ['value' => 'Authentic', 'label' => '✨ Authentic'],
                    ['value' => 'Budget', 'label' => '💰 Budget'],
                    ['value' => 'Organic', 'label' => '🌿 Organic']
                ],
                'preparation_time_options' => [
                    ['value' => 10, 'label' => '10 minutes'],
                    ['value' => 15, 'label' => '15 minutes'],
                    ['value' => 20, 'label' => '20 minutes'],
                    ['value' => 25, 'label' => '25 minutes'],
                    ['value' => 30, 'label' => '30 minutes'],
                    ['value' => 45, 'label' => '45 minutes'],
                    ['value' => 60, 'label' => '1 hour']
                ],
                'calorie_ranges' => [
                    ['value' => '0-200', 'label' => 'Low (0-200 cal)'],
                    ['value' => '200-400', 'label' => 'Medium (200-400 cal)'],
                    ['value' => '400-600', 'label' => 'High (400-600 cal)'],
                    ['value' => '600+', 'label' => 'Very High (600+ cal)']
                ]
            ];
            
            return response()->json([
                'success' => true,
                'message' => '✅ Form data loaded successfully',
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->id,
                        'name' => $restaurant->business_name,
                        'legal_name' => $restaurant->legal_name
                    ],
                    'categories' => $formattedCategories,
                    'second_flavors' => $formattedSecondFlavors,
                    'addons' => $formattedAddons,
                    'form_options' => $formOptions,
                    'fields_info' => [
                        'required_fields' => ['name', 'price', 'currency', 'category_id'],
                        'optional_fields' => ['description', 'ingredients', 'vat_price', 'second_flavor_id', 'status', 'is_available', 'spice_level', 'preparation_time', 'calories', 'tags', 'allergen', 'dietary_flags', 'cold_drinks_addons', 'images'],
                        'image_upload' => [
                            'max_images' => 5,
                            'max_size' => '2MB per image',
                            'formats' => ['jpeg', 'png', 'jpg', 'gif']
                        ]
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Form data fetch failed', [
                'restaurant_id' => $restaurantId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Failed to load form data. Please try again.'
            ], 500);
        }
    }

    /**
     * Display a listing of menus for a restaurant
     */
    public function index($restaurantId)
    {
        try {
            $menus = Menu::where('restaurant_id', $restaurantId)
                ->with(['images', 'category' => function($query) {
                    $query->select('id', 'name', 'image');
                }, 'secondFlavor' => function($query) {
                    $query->select('id', 'name', 'image');
                }, 'addons' => function($query) {
                    $query->where('is_active', 1)->select('restaurant_addons.id', 'restaurant_addons.name', 'restaurant_addons.price', 'restaurant_addons.image');
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            // Remove pivot data from addons and format category/secondFlavor
            $menus->getCollection()->transform(function ($menu) {
                if ($menu->addons && $menu->addons->count() > 0) {
                    $menu->addons = $menu->addons->map(function ($addon) {
                        return [
                            'id' => $addon->id,
                            'name' => $addon->name,
                            'price' => $addon->price,
                            'image' => $addon->image ? asset('storage/' . $addon->image) : null
                        ];
                    })->values();
                } else {
                    $menu->addons = [];
                }
                
                // Format category
                if ($menu->category) {
                    $menu->category->image_url = $menu->category->image ? asset('storage/' . $menu->category->image) : null;
                }
                
                // Format secondFlavor
                if ($menu->secondFlavor) {
                    $menu->secondFlavor->image_url = $menu->secondFlavor->image ? asset('storage/' . $menu->secondFlavor->image) : null;
                }
                
                return $menu;
            });
            
            return response()->json([
                'success' => true,
                'data' => $menus->items()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch menus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created menu item
     */
    public function store(Request $request, $restaurantId)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'ingredients' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'vat_price' => 'nullable|numeric|min:0',
                'currency' => 'required|string|in:GBP,USD,EUR,PKR',
                'category_id' => 'required|integer|exists:menu_categories,id',
                'second_flavor_id' => 'nullable|integer|exists:second_flavors,id',
                'status' => 'nullable',
                'is_available' => 'boolean',
                'spice_level' => 'nullable|integer|min:0|max:5',
                'preparation_time' => 'nullable|integer|min:0|max:300',
                'calories' => 'nullable|integer|min:0|max:5000',
                'tags' => 'nullable|string',
                'allergen' => 'nullable|string|max:255',
                'dietary_flags' => 'nullable|string',
                'cold_drinks_addons' => 'nullable',
                'addon_ids' => 'nullable|string',
                'addonlist' => 'nullable|string',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            $data = $request->all();
            $data['restaurant_id'] = $restaurantId;
            
            // Handle status conversion: 0 = inactive, 1 = active
            if ($request->has('status')) {
                $status = $request->input('status');
                if ($status === '0' || $status === 0 || $status === false || $status === 'false') {
                    $data['status'] = 'inactive';
                } elseif ($status === '1' || $status === 1 || $status === true || $status === 'true') {
                    $data['status'] = 'active';
                } elseif (in_array($status, ['active', 'inactive'])) {
                    $data['status'] = $status;
                } else {
                    $data['status'] = 'active'; // Default to active
                }
            } else {
                $data['status'] = 'active'; // Default to active if not provided
            }
            
            // Handle availability conversion: 0 = false, 1 = true
            if ($request->has('is_available')) {
                $availability = $request->input('is_available');
                if ($availability === '0' || $availability === 0 || $availability === false || $availability === 'false') {
                    $data['is_available'] = false;
                } elseif ($availability === '1' || $availability === 1 || $availability === true || $availability === 'true') {
                    $data['is_available'] = true;
                } else {
                    $data['is_available'] = (bool)$availability;
                }
            } else {
                $data['is_available'] = true; // Default to true (available)
            }
            
            // Handle tags - convert comma-separated string to array
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $data['tags'] = array_filter($tags);
            } else {
                $data['tags'] = null;
            }
            
            // Handle dietary flags - convert comma-separated string to array
            if ($request->filled('dietary_flags')) {
                $dietaryFlags = array_map('trim', explode(',', $request->dietary_flags));
                $data['dietary_flags'] = array_filter($dietaryFlags);
            } else {
                $data['dietary_flags'] = null;
            }
            
            // Handle cold drinks addons - support multiple formats
            if ($request->has('cold_drinks_addons')) {
                $addons = $request->cold_drinks_addons;
                
                // Format 1: JSON string "[{\"id\":1},{\"id\":2}]"
                if (is_string($addons) && (strpos($addons, '[') === 0 && strpos($addons, ']') === strlen($addons) - 1)) {
                    $jsonArray = json_decode($addons, true);
                    if (is_array($jsonArray) && isset($jsonArray[0]) && is_array($jsonArray[0]) && isset($jsonArray[0]['id'])) {
                        // Object array format: [{"id":1}, {"id":2}]
                        $data['cold_drinks_addons'] = array_map(function($item) {
                            return $item['id'];
                        }, $jsonArray);
                    } else {
                        // Simple array format: [1,2,3]
                        $data['cold_drinks_addons'] = array_map('intval', $jsonArray);
                    }
                }
                // Format 2: Comma-separated string "14,20,16"
                elseif (is_string($addons)) {
                    $data['cold_drinks_addons'] = array_map('intval', array_filter(explode(',', $addons)));
                }
                // Format 3: Object array [{"id":1}, {"id":2}]
                elseif (is_array($addons) && isset($addons[0]) && is_array($addons[0]) && isset($addons[0]['id'])) {
                    $data['cold_drinks_addons'] = array_map(function($item) {
                        return $item['id'];
                    }, $addons);
                }
                // Format 4: Simple array [1, 2, 3]
                else {
                    $data['cold_drinks_addons'] = $addons;
                }
            } else {
                $data['cold_drinks_addons'] = [];
            }
            
            // Create menu item
            $menu = Menu::create($data);
            
            // Handle addon linking (support both JSON and form-data)
            $addonIds = [];
            
            
            // Check for form-data array format: addon_ids[0], addon_ids[1], etc.
            $allInputs = $request->all();
            foreach ($allInputs as $key => $value) {
                if (strpos($key, 'addon_ids[') === 0) {
                    $addonIds[] = (int)$value;
                }
            }
            
            // If no form-data array found, check for regular addon_ids or addonlist
            if (empty($addonIds)) {
                $addonIdsString = $request->input('addon_ids') ?: $request->input('addonlist');
                if ($addonIdsString) {
                    if (is_array($addonIdsString)) {
                        // Array format: [11, 12, 13]
                        $addonIds = array_map('intval', $addonIdsString);
                    } elseif (is_string($addonIdsString)) {
                        // Check if it's JSON array format: "[14,13]"
                        if (strpos($addonIdsString, '[') === 0 && strpos($addonIdsString, ']') === strlen($addonIdsString) - 1) {
                            $jsonArray = json_decode($addonIdsString, true);
                            if (is_array($jsonArray)) {
                                $addonIds = array_map('intval', $jsonArray);
                            } else {
                                // Comma-separated format: "11,12,13"
                                $addonIds = array_map('intval', array_filter(explode(',', $addonIdsString)));
                            }
                        } else {
                            // Comma-separated format: "11,12,13"
                            $addonIds = array_map('intval', array_filter(explode(',', $addonIdsString)));
                        }
                    } else {
                        // Single value
                        $addonIds = [(int)$addonIdsString];
                    }
                }
            }
            
            
            if (!empty($addonIds)) {
                $menu->addons()->attach($addonIds);
            }
            
            // Also sync cold_drinks_addons to pivot table
            $this->syncColdDrinksAddons($menu, $data['cold_drinks_addons'] ?? []);
            
            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $image->store('menu-images', 'public');
                    $menu->images()->create([
                        'image_url' => $imagePath,
                        'is_primary' => $index === 0, // First image is primary
                        'sort_order' => $index + 1
                    ]);
                }
            }

            // Load relationships for response
            $menu->load(['images', 'restaurant', 'secondFlavor', 'addons']);

            return response()->json([
                'success' => true,
                'message' => 'Menu item created successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove specific addon from menu
     */
    public function removeAddon(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'menu_id' => 'required|integer|exists:menus,id',
                'addon_id' => 'required|integer|exists:restaurant_addons,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Please provide valid menu ID and addon ID.',
                    'errors' => $validator->errors()
                ], 200);
            }

            $menuId = $request->input('menu_id');
            $addonId = $request->input('addon_id');

            // Find the menu
            $menu = Menu::find($menuId);
            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Menu not found. Please check the menu ID.'
                ], 200);
            }

            // Check if addon exists
            $addon = \App\Models\RestaurantAddon::find($addonId);
            if (!$addon) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Addon not found. Please check the addon ID.'
                ], 200);
            }

            // Check if addon is linked to this menu (check both pivot table and cold_drinks_addons field)
            $isLinkedInPivot = $menu->addons()->where('restaurant_addon_id', $addonId)->exists();
            $isLinkedInField = in_array($addonId, $menu->cold_drinks_addons ?? []);
            
            if (!$isLinkedInPivot && !$isLinkedInField) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ This addon is not linked to the specified menu.'
                ], 200);
            }

            // Remove the addon from menu
            $menu->addons()->detach($addonId);

            // Update cold_drinks_addons field to remove this addon
            $currentAddons = $menu->cold_drinks_addons ?? [];
            $updatedAddons = array_filter($currentAddons, function($id) use ($addonId) {
                return $id != $addonId;
            });
            $menu->update(['cold_drinks_addons' => array_values($updatedAddons)]);

            return response()->json([
                'success' => true,
                'message' => '✅ Addon removed successfully from menu!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Remove addon failed', [
                'menu_id' => $request->input('menu_id'),
                'addon_id' => $request->input('addon_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => '❌ Failed to remove addon. Please try again later.'
            ], 500);
        }
    }

    /**
     * Sync cold drinks addons to pivot table
     */
    private function syncColdDrinksAddons($menu, $coldDrinksAddons)
    {
        \Log::info('syncColdDrinksAddons called', [
            'menu_id' => $menu->id,
            'addons' => $coldDrinksAddons,
            'is_empty' => empty($coldDrinksAddons),
            'is_array' => is_array($coldDrinksAddons)
        ]);
        
        if (!empty($coldDrinksAddons) && is_array($coldDrinksAddons)) {
            try {
                // First detach all existing addons, then attach new ones
                $detached = $menu->addons()->detach();
                \Log::info('Detached addons', ['count' => $detached]);
                
                $menu->addons()->attach($coldDrinksAddons);
                \Log::info('Attached addons', ['addon_ids' => $coldDrinksAddons]);
                
                // Verify sync
                $count = $menu->addons()->count();
                \Log::info('Addon count after sync', ['count' => $count]);
            } catch (\Exception $e) {
                \Log::error('Error syncing addons: ' . $e->getMessage());
            }
        } else {
            \Log::warning('Cold drinks addons empty or not array');
        }
    }

    /**
     * Display the specified menu item
     */
    public function show($restaurantId, $menuId)
    {
        try {
            $menu = Menu::where('restaurant_id', $restaurantId)
                ->where('id', $menuId)
                ->with(['images', 'category' => function($query) {
                    $query->select('id', 'name', 'image');
                }, 'secondFlavor' => function($query) {
                    $query->select('id', 'name', 'image');
                }])
                ->firstOrFail();
            
            // Format category and secondFlavor
            if ($menu->category) {
                $menu->category->image_url = $menu->category->image ? asset('storage/' . $menu->category->image) : null;
            }
            if ($menu->secondFlavor) {
                $menu->secondFlavor->image_url = $menu->secondFlavor->image ? asset('storage/' . $menu->secondFlavor->image) : null;
            }
            
            return response()->json([
                'success' => true,
                'data' => $menu
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 200);
        }
    }

    /**
     * Update the specified menu item
     */
    public function update(Request $request, $restaurantId, $menuId)
    {
        try {
            $menu = Menu::where('restaurant_id', $restaurantId)
                ->where('id', $menuId)
                ->firstOrFail();

            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'ingredients' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'vat_price' => 'nullable|numeric|min:0',
                'currency' => 'sometimes|required|string|in:GBP,USD,EUR,PKR',
                'category_id' => 'sometimes|required|integer|exists:menu_categories,id',
                'second_flavor_id' => 'nullable|integer|exists:second_flavors,id',
                'status' => 'sometimes|required|in:active,inactive',
                'is_available' => 'boolean',
                'spice_level' => 'nullable|integer|min:0|max:5',
                'preparation_time' => 'nullable|integer|min:0|max:300',
                'calories' => 'nullable|integer|min:0|max:5000',
                'tags' => 'nullable|string',
                'allergen' => 'nullable|string|max:255',
                'dietary_flags' => 'nullable|string',
                'cold_drinks_addons' => 'nullable',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            $data = $request->all();
            
            // Handle availability checkbox
            if ($request->has('is_available')) {
                $data['is_available'] = true;
            } elseif ($request->has('is_available') && !$request->is_available) {
                $data['is_available'] = false;
            }
            
            // Handle tags
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $data['tags'] = array_filter($tags);
            }
            
            // Handle dietary flags
            if ($request->filled('dietary_flags')) {
                $dietaryFlags = array_map('trim', explode(',', $request->dietary_flags));
                $data['dietary_flags'] = array_filter($dietaryFlags);
            }
            
            // Handle cold drinks addons - support multiple formats
            if ($request->has('cold_drinks_addons')) {
                $addons = $request->cold_drinks_addons;
                
                // Format 1: JSON string "[{\"id\":1},{\"id\":2}]"
                if (is_string($addons) && (strpos($addons, '[') === 0 && strpos($addons, ']') === strlen($addons) - 1)) {
                    $jsonArray = json_decode($addons, true);
                    if (is_array($jsonArray) && isset($jsonArray[0]) && is_array($jsonArray[0]) && isset($jsonArray[0]['id'])) {
                        // Object array format: [{"id":1}, {"id":2}]
                        $data['cold_drinks_addons'] = array_map(function($item) {
                            return $item['id'];
                        }, $jsonArray);
                    } else {
                        // Simple array format: [1,2,3]
                        $data['cold_drinks_addons'] = array_map('intval', $jsonArray);
                    }
                }
                // Format 2: Comma-separated string "14,20,16"
                elseif (is_string($addons)) {
                    $data['cold_drinks_addons'] = array_map('intval', array_filter(explode(',', $addons)));
                }
                // Format 3: Object array [{"id":1}, {"id":2}]
                elseif (is_array($addons) && isset($addons[0]) && is_array($addons[0]) && isset($addons[0]['id'])) {
                    $data['cold_drinks_addons'] = array_map(function($item) {
                        return $item['id'];
                    }, $addons);
                }
                // Format 4: Simple array [1, 2, 3]
                else {
                    $data['cold_drinks_addons'] = $addons;
                }
            }
            
            // Update menu item
            $menu->update($data);
            
            // Sync cold_drinks_addons to pivot table if provided
            if (isset($data['cold_drinks_addons'])) {
                $this->syncColdDrinksAddons($menu, $data['cold_drinks_addons']);
            }
            
            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $image->store('menu-images', 'public');
                    $menu->images()->create([
                        'image_url' => $imagePath,
                        'is_primary' => false,
                        'sort_order' => $menu->images()->count() + $index + 1
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => '🎉 Menu updated successfully! Your changes have been saved.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Menu update failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Failed to update menu item. Please try again later.'
            ], 500);
        }
    }

    /**
     * Remove the specified menu item
     */
    public function destroy($restaurantId, $menuId)
    {
        try {
            $menu = Menu::where('restaurant_id', $restaurantId)
                ->where('id', $menuId)
                ->firstOrFail();
            
            // Delete associated images from storage
            foreach ($menu->images as $image) {
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
            }
            
            // Delete menu item (cascade will delete images)
            $menu->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle menu item availability
     */
    public function toggleAvailability($restaurantId, $menuId)
    {
        try {
            $menu = Menu::where('restaurant_id', $restaurantId)
                ->where('id', $menuId)
                ->firstOrFail();
            
            $menu->update(['is_available' => !$menu->is_available]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu availability updated successfully',
                'data' => [
                    'is_available' => $menu->is_available
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle menu availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get menu statistics for a restaurant
     */
    public function getStats($restaurantId)
    {
        try {
            $stats = [
                'total_menus' => Menu::where('restaurant_id', $restaurantId)->count(),
                'active_menus' => Menu::where('restaurant_id', $restaurantId)->where('status', 'active')->count(),
                'available_menus' => Menu::where('restaurant_id', $restaurantId)->where('is_available', true)->count(),
                'categories_count' => Menu::where('restaurant_id', $restaurantId)->distinct('category')->count(),
                'total_addons' => RestaurantAddon::where('restaurant_id', $restaurantId)->where('is_active', true)->count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch menu statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store menu without restaurant_id in URL
     */
    public function storeWithoutRestaurantId(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'ingredients' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'vat_price' => 'nullable|numeric|min:0',
                'currency' => 'required|string|in:GBP,USD,EUR,PKR',
                'category_id' => 'required|integer|exists:menu_categories,id',
                'second_flavor_id' => 'nullable|integer|exists:second_flavors,id',
                'status' => 'nullable',
                'is_available' => 'boolean',
                'spice_level' => 'nullable|integer|min:0|max:5',
                'preparation_time' => 'nullable|integer|min:0|max:300',
                'calories' => 'nullable|integer|min:0|max:5000',
                'tags' => 'nullable|string',
                'allergen' => 'nullable|string|max:255',
                'dietary_flags' => 'nullable|string',
                'cold_drinks_addons' => 'nullable',
                'addon_ids' => 'nullable|string',
                'addonlist' => 'nullable|string',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            $restaurantId = $request->restaurant_id;
            $data = $request->all();
            $data['restaurant_id'] = $restaurantId;
            
            // Handle status conversion: 0 = inactive, 1 = active
            if ($request->has('status')) {
                $status = $request->input('status');
                if ($status === '0' || $status === 0 || $status === false || $status === 'false') {
                    $data['status'] = 'inactive';
                } elseif ($status === '1' || $status === 1 || $status === true || $status === 'true') {
                    $data['status'] = 'active';
                } elseif (in_array($status, ['active', 'inactive'])) {
                    $data['status'] = $status;
                } else {
                    $data['status'] = 'active'; // Default to active
                }
            } else {
                $data['status'] = 'active'; // Default to active if not provided
            }
            
            // Handle availability conversion: 0 = false, 1 = true
            if ($request->has('is_available')) {
                $availability = $request->input('is_available');
                if ($availability === '0' || $availability === 0 || $availability === false || $availability === 'false') {
                    $data['is_available'] = false;
                } elseif ($availability === '1' || $availability === 1 || $availability === true || $availability === 'true') {
                    $data['is_available'] = true;
                } else {
                    $data['is_available'] = (bool)$availability;
                }
            } else {
                $data['is_available'] = true; // Default to true (available)
            }
            
            // Handle tags - convert comma-separated string to array
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $data['tags'] = array_filter($tags);
            } else {
                $data['tags'] = null;
            }
            
            // Handle dietary flags - convert comma-separated string to array
            if ($request->filled('dietary_flags')) {
                $dietaryFlags = array_map('trim', explode(',', $request->dietary_flags));
                $data['dietary_flags'] = array_filter($dietaryFlags);
            } else {
                $data['dietary_flags'] = null;
            }
            
            // Handle cold drinks addons - support multiple formats
            if ($request->has('cold_drinks_addons')) {
                $addons = $request->cold_drinks_addons;
                
                // Format 1: JSON string "[{\"id\":1},{\"id\":2}]"
                if (is_string($addons) && (strpos($addons, '[') === 0 && strpos($addons, ']') === strlen($addons) - 1)) {
                    $jsonArray = json_decode($addons, true);
                    if (is_array($jsonArray) && isset($jsonArray[0]) && is_array($jsonArray[0]) && isset($jsonArray[0]['id'])) {
                        // Object array format: [{"id":1}, {"id":2}]
                        $data['cold_drinks_addons'] = array_map(function($item) {
                            return $item['id'];
                        }, $jsonArray);
                    } else {
                        // Simple array format: [1,2,3]
                        $data['cold_drinks_addons'] = array_map('intval', $jsonArray);
                    }
                }
                // Format 2: Comma-separated string "14,20,16"
                elseif (is_string($addons)) {
                    $data['cold_drinks_addons'] = array_map('intval', array_filter(explode(',', $addons)));
                }
                // Format 3: Object array [{"id":1}, {"id":2}]
                elseif (is_array($addons) && isset($addons[0]) && is_array($addons[0]) && isset($addons[0]['id'])) {
                    $data['cold_drinks_addons'] = array_map(function($item) {
                        return $item['id'];
                    }, $addons);
                }
                // Format 4: Simple array [1, 2, 3]
                else {
                    $data['cold_drinks_addons'] = $addons;
                }
            } else {
                $data['cold_drinks_addons'] = [];
            }
            
            // Create menu item
            $menu = Menu::create($data);
            
            // Handle addon linking (support both JSON and form-data)
            $addonIds = [];
            
            
            // Check for form-data array format: addon_ids[0], addon_ids[1], etc.
            $allInputs = $request->all();
            foreach ($allInputs as $key => $value) {
                if (strpos($key, 'addon_ids[') === 0) {
                    $addonIds[] = (int)$value;
                }
            }
            
            // If no form-data array found, check for regular addon_ids or addonlist
            if (empty($addonIds)) {
                $addonIdsString = $request->input('addon_ids') ?: $request->input('addonlist');
                if ($addonIdsString) {
                    if (is_array($addonIdsString)) {
                        // Array format: [11, 12, 13]
                        $addonIds = array_map('intval', $addonIdsString);
                    } elseif (is_string($addonIdsString)) {
                        // Check if it's JSON array format: "[14,13]"
                        if (strpos($addonIdsString, '[') === 0 && strpos($addonIdsString, ']') === strlen($addonIdsString) - 1) {
                            $jsonArray = json_decode($addonIdsString, true);
                            if (is_array($jsonArray)) {
                                $addonIds = array_map('intval', $jsonArray);
                            } else {
                                // Comma-separated format: "11,12,13"
                                $addonIds = array_map('intval', array_filter(explode(',', $addonIdsString)));
                            }
                        } else {
                            // Comma-separated format: "11,12,13"
                            $addonIds = array_map('intval', array_filter(explode(',', $addonIdsString)));
                        }
                    } else {
                        // Single value
                        $addonIds = [(int)$addonIdsString];
                    }
                }
            }
            
            
            if (!empty($addonIds)) {
                $menu->addons()->attach($addonIds);
            }
            
            // Also sync cold_drinks_addons to pivot table
            $this->syncColdDrinksAddons($menu, $data['cold_drinks_addons'] ?? []);
            
            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $image->store('menu-images', 'public');
                    $menu->images()->create([
                        'image_url' => $imagePath,
                        'is_primary' => $index === 0, // First image is primary
                        'sort_order' => $index + 1
                    ]);
                }
            }

            // Load relationships for response
            $menu->load(['images', 'restaurant', 'secondFlavor', 'addons']);

            return response()->json([
                'success' => true,
                'message' => 'Menu item created successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get menus without restaurant_id in URL
     */
    public function indexWithoutRestaurantId(Request $request)
    {
        try {
            $query = Menu::with(['images', 'category' => function($query) {
                $query->select('id', 'name', 'image');
            }, 'secondFlavor' => function($query) {
                $query->select('id', 'name', 'image');
            }, 'addons' => function($query) {
                $query->where('is_active', 1)->select('restaurant_addons.id', 'restaurant_addons.name', 'restaurant_addons.price', 'restaurant_addons.image');
            }]);

            // Auto-detect restaurant_id from authenticated user
            $user = $request->user();
            $restaurantId = $request->restaurant_id ?? $user->id;
            
            // Ensure we have a valid restaurant ID
            if (!$restaurantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant ID not found. Please provide restaurant_id parameter.'
                ], 400);
            }
            
            $query->where('restaurant_id', $restaurantId);

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Category filter
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $menus = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            // Remove pivot data from addons and format category/secondFlavor
            $menus->getCollection()->transform(function ($menu) {
                if ($menu->addons && $menu->addons->count() > 0) {
                    $menu->addons = $menu->addons->map(function ($addon) {
                        return [
                            'id' => $addon->id,
                            'name' => $addon->name,
                            'price' => $addon->price,
                            'image' => $addon->image ? asset('storage/' . $addon->image) : null
                        ];
                    })->values();
                } else {
                    $menu->addons = [];
                }
                
                // Format category
                if ($menu->category) {
                    $menu->category->image_url = $menu->category->image ? asset('storage/' . $menu->category->image) : null;
                }
                
                // Format secondFlavor
                if ($menu->secondFlavor) {
                    $menu->secondFlavor->image_url = $menu->secondFlavor->image ? asset('storage/' . $menu->secondFlavor->image) : null;
                }
                
                return $menu;
            });

            return response()->json([
                'success' => true,
                'message' => 'Menus retrieved successfully',
                'data' => $menus->items()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve menus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show menu without restaurant_id in URL
     */
    public function showWithoutRestaurantId($menuId)
    {
        try {
            $menu = Menu::where('id', $menuId)
                ->with(['images', 'category' => function($query) {
                    $query->select('id', 'name', 'image');
                }, 'secondFlavor' => function($query) {
                    $query->select('id', 'name', 'image');
                }])
                ->firstOrFail();
            
            // Format category and secondFlavor
            if ($menu->category) {
                $menu->category->image_url = $menu->category->image ? asset('storage/' . $menu->category->image) : null;
            }
            if ($menu->secondFlavor) {
                $menu->secondFlavor->image_url = $menu->secondFlavor->image ? asset('storage/' . $menu->secondFlavor->image) : null;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item retrieved successfully',
                'data' => $menu
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 200);
        }
    }

    /**
     * Update menu without restaurant_id in URL
     */
    public function updateWithoutRestaurantId(Request $request, $menuId)
    {
        try {
            $menu = Menu::where('id', $menuId)->firstOrFail();

            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'ingredients' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'vat_price' => 'nullable|numeric|min:0',
                'currency' => 'sometimes|required|string|in:GBP,USD,EUR,PKR',
                'category_id' => 'sometimes|required|integer|exists:menu_categories,id',
                'second_flavor_id' => 'nullable|integer|exists:second_flavors,id',
                'status' => 'sometimes|required|in:active,inactive',
                'is_available' => 'boolean',
                'spice_level' => 'nullable|integer|min:0|max:5',
                'preparation_time' => 'nullable|integer|min:0|max:300',
                'calories' => 'nullable|integer|min:0|max:5000',
                'tags' => 'nullable|string',
                'allergen' => 'nullable|string|max:255',
                'dietary_flags' => 'nullable|string',
                'cold_drinks_addons' => 'nullable',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            $data = $request->all();
            
            // Handle availability checkbox
            if ($request->has('is_available')) {
                $data['is_available'] = true;
            } elseif ($request->has('is_available') && !$request->is_available) {
                $data['is_available'] = false;
            }
            
            // Handle tags
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $data['tags'] = array_filter($tags);
            }
            
            // Handle dietary flags
            if ($request->filled('dietary_flags')) {
                $dietaryFlags = array_map('trim', explode(',', $request->dietary_flags));
                $data['dietary_flags'] = array_filter($dietaryFlags);
            }
            
            // Handle cold drinks addons
            if ($request->has('cold_drinks_addons')) {
                $data['cold_drinks_addons'] = $request->cold_drinks_addons;
            }
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                // Delete existing images
                foreach ($menu->images as $image) {
                    Storage::disk('public')->delete($image->image_url);
                    $image->delete();
                }
                
                // Upload new images
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $image->store('menu-images', 'public');
                    $menu->images()->create([
                        'image_url' => $imagePath,
                        'is_primary' => $index === 0,
                        'sort_order' => $index + 1
                    ]);
                }
            }

            $menu->update($data);
            
            // Load relationships for response
            $menu->load(['images', 'restaurant', 'category', 'secondFlavor']);

            return response()->json([
                'success' => true,
                'message' => 'Menu item updated successfully',
                'data' => $menu
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update menu with ID in request body (like delete)
     */
    public function updateWithIdInBody(Request $request)
    {
        try {
            // Handle form-data for PUT requests - Laravel doesn't parse it automatically
            if ($request->isMethod('PUT') && $request->header('Content-Type') === 'multipart/form-data') {
                // For PUT requests with form-data, we need to manually parse the input
                $input = $request->input();
                if (empty($input)) {
                    // If input is empty, try to get from raw content
                    $rawContent = $request->getContent();
                    if (!empty($rawContent)) {
                        parse_str($rawContent, $parsedData);
                        $request->merge($parsedData);
                    }
                }
            }
            
            // Debug: Check what data is being received
            $requestData = $request->all();
            
            // Additional debugging for PUT requests
            \Log::info('PUT Request Debug', [
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'input_data' => $request->input(),
                'all_data' => $request->all(),
                'raw_content' => $request->getContent(),
                'form_data' => $request->except('_token')
            ]);
            
            // Validate the incoming request to ensure 'id' is present
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'ingredients' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'vat_price' => 'nullable|numeric|min:0',
                'currency' => 'sometimes|required|string|in:GBP,USD,EUR,PKR',
                'category_id' => 'sometimes|required|integer|exists:menu_categories,id',
                'second_flavor_id' => 'nullable|integer|exists:second_flavors,id',
                'status' => 'sometimes|required|in:active,inactive',
                'is_available' => 'nullable|in:true,false,1,0,"true","false","1","0"',
                'spice_level' => 'nullable|integer|min:0|max:5',
                'preparation_time' => 'nullable|integer|min:0|max:300',
                'calories' => 'nullable|integer|min:0|max:5000',
                'tags' => 'nullable|string',
                'allergen' => 'nullable|string|max:255',
                'dietary_flags' => 'nullable|string',
                'cold_drinks_addons' => 'nullable',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            // Retrieve the menu by ID from the request body
            $menuId = $request->input('id');
            
            // Debug: Log what ID we're actually receiving
            \Log::info('Menu Update Request', [
                'received_id' => $menuId,
                'request_all' => $request->all(),
                'content_type' => $request->header('Content-Type')
            ]);
            
            // Debug: Log all available menu IDs and database info
            $allMenuIds = Menu::pluck('id')->toArray();
            $totalMenus = Menu::count();
            $latestMenu = Menu::latest()->first();
            
            // Debug: Check database connection
            \Log::info('Database Info', [
                'db_connection' => \DB::connection()->getDatabaseName(),
                'db_driver' => config('database.default')
            ]);
            
            // Check if menu exists
            $menu = Menu::find($menuId);
            
            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Menu item not found. Please check the menu ID and try again.'
                ], 200);
            }
            
            // Optional: Check if menu belongs to authenticated restaurant (if needed)
            // For now, we'll allow any authenticated user to update any menu
            // You can uncomment this if you want to enforce restaurant ownership
            /*
            $user = auth()->user();
            if ($user && $menu->restaurant_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found or access denied'
                ], 200);
            }
            */

            $data = $request->except('id'); // Exclude 'id' from update data
            
            // Handle availability checkbox - convert string to boolean
            if ($request->has('is_available')) {
                $isAvailable = $request->input('is_available');
                if (in_array($isAvailable, ['true', '1', 1, true])) {
                    $data['is_available'] = true;
                } elseif (in_array($isAvailable, ['false', '0', 0, false])) {
                    $data['is_available'] = false;
                } else {
                    $data['is_available'] = (bool)$isAvailable;
                }
            }
            
            // Handle tags
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $data['tags'] = array_filter($tags);
            }
            
            // Handle dietary flags
            if ($request->filled('dietary_flags')) {
                $dietaryFlags = array_map('trim', explode(',', $request->dietary_flags));
                $data['dietary_flags'] = array_filter($dietaryFlags);
            }
            
            // Handle cold drinks addons - support multiple formats
            if ($request->has('cold_drinks_addons')) {
                $addons = $request->cold_drinks_addons;
                
                // Format 1: JSON string "[{\"id\":1},{\"id\":2}]"
                if (is_string($addons) && (strpos($addons, '[') === 0 && strpos($addons, ']') === strlen($addons) - 1)) {
                    $jsonArray = json_decode($addons, true);
                    if (is_array($jsonArray) && isset($jsonArray[0]) && is_array($jsonArray[0]) && isset($jsonArray[0]['id'])) {
                        // Object array format: [{"id":1}, {"id":2}]
                        $data['cold_drinks_addons'] = array_map(function($item) {
                            return $item['id'];
                        }, $jsonArray);
                    } else {
                        // Simple array format: [1,2,3]
                        $data['cold_drinks_addons'] = array_map('intval', $jsonArray);
                    }
                }
                // Format 2: Comma-separated string "14,20,16"
                elseif (is_string($addons)) {
                    $data['cold_drinks_addons'] = array_map('intval', array_filter(explode(',', $addons)));
                }
                // Format 3: Object array [{"id":1}, {"id":2}]
                elseif (is_array($addons) && isset($addons[0]) && is_array($addons[0]) && isset($addons[0]['id'])) {
                    $data['cold_drinks_addons'] = array_map(function($item) {
                        return $item['id'];
                    }, $addons);
                }
                // Format 4: Simple array [1, 2, 3]
                else {
                    $data['cold_drinks_addons'] = $addons;
                }
            } else {
                $data['cold_drinks_addons'] = [];
            }
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                // Delete existing images
                foreach ($menu->images as $image) {
                    if (Storage::disk('public')->exists($image->image_url)) {
                        Storage::disk('public')->delete($image->image_url);
                    }
                    $image->delete();
                }
                
                // Upload new images
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $image->store('menu-images', 'public');
                    $menu->images()->create([
                        'image_url' => $imagePath,
                        'is_primary' => $index === 0,
                        'sort_order' => $index + 1
                    ]);
                }
            }

            // Update the menu
            $menu->update($data);
            
            // Sync cold_drinks_addons to pivot table using the helper method
            $this->syncColdDrinksAddons($menu, $data['cold_drinks_addons'] ?? []);

            return response()->json([
                'success' => true,
                'message' => '🎉 Menu updated successfully! Your changes have been saved.'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Please provide valid data to update the menu item.'
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Menu update failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Failed to update menu item. Please try again later.'
            ], 500);
        }
    }

    /**
     * View menu with ID in request body
     */
    public function viewWithIdInBody(Request $request)
    {
        try {
            // Debug: Check what data is being received
            $requestData = $request->all();
            
            // Validate the incoming request to ensure 'id' is present
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            // Retrieve the menu by ID from the request body
            $menuId = $request->input('id');
            
            // Debug: Log what ID we're actually receiving
            \Log::info('Menu View Request', [
                'received_id' => $menuId,
                'request_all' => $request->all(),
                'content_type' => $request->header('Content-Type')
            ]);
            
            // Check if menu exists
            $menu = Menu::with(['images', 'category' => function($query) {
                $query->select('id', 'name', 'image');
            }, 'secondFlavor' => function($query) {
                $query->select('id', 'name', 'image');
            }, 'addons' => function($query) {
                $query->where('is_active', 1)->select('restaurant_addons.id', 'restaurant_addons.name', 'restaurant_addons.price', 'restaurant_addons.image');
            }])->find($menuId);
            
            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found'
                ], 200);
            }
            
            // Debug logging
            \Log::info('Menu View - Checking Addons', [
                'menu_id' => $menu->id,
                'pivot_count' => $menu->addons ? $menu->addons->count() : 0,
                'cold_drinks_addons' => $menu->cold_drinks_addons,
                'cold_drinks_type' => gettype($menu->cold_drinks_addons),
                'is_array' => is_array($menu->cold_drinks_addons),
                'is_empty' => empty($menu->cold_drinks_addons)
            ]);
            
            // Transform addons to remove pivot data and format properly
            if ($menu->addons && $menu->addons->count() > 0) {
                \Log::info('Loading addons from pivot table', ['count' => $menu->addons->count()]);
                $menu->addons = $menu->addons->map(function ($addon) {
                    return [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'price' => $addon->price,
                        'image' => $addon->image ? asset('storage/' . $addon->image) : null
                    ];
                })->values();
            } else {
                \Log::info('Pivot table empty, checking cold_drinks_addons field');
                // If no addons in pivot table, try to load from cold_drinks_addons field
                $coldDrinksAddons = $menu->cold_drinks_addons ?? [];
                \Log::info('Cold drinks addons value', [
                    'raw' => $coldDrinksAddons,
                    'type' => gettype($coldDrinksAddons),
                    'is_array' => is_array($coldDrinksAddons),
                    'is_empty' => empty($coldDrinksAddons)
                ]);
                
                if (!empty($coldDrinksAddons) && is_array($coldDrinksAddons)) {
                    \Log::info('Loading addons from cold_drinks_addons', ['addon_ids' => $coldDrinksAddons]);
                    $addons = \App\Models\RestaurantAddon::whereIn('id', $coldDrinksAddons)
                        ->where('is_active', true)
                        ->get(['id', 'name', 'price', 'image']);
                    
                    \Log::info('Addons loaded from database', ['count' => $addons->count()]);
                    
                    $menu->addons = $addons->map(function ($addon) {
                        return [
                            'id' => $addon->id,
                            'name' => $addon->name,
                            'price' => $addon->price,
                            'image' => $addon->image ? asset('storage/' . $addon->image) : null
                        ];
                    })->values();
                } else {
                    \Log::warning('No addons found in either pivot table or cold_drinks_addons field');
                    $menu->addons = [];
                }
            }
            
            // Format category image URL
            if ($menu->category) {
                $menu->category->image_url = $menu->category->image ? asset('storage/' . $menu->category->image) : null;
            }
            
            // Format secondFlavor image URL
            if ($menu->secondFlavor) {
                $menu->secondFlavor->image_url = $menu->secondFlavor->image ? asset('storage/' . $menu->secondFlavor->image) : null;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item retrieved successfully',
                'data' => $menu
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve menu item',
                'error' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Delete menu without restaurant_id in URL
     */
 public function destroyWithoutRestaurantId(Request $request)
{
    try {
        // Validate the incoming request to ensure 'id' is present
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:menus,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Menu ID is required to delete menu item'
            ], 200);
        }

        // Retrieve the menu by ID from the request
        $menuId = $request->input('id');
        $menu = Menu::with('images')->findOrFail($menuId);

        // Delete associated image files and their DB records
        foreach ($menu->images as $image) {
            // Delete the file from storage
            if (Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }

            // Delete the image record from the database
            $image->delete();
        }

        // Delete the menu item
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully'
        ]);

    } catch (\Illuminate\Validation\ValidationException $ve) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $ve->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete menu item',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get all menus (from all restaurants) - for customers
     * Simple version - just get all menus
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMenus(Request $request)
    {
        try {
            // Simple query - get all active menus with restaurant info
            $menus = Menu::where('status', 'active')
                ->whereHas('restaurant', function($q) {
                    $q->where('status', 'active');
                })
                ->with(['category' => function($query) {
                    $query->select('id', 'name', 'image');
                }, 'secondFlavor' => function($query) {
                    $query->select('id', 'name', 'image');
                }])
                ->orderBy('price', 'asc')
                ->get();

            // Format response - simple structure
            $formattedMenus = $menus->map(function($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'description' => $menu->description,
                    'price' => $menu->price,
                    'vat_price' => $menu->vat_price,
                    'currency' => $menu->currency ?? 'GBP',
                    'image_url' => $menu->image ? asset('storage/' . $menu->image) : null,
                    'is_available' => $menu->is_available ?? true,
                    'spice_level' => $menu->spice_level ?? 0,
                    'preparation_time' => $menu->preparation_time,
                    'calories' => $menu->calories,
                    'tags' => $menu->tags ?? [],
                    'dietary_flags' => $menu->dietary_flags ?? [],
                    'category' => $menu->category ? [
                        'id' => $menu->category->id,
                        'name' => $menu->category->name,
                        'image' => $menu->category->image ? asset('storage/' . $menu->category->image) : null,
                    ] : null,
                    'second_flavor' => $menu->secondFlavor ? [
                        'id' => $menu->secondFlavor->id,
                        'name' => $menu->secondFlavor->name,
                        'image' => $menu->secondFlavor->image ? asset('storage/' . $menu->secondFlavor->image) : null,
                    ] : null,
                    'created_at' => $menu->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'All menus retrieved successfully',
                'data' => [
                    'total_menus' => $menus->count(),
                    'menus' => $formattedMenus
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve menus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all menus by second flavor ID (from all restaurants)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenusBySecondFlavor(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'second_flavor_id' => 'required|integer|exists:second_flavors,id'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            $secondFlavorId = $request->input('second_flavor_id');

            // Get second flavor details
            $secondFlavor = SecondFlavor::find($secondFlavorId);

            if (!$secondFlavor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Second Flavor not found',
                    'error' => "Second Flavor with ID {$secondFlavorId} does not exist"
                ], 404);
            }

            // Get all menus for this second flavor
            $menus = Menu::where('second_flavor_id', $secondFlavorId)
                ->where('status', 'active')
                ->whereHas('restaurant', function($query) {
                    $query->where('status', 'active');
                })
                ->orderBy('price', 'asc')
                ->get()
                ->map(function($menu) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'description' => $menu->description,
                        'price' => $menu->price,
                        'vat_price' => $menu->vat_price,
                        'currency' => $menu->currency ?? 'GBP',
                        'image_url' => $menu->image ? asset('storage/' . $menu->image) : null,
                        'is_available' => $menu->is_available ?? true,
                        'spice_level' => $menu->spice_level ?? 0,
                        'preparation_time' => $menu->preparation_time,
                        'calories' => $menu->calories,
                        'tags' => $menu->tags ?? [],
                        'dietary_flags' => $menu->dietary_flags ?? [],
                        'created_at' => $menu->created_at,
                    ];
                });

            // Count unique restaurants
            $uniqueRestaurants = $menus->pluck('restaurant.id')->unique()->count();

            return response()->json([
                'success' => true,
                'message' => 'Menus retrieved successfully',
                'data' => [
                    'second_flavor' => [
                        'id' => $secondFlavor->id,
                        'name' => $secondFlavor->name,
                        'image_url' => $secondFlavor->image ? asset('storage/' . $secondFlavor->image) : null,
                    ],
                    'total_menus' => $menus->count(),
                    'total_restaurants' => $uniqueRestaurants,
                    'menus' => $menus
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve menus',
                'error' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Get all menus by category ID (from all restaurants)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenusByCategory(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|integer|exists:menu_categories,id'
            ]);

            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                \Log::info('Request data:', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 200);
            }

            $categoryId = $request->input('category_id');

            // Get category details
            $category = MenuCategory::find($categoryId);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                    'error' => "Category with ID {$categoryId} does not exist"
                ], 404);
            }

            // Get all menus for this category with restaurant details
            $menus = Menu::where('category_id', $categoryId)
                ->where('status', 'active')
                ->whereHas('restaurant', function($query) {
                    $query->where('status', 'active');
                })
                ->orderBy('price', 'asc')
                ->get()
                ->map(function($menu) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'description' => $menu->description,
                        'price' => $menu->price,
                        'vat_price' => $menu->vat_price,
                        'currency' => $menu->currency ?? 'GBP',
                        'image_url' => $menu->image ? asset('storage/' . $menu->image) : null,
                        'is_available' => $menu->is_available ?? true,
                        'spice_level' => $menu->spice_level ?? 0,
                        'preparation_time' => $menu->preparation_time,
                        'calories' => $menu->calories,
                        'tags' => $menu->tags ?? [],
                        'dietary_flags' => $menu->dietary_flags ?? [],
                        'created_at' => $menu->created_at,
                    ];
                });

            // Count unique restaurants
            $uniqueRestaurants = $menus->pluck('restaurant.id')->unique()->count();

            return response()->json([
                'success' => true,
                'message' => 'Menus retrieved successfully',
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'description' => $category->description,
                        'image_url' => $category->image ? asset('storage/' . $category->image) : null,
                    ],
                    'total_menus' => $menus->count(),
                    'total_restaurants' => $uniqueRestaurants,
                    'menus' => $menus
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve menus',
                'error' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Get menu item with addons by ID
     */
    public function getMenuWithAddons(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'menu_id' => 'required|integer|exists:menus,id'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $firstError = $errors->first();
                
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 200);
            }

            $menuId = $request->input('menu_id');
            
            // Get menu with addons relationship
            $menu = Menu::with(['addons' => function($query) {
                    $query->where('is_active', 1);
                }])
                ->find($menuId);

            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found'
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Menu item with addons retrieved successfully',
                'data' => $this->formatMenuWithAddons($menu, true)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format menu item with addons
     */
    private function formatMenuWithAddons($menu, $includeRestaurant = false)
    {
        $formattedMenu = [
            'id' => $menu->id,
            'restaurant_id' => $menu->restaurant_id,
            'name' => $menu->name,
            'category_id' => $menu->category_id,
            'price' => $menu->price,
            'vat_price' => $menu->vat_price,
            'currency' => $menu->currency ?? 'GBP',
            'status' => $menu->status == 'active' ? 1 : 0,
            'spice_level' => $menu->spice_level ?? 0,
            'preparation_time' => $menu->preparation_time,
            'calories' => $menu->calories,
            'tags' => is_string($menu->tags) ? $menu->tags : implode(', ', $menu->tags ?? []),
            'description' => $menu->description,
            'ingredients' => $menu->ingredients,
            'allergen' => $menu->allergen,
            'dietary_flags' => is_string($menu->dietary_flags) ? $menu->dietary_flags : implode(', ', $menu->dietary_flags ?? []),
            'is_available' => $menu->is_available ? 1 : 0,
            'image' => $menu->image,
        ];

        // Add addons if relationship is loaded
        if ($menu->relationLoaded('addons')) {
            $formattedMenu['addons'] = $menu->addons->map(function($addon) use ($menu) {
                return [
                    'menu_id' => $menu->id,
                    'addid' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price,
                    'currency' => 'GBP',
                    'is_available' => $addon->is_active ? 1 : 0,
                    'description' => $addon->description ?? ''
                ];
            })->values()->toArray();
        }

        // Add restaurant details if requested
        if ($includeRestaurant && $menu->relationLoaded('restaurant')) {
            $formattedMenu['restaurant'] = [
                'id' => $menu->restaurant->id,
                'business_name' => $menu->restaurant->business_name,
                'legal_name' => $menu->restaurant->legal_name,
                'city' => $menu->restaurant->city,
                'phone' => $menu->restaurant->phone,
            ];
        }

        return $formattedMenu;
    }

}
