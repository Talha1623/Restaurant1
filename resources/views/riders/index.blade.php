@extends('layouts.app')
@section('title', 'Riders')
@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            Riders Dashboard
        </h1>
        <a href="{{ route('riders.create') }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
            + Add New Rider
        </a>
    </div>

 <!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="p-6 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <h3 class="text-sm font-medium opacity-80">Total Riders</h3>
            <p class="text-3xl font-bold">{{ $totalRiders }}</p>
        </div>
        <i class="fas fa-users text-4xl"></i>
    </div>

    <div class="p-6 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <h3 class="text-sm font-medium opacity-80">Active</h3>
            <p class="text-3xl font-bold">{{ $activeRiders }}</p>
        </div>
        <i class="fas fa-check text-4xl"></i>
    </div>

    <div class="p-6 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <h3 class="text-sm font-medium opacity-80">Inactive</h3>
            <p class="text-3xl font-bold">{{ $inactiveRiders }}</p>
        </div>
        <i class="fas fa-times text-4xl"></i>
    </div>

    <!-- ✅ New Book Rider Box -->
    <div class="p-6 bg-gradient-to-r from-purple-500 to-purple-700 text-white rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <h3 class="text-sm font-medium opacity-80">Book Rider</h3>
            <p class="text-3xl font-bold">{{ $bookRiders ?? 0 }}</p>
        </div>
        <i class="fas fa-book text-4xl"></i>
    </div>
</div>

    <!-- Filters -->
    <form method="GET" class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mt-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search riders..." 
               class="w-full md:w-1/3 px-3 py-2 text-sm border rounded-lg focus:ring focus:ring-green-200">

        <div class="flex flex-wrap gap-2">
            <select name="status" class="px-2 py-1 text-sm border rounded-md focus:ring-2 focus:ring-indigo-400">
                <option value="">Status</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
            </select>

            <select name="vehicle_type" class="px-2 py-1 text-sm border rounded-md focus:ring-2 focus:ring-indigo-400">
                <option value="">Vehicle Type</option>
                <option value="bike" {{ request('vehicle_type')=='bike'?'selected':'' }}>Bike</option>
                <option value="car" {{ request('vehicle_type')=='car'?'selected':'' }}>Car</option>
                <option value="scooter" {{ request('vehicle_type')=='scooter'?'selected':'' }}>Scooter</option>
            </select>

            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 shadow">
                Filter
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200 mt-4">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-gray-600 text-sm">
                    <th class="px-4 py-3 border-r text-left">#</th>
                    <th class="px-4 py-3 border-r text-left">Name</th>
                    <th class="px-4 py-3 border-r text-left">Phone</th>
                    <th class="px-4 py-3 border-r text-left">City</th>
                    <th class="px-4 py-3 border-r text-left">Vehicle</th>
                    <th class="px-4 py-3 border-r text-center">Active</th>
                    <th class="px-4 py-3 border-r text-center">Block</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($riders as $rider)
                <tr>
                    <td class="px-4 py-3 border-r">{{ $rider->id }}</td>
                    <td class="px-4 py-3 border-r font-semibold text-indigo-600 flex items-center gap-1">
                        <i class="fas fa-user"></i> {{ $rider->name }}
                    </td>
                    <td class="px-4 py-3 border-r">{{ $rider->phone }}</td>
                    <td class="px-4 py-3 border-r">{{ $rider->city }}</td>
                    <td class="px-4 py-3 border-r">{{ $rider->vehicle_type }} - {{ $rider->vehicle_number }}</td>
                    
                    <!-- Active Toggle Switch -->
                    <td class="px-4 py-3 border-r text-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   class="sr-only peer" 
                                   data-rider-id="{{ $rider->id }}"
                                   {{ $rider->status === 'active' ? 'checked' : '' }}
                                   onchange="toggleStatus({{ $rider->id }}, this.checked)">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </td>

                    <!-- Block Toggle Switch -->
                    <td class="px-4 py-3 border-r text-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   class="sr-only peer" 
                                   data-rider-id="{{ $rider->id }}"
                                   {{ ($rider->blocked ?? false) ? 'checked' : '' }}
                                   onchange="toggleBlock({{ $rider->id }}, this.checked)">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </td>
                    
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <!-- View -->
                            <a href="{{ route('riders.show',$rider) }}" class="inline-flex items-center px-2 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600 shadow">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>

                            <!-- Edit -->
                            <a href="{{ route('riders.edit',$rider) }}" class="inline-flex items-center px-2 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 shadow">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('riders.destroy',$rider) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Delete this rider?')" 
                                    class="inline-flex items-center px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600 shadow">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-500">No riders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">{{ $riders->links() }}</div>
</div>

@push('scripts')
<script>
function toggleStatus(riderId, isActive) {
    const status = isActive ? 'active' : 'inactive';
    
    // Show loading state
    const toggle = document.querySelector(`input[data-rider-id="${riderId}"]`);
    toggle.disabled = true;
    
    // Send AJAX request
    fetch(`/riders/${riderId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Status updated successfully!', 'success');
            
            // Update the toggle to reflect the new status
            toggle.checked = data.status === 'active';
            toggle.disabled = false;
        } else {
            // Revert toggle if failed
            toggle.checked = !isActive;
            toggle.disabled = false;
            showNotification(data.message || 'Failed to update status!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert toggle if failed
        toggle.checked = !isActive;
        toggle.disabled = false;
        showNotification('Error updating status!', 'error');
    });
}

function toggleBlock(riderId, isBlocked) {
    // Show loading state
    const toggle = document.querySelector(`input[data-rider-id="${riderId}"][onchange*="toggleBlock"]`);
    toggle.disabled = true;
    
    // Send AJAX request
    fetch(`/riders/${riderId}/toggle-block`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ blocked: isBlocked })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Block status updated successfully!', 'success');
            
            // Update the toggle to reflect the new blocked status
            toggle.checked = data.blocked;
            toggle.disabled = false;
        } else {
            // Revert toggle if failed
            toggle.checked = !isBlocked;
            toggle.disabled = false;
            showNotification(data.message || 'Failed to update block status!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert toggle if failed
        toggle.checked = !isBlocked;
        toggle.disabled = false;
        showNotification('Error updating block status!', 'error');
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endsection
