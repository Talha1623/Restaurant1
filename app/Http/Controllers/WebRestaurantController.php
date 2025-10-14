<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebRestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('legal_name', 'like', '%' . $request->search . '%')
                  ->orWhere('business_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by block status
        if ($request->filled('block_status')) {
            $query->where('blocked', $request->block_status === 'blocked');
        }

        $restaurants = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats for the view
        $totalRestaurants = Restaurant::count();
        
        // Check if status column exists
        try {
            $activeRestaurants = Restaurant::where('status', 'active')->count();
            $inactiveRestaurants = Restaurant::where('status', 'inactive')->count();
        } catch (\Exception $e) {
            $activeRestaurants = 0;
            $inactiveRestaurants = 0;
        }
        
        // Check if blocked column exists
        try {
            $blockedRestaurants = Restaurant::where('blocked', true)->count();
        } catch (\Exception $e) {
            $blockedRestaurants = 0;
        }
        
        // Check if cuisine_tags column exists
        try {
            $cuisines = Restaurant::distinct()->pluck('cuisine_tags')->filter()->count();
        } catch (\Exception $e) {
            $cuisines = 0;
        }

        return view('restaurants.index', compact('restaurants', 'totalRestaurants', 'activeRestaurants', 'inactiveRestaurants', 'blockedRestaurants', 'cuisines'));
    }

    public function create()
    {
        return view('restaurants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'legal_name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email' => 'required|email|unique:restaurants,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'cuisine_type' => 'required|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('restaurants/logos', 'public');
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('restaurants/covers', 'public');
        }

        $data['status'] = 'active';
        $data['block_status'] = 'unblocked';

        Restaurant::create($data);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant created successfully!');
    }

    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', compact('restaurant'));
    }

    public function edit(Restaurant $restaurant)
    {
        return view('restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'legal_name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email' => 'required|email|unique:restaurants,email,' . $restaurant->id,
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string',
            'city' => 'required|string',
            'postcode' => 'required|string',
            'contact_person' => 'required|string',
            'opening_time' => 'required|string',
            'closing_time' => 'required|string',
            'min_order' => 'required|numeric',
            'status' => 'required|string',
            'cuisine_tags' => 'nullable|string',
            'delivery_zone' => 'nullable|string',
            'delivery_postcode' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $request->file('logo')->store('restaurant-logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner
            if ($restaurant->banner) {
                Storage::disk('public')->delete($restaurant->banner);
            }
            $data['banner'] = $request->file('banner')->store('restaurant-banners', 'public');
        }

        $restaurant->update($data);

        return redirect()->route('restaurants.show', $restaurant)->with('success', 'Restaurant updated successfully!');
    }

    public function destroy(Restaurant $restaurant)
    {
        // Delete associated files
        if ($restaurant->logo) {
            Storage::disk('public')->delete($restaurant->logo);
        }
        if ($restaurant->cover_image) {
            Storage::disk('public')->delete($restaurant->cover_image);
        }

        $restaurant->delete();

        return redirect()->route('restaurants.index')->with('success', 'Restaurant deleted successfully!');
    }

    public function toggleStatus(Restaurant $restaurant)
    {
        try {
            $restaurant->update([
                'status' => $restaurant->status === 'active' ? 'inactive' : 'active'
            ]);

            // Check if it's an AJAX request
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Restaurant status updated successfully!',
                    'status' => $restaurant->status
                ]);
            }

            return back()->with('success', 'Restaurant status updated successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to update status!');
        }
    }

    public function toggleBlock(Restaurant $restaurant)
    {
        try {
            $restaurant->update([
                'blocked' => !$restaurant->blocked
            ]);

            // Check if it's an AJAX request
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Restaurant block status updated successfully!',
                    'blocked' => $restaurant->blocked
                ]);
            }

            return back()->with('success', 'Restaurant block status updated successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update block status: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to update block status!');
        }
    }

    public function menu(Restaurant $restaurant)
    {
        // Get menus grouped by category
        $menus = \App\Models\Menu::where('restaurant_id', $restaurant->id)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group menus by category
        $menuCategories = $menus->groupBy(function($menu) {
            return $menu->category ? $menu->category->name : 'Other';
        })->map(function($dishes, $categoryName) {
            return $dishes->map(function($dish) {
                return [
                    'id' => $dish->id,
                    'name' => $dish->name,
                    'description' => $dish->description,
                    'price' => $dish->currency . ' ' . number_format($dish->price, 2),
                    'image' => $dish->image ? asset('storage/' . $dish->image) : null,
                    'spice_level' => $dish->spice_level,
                    'preparation_time' => $dish->preparation_time,
                    'calories' => $dish->calories,
                    'status' => $dish->status,
                ];
            });
        });

        return view('restaurants.menu', compact('restaurant', 'menuCategories'));
    }

    public function orders(Restaurant $restaurant)
    {
        return view('restaurants.orders', compact('restaurant'));
    }

    public function reviews(Restaurant $restaurant)
    {
        // Get all reviews for this restaurant
        $reviews = \App\Models\Review::where('restaurant_id', $restaurant->id)
            ->where('status', 'active')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;
        
        // Calculate rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = $reviews->where('rating', $i)->count();
        }

        return view('restaurants.reviews', compact(
            'restaurant',
            'reviews',
            'totalReviews',
            'averageRating',
            'ratingDistribution'
        ));
    }

    public function analytics(Restaurant $restaurant)
    {
        return view('restaurants.analytics', compact('restaurant'));
    }

    public function settings(Restaurant $restaurant)
    {
        return view('restaurants.settings', compact('restaurant'));
    }
}
