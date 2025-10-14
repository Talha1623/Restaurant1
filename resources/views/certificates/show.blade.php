@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-3">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-3 border border-gray-200 mb-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('certificates.index', ['restaurant_id' => $certificate->restaurant_id]) }}" 
                   class="hover:opacity-80 transition-opacity">
                    <i class="fas fa-arrow-left" style="color: #000000;"></i>
                </a>
                <div>
                    <h1 class="text-md font-bold text-gray-900">{{ $certificate->name }}</h1>
                    <p class="text-xs text-gray-600">{{ $certificate->restaurant->legal_name }} - {{ $certificate->type }}</p>
                </div>
            </div>
            <a href="{{ route('certificates.edit', $certificate->id) }}" 
               class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition flex items-center gap-1">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <!-- Certificate Details -->
            <div class="lg:col-span-2 space-y-3">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-1">
                        <i class="fas fa-info-circle text-blue-500 text-xs"></i>
                        Certificate Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Certificate Name</label>
                            <p class="text-sm text-gray-800 font-medium">{{ $certificate->name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                            <p class="text-sm text-gray-800">{{ $certificate->type }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Certificate Number</label>
                            <p class="text-sm text-gray-800">{{ $certificate->certificate_number ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $certificate->status == 'active' ? 'bg-green-100 text-green-800' : ($certificate->status == 'expired' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($certificate->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-1">
                        <i class="fas fa-calendar text-green-500 text-xs"></i>
                        Important Dates
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Issue Date</label>
                            <p class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($certificate->issue_date)->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date</label>
                            <p class="text-sm text-gray-800">
                                @if($certificate->expiry_date)
                                    {{ \Carbon\Carbon::parse($certificate->expiry_date)->format('M d, Y') }}
                                    @if(\Carbon\Carbon::parse($certificate->expiry_date)->isPast())
                                        <span class="text-red-600 text-xs ml-1">(Expired)</span>
                                    @elseif(\Carbon\Carbon::parse($certificate->expiry_date)->diffInDays() <= 30)
                                        <span class="text-yellow-600 text-xs ml-1">(Expires Soon)</span>
                                    @endif
                                @else
                                    No expiry date
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Authority & Description -->
                <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-1">
                        <i class="fas fa-building text-purple-500 text-xs"></i>
                        Issuing Authority
                    </h3>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Issuing Authority</label>
                            <p class="text-sm text-gray-800">{{ $certificate->issuing_authority }}</p>
                        </div>
                        @if($certificate->description)
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                <p class="text-sm text-gray-800">{{ $certificate->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Certificate File -->
            <div class="space-y-3">
                <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-1">
                        <i class="fas fa-file text-orange-500 text-xs"></i>
                        Certificate File
                    </h3>
                    
                    @if($certificate->certificate_file)
                        <div class="text-center">
                            @if(in_array(pathinfo($certificate->certificate_file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ asset('storage/' . $certificate->certificate_file) }}" 
                                     alt="Certificate" 
                                     class="w-full h-48 object-cover rounded-lg mb-2">
                            @else
                                <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-2">
                                    <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                                </div>
                            @endif
                            
                            <a href="{{ asset('storage/' . $certificate->certificate_file) }}" 
                               target="_blank" 
                               class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition">
                                <i class="fas fa-download mr-1"></i>
                                Download
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-file text-3xl mb-2"></i>
                            <p class="text-xs">No file uploaded</p>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-3 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-1">
                        <i class="fas fa-bolt text-yellow-500 text-xs"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('certificates.edit', $certificate->id) }}" 
                           class="w-full flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('certificates.destroy', $certificate->id) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this certificate?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center justify-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-1"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
