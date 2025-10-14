@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-3 space-y-3">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-md font-bold text-gray-800 flex items-center gap-2">
                <a href="{{ route('restaurants.show', $restaurant->id) }}" class="hover:opacity-80 transition-opacity">
                    <i class="fas fa-arrow-left" style="color: #000000;"></i>
                </a> 
                Certificates - {{ $restaurant->legal_name ?? 'N/A' }}
            </h3>
            
            <!-- Add New Certificate Button -->
            <a href="{{ route('certificates.create', ['restaurant_id' => $restaurant->id]) }}" 
               class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition flex items-center gap-1">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <!-- Total Certificates -->
        <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-certificate text-blue-600 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Total</p>
                    <p class="text-lg font-bold text-gray-900">{{ \App\Models\Certificate::where('restaurant_id', $restaurant->id)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Active Certificates -->
        <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Active</p>
                    <p class="text-lg font-bold text-green-700">{{ \App\Models\Certificate::where('restaurant_id', $restaurant->id)->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Expired Certificates -->
        <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Expired</p>
                    <p class="text-lg font-bold text-red-700">{{ \App\Models\Certificate::where('restaurant_id', $restaurant->id)->where('status', 'expired')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Certificates -->
        <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Pending</p>
                    <p class="text-lg font-bold text-yellow-700">{{ \App\Models\Certificate::where('restaurant_id', $restaurant->id)->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
            
            <!-- Search Input -->
            <div class="flex-1 min-w-64 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-xs"></i>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search certificates, type, authority..." 
                       class="block w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
            </div>
            
            <!-- Filter by Type -->
            <select name="type" class="px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-green-500 focus:border-green-500">
                <option value="">All Types</option>
                <option value="food_hygiene" {{ request('type')=='food_hygiene'?'selected':'' }}>Food Hygiene</option>
                <option value="business_license" {{ request('type')=='business_license'?'selected':'' }}>Business License</option>
                <option value="health_safety" {{ request('type')=='health_safety'?'selected':'' }}>Health & Safety</option>
                <option value="fire_safety" {{ request('type')=='fire_safety'?'selected':'' }}>Fire Safety</option>
                <option value="other" {{ request('type')=='other'?'selected':'' }}>Other</option>
            </select>
            
            <!-- Filter by Status -->
            <select name="status" class="px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-green-500 focus:border-green-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            </select>
            
            <!-- Sort Options -->
            <select name="sort" class="px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-green-500 focus:border-green-500">
                <option value="">Sort By</option>
                <option value="name_asc" {{ request('sort')=='name_asc'?'selected':'' }}>Name: A-Z</option>
                <option value="name_desc" {{ request('sort')=='name_desc'?'selected':'' }}>Name: Z-A</option>
                <option value="issue_date_new" {{ request('sort')=='issue_date_new'?'selected':'' }}>Issue: Newest</option>
                <option value="issue_date_old" {{ request('sort')=='issue_date_old'?'selected':'' }}>Issue: Oldest</option>
                <option value="expiry_date_soon" {{ request('sort')=='expiry_date_soon'?'selected':'' }}>Expiry: Soonest</option>
                <option value="expiry_date_late" {{ request('sort')=='expiry_date_late'?'selected':'' }}>Expiry: Latest</option>
            </select>
            
            <!-- Search Button -->
            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-md hover:bg-green-700 shadow flex items-center gap-1">
                <i class="fas fa-search"></i>
                Search
            </button>
            
            <!-- Clear Button -->
            @if(request()->hasAny(['search', 'type', 'status', 'sort']))
                <a href="{{ route('certificates.index', ['restaurant_id' => $restaurant->id]) }}" 
                   class="px-3 py-1.5 bg-gray-500 text-white text-xs rounded-md hover:bg-gray-600 shadow flex items-center gap-1">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Certificates List -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        @if($certificates->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Certificate</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Issue Date</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($certificates as $certificate)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-certificate text-green-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-xs font-medium text-gray-900">{{ $certificate->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $certificate->issuing_authority }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $certificate->type)) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-900">
                                {{ $certificate->issue_date->format('M d, Y') }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-900">
                                @if($certificate->expiry_date)
                                    {{ $certificate->expiry_date->format('M d, Y') }}
                                @else
                                    <span class="text-gray-400">No expiry</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($certificate->status == 'active')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif($certificate->status == 'expired')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('certificates.show', $certificate) }}"
                                       class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded-md hover:bg-blue-50 transition">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('certificates.edit', $certificate) }}"
                                       class="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded-md hover:bg-indigo-50 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('certificates.destroy', $certificate) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md hover:bg-red-50 transition"
                                                onclick="return confirm('Are you sure you want to delete this certificate?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-3 py-2 border-t border-gray-200 text-xs">
                {{ $certificates->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-certificate text-6xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No certificates</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new certificate.</p>
                <div class="mt-6">
                    <a href="{{ route('certificates.create', ['restaurant_id' => $restaurant->id]) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-plus -ml-1 mr-2"></i>
                        Add New Certificate
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Restaurant Action Buttons -->
    <div class="bg-white rounded-lg shadow p-3 border border-gray-200 mt-4">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
            <a href="{{ route('menus.index', ['restaurant_id' => $restaurant->id]) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-black hover:bg-gray-800">
                <i class="fas fa-utensils"></i> Menu
            </a>
            <a href="{{ route('certificates.index', ['restaurant_id' => $restaurant->id]) }}" 
               class="px-2 py-1.5 text-white text-xs rounded-md transition flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700">
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