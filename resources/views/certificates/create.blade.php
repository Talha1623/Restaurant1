@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-3">
    <div class="bg-white p-4 shadow rounded-lg border border-gray-200">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3 border-b pb-2">
            <h2 class="text-md font-bold flex items-center gap-2 text-gray-800">
                <a href="{{ route('certificates.index', ['restaurant_id' => $restaurantId]) }}" class="hover:opacity-80 transition-opacity">
                    <i class="fas fa-arrow-left" style="color: #000000;"></i>
                </a> Add New Certificate
            </h2>
        </div>

        @if(session('success'))
            <div class="mb-3 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                <h4 class="font-bold">Please fix the following errors:</h4>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('certificates.store') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input type="hidden" name="restaurant_id" value="{{ $restaurantId }}">

        <div class="space-y-3">
            <!-- Certificate Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Certificate Name *</label>
                    <input type="text" name="name" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Certificate Type *</label>
                    <select name="type" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500" required>
                        <option value="">Select Certificate Type</option>
                        @foreach($certificateTypes as $certType)
                            <option value="{{ $certType->name }}">{{ $certType->name }}</option>
                        @endforeach
                    </select>
                    @if($certificateTypes->isEmpty())
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-info-circle"></i> 
                            No certificate types available. 
                            <a href="{{ route('settings.index') }}" class="text-blue-600 hover:underline">Add types in Settings</a>
                        </p>
                    @endif
                </div>
            </div>

                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Issue Date *</label>
                    <input type="date" name="issue_date" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500" required>
                </div>
            </div>

            <!-- Dates & Authority -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Expiry Date</label>
                    <input type="date" name="expiry_date" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Issuing Authority *</label>
                    <select name="issuing_authority" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500" required>
                        <option value="">Select Issuing Authority</option>
                        @foreach($issuingAuthorities as $authority)
                            <option value="{{ $authority->name }}">{{ $authority->name }}</option>
                        @endforeach
                    </select>
                    @if($issuingAuthorities->isEmpty())
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-info-circle"></i> 
                            No issuing authorities available. 
                            <a href="{{ route('settings.index') }}" class="text-blue-600 hover:underline">Add authorities in Settings</a>
                        </p>
                    @endif
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Certificate Number</label>
                    <input type="text" name="certificate_number" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block mb-1 text-xs font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500" placeholder="Enter certificate description..."></textarea>
            </div>

            <!-- File Upload & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Certificate File *</label>
                    <input type="file" name="certificate_file" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500" accept=".pdf,.jpg,.jpeg,.png" required>
                    <p class="text-xs text-gray-500 mt-1">Upload PDF, JPG, or PNG (Max: 5MB)</p>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Status *</label>
                    <select name="status" class="w-full border px-2 py-1.5 text-sm rounded-md focus:ring-1 focus:ring-green-500" required>
                        <option value="inactive" selected>Inactive</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between items-center pt-3 border-t">
            <a href="{{ route('certificates.index', ['restaurant_id' => $restaurantId]) }}" 
               class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded-md hover:bg-gray-600 transition flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="px-4 py-1.5 bg-green-600 text-white text-sm rounded-md shadow hover:bg-green-700 flex items-center gap-1">
                <i class="fas fa-save"></i> Save Certificate
            </button>
        </div>
    </form>
    </div>
</div>
@endsection





