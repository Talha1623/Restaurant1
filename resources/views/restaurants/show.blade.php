@extends('layouts.app')

@section('content')
<!-- Full Width Restaurant Banner -->
@if($restaurant->banner)
    @php
        // Fix banner path - convert from restaurants/banners/ to restaurant-banners/
        $bannerPath = $restaurant->banner;
        if (strpos($bannerPath, 'restaurants/banners/') === 0) {
            $bannerPath = str_replace('restaurants/banners/', 'restaurant-banners/', $bannerPath);
        }
    @endphp
    <div class="w-full h-64 bg-gradient-to-r from-gray-800 to-gray-900 relative">
        <img src="{{ url('storage/'.$bannerPath) }}" 
             alt="Restaurant Banner" 
             class="w-full h-full object-cover opacity-90">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
            <div class="max-w-7xl mx-auto flex items-center gap-4">
                <!-- Restaurant Logo -->
                <div class="flex-shrink-0">
                    @if($restaurant->logo)
                        @php
                            // Fix logo path - convert from restaurants/logos/ to restaurant-logos/
                            $logoPath = $restaurant->logo;
                            if (strpos($logoPath, 'restaurants/logos/') === 0) {
                                $logoPath = str_replace('restaurants/logos/', 'restaurant-logos/', $logoPath);
                            }
                        @endphp
                        <img src="{{ asset('storage/'.$logoPath) }}" 
                             alt="Restaurant Logo" 
                             class="h-20 w-20 object-cover rounded-full shadow-lg border-4 border-white">
                    @else
                        <div class="h-20 w-20 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                            <i class="fas fa-store text-white text-2xl"></i>
                        </div>
                    @endif
                </div>
                <!-- Restaurant Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-2">{{ $restaurant->legal_name ?? $restaurant->business_name }}</h1>
                    <div class="flex flex-wrap gap-3 text-sm opacity-90">
                        @if($restaurant->contact_person)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user-tie"></i>
                                {{ $restaurant->contact_person }}
                            </span>
                        @endif
                        @if($restaurant->phone)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-phone"></i>
                                {{ $restaurant->phone }}
                            </span>
                        @endif
                        @if($restaurant->email)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-envelope"></i>
                                {{ $restaurant->email }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="w-full h-64 bg-gradient-to-r from-green-600 to-blue-600 relative">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
            <div class="max-w-7xl mx-auto flex items-center gap-4">
                <!-- Restaurant Logo -->
                <div class="flex-shrink-0">
                    @if($restaurant->logo)
                        @php
                            // Fix logo path - convert from restaurants/logos/ to restaurant-logos/
                            $logoPath = $restaurant->logo;
                            if (strpos($logoPath, 'restaurants/logos/') === 0) {
                                $logoPath = str_replace('restaurants/logos/', 'restaurant-logos/', $logoPath);
                            }
                        @endphp
                        <img src="{{ asset('storage/'.$logoPath) }}" 
                             alt="Restaurant Logo" 
                             class="h-20 w-20 object-cover rounded-full shadow-lg border-4 border-white">
                    @else
                        <div class="h-20 w-20 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                            <i class="fas fa-store text-white text-2xl"></i>
                        </div>
                    @endif
                </div>
                <!-- Restaurant Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-2">{{ $restaurant->legal_name ?? $restaurant->business_name }}</h1>
                    <div class="flex flex-wrap gap-3 text-sm opacity-90">
                        @if($restaurant->contact_person)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user-tie"></i>
                                {{ $restaurant->contact_person }}
                            </span>
                        @endif
                        @if($restaurant->phone)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-phone"></i>
                                {{ $restaurant->phone }}
                            </span>
                        @endif
                        @if($restaurant->email)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-envelope"></i>
                                {{ $restaurant->email }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="max-w-7xl mx-auto px-4 py-3 space-y-3">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-md font-bold text-gray-800 flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-green-600 transition-colors">
                    <i class="fas fa-arrow-left" style="color: #000000;"></i>
                </a>
                Restaurant Details
            </h3>
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('restaurants.edit', $restaurant) }}" 
                   class="px-2 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 transition flex items-center gap-1">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <!-- Restaurant Details Grid - Compact Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Address Information -->
        <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
            <h4 class="text-xs font-semibold text-gray-800 mb-2 flex items-center gap-1">
                <i class="fas fa-map-marker-alt text-red-500"></i>
                Address & Business Info
            </h4>
            <div class="space-y-1 text-xs">
                @if($restaurant->legal_name)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Legal Name:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->legal_name }}</span>
                </div>
                @endif
                @if($restaurant->business_name)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Business Name:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->business_name }}</span>
                </div>
                @endif
                @if($restaurant->address_line1)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Address:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->address_line1 }}</span>
                </div>
                @endif
                @if($restaurant->city)
                    <div class="flex justify-between">
                        <span class="text-gray-600">City:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->city }}</span>
                </div>
                @endif
                @if($restaurant->postcode)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Postcode:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->postcode }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Business Hours -->
        <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
            <h4 class="text-xs font-semibold text-gray-800 mb-2 flex items-center gap-1">
                <i class="fas fa-clock text-orange-500"></i>
                Hours
            </h4>
            <div class="space-y-1 text-xs">
                @if($restaurant->opening_time)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Opening:</span>
                        <span class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($restaurant->opening_time)->format('g:i A') }}</span>
                    </div>
                @endif
                @if($restaurant->closing_time)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Closing:</span>
                        <span class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($restaurant->closing_time)->format('g:i A') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Cuisine & Delivery -->
        <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
            <h4 class="text-xs font-semibold text-gray-800 mb-2 flex items-center gap-1">
                <i class="fas fa-utensils text-pink-500"></i>
                Cuisine & Delivery
            </h4>
            <div class="space-y-1 text-xs">
                @if($restaurant->cuisine_tags)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cuisine:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->cuisine_tags }}</span>
                    </div>
                @endif
                @if($restaurant->delivery_zone)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Zone:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->delivery_zone }} km</span>
                    </div>
                @endif
                @if($restaurant->delivery_postcode)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery Postcode:</span>
                        <span class="text-gray-800 font-medium">{{ $restaurant->delivery_postcode }}</span>
                    </div>
                @endif
                @if($restaurant->min_order)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Min Order:</span>
                        <span class="text-gray-800 font-medium">£{{ number_format($restaurant->min_order, 2) }}</span>
                    </div>
                @endif
                @if($restaurant->status)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $restaurant->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($restaurant->status) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
            <a href="{{ route('menus.index', ['restaurant_id' => $restaurant->id]) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-black hover:bg-gray-800">
                <i class="fas fa-utensils"></i> Menu
            </a>
            <a href="{{ route('certificates.index', ['restaurant_id' => $restaurant->id]) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-black hover:bg-gray-800">
                <i class="fas fa-certificate"></i> Certificates
            </a>
            <a href="{{ route('restaurants.orders', $restaurant) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-black hover:bg-gray-800">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="{{ route('restaurants.analytics', $restaurant) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-black hover:bg-gray-800">
                <i class="fas fa-chart-bar"></i> Analytics
            </a>
            <a href="{{ route('restaurants.reviews', $restaurant) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-black hover:bg-gray-800">
                <i class="fas fa-star"></i> Reviews
            </a>
            <a href="{{ route('restaurants.settings', $restaurant) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-black hover:bg-gray-800">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>
</div>
@endsection