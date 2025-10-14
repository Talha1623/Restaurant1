<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\CertificateType;
use App\Models\IssuingAuthority;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $restaurantId = $request->get('restaurant_id');
        $restaurant = \App\Models\Restaurant::findOrFail($restaurantId);
        
        $query = Certificate::where('restaurant_id', $restaurantId);
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('type', 'like', "%{$searchTerm}%")
                  ->orWhere('issuing_authority', 'like', "%{$searchTerm}%")
                  ->orWhere('certificate_number', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Sort functionality
        if ($request->filled('sort')) {
            $sortBy = $request->sort;
            switch ($sortBy) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'issue_date_new':
                    $query->orderBy('issue_date', 'desc');
                    break;
                case 'issue_date_old':
                    $query->orderBy('issue_date', 'asc');
                    break;
                case 'expiry_date_soon':
                    $query->orderBy('expiry_date', 'asc');
                    break;
                case 'expiry_date_late':
                    $query->orderBy('expiry_date', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // Default sort by newest
            $query->orderBy('created_at', 'desc');
        }
        
        $certificates = $query->paginate(10)->appends($request->query());
            
        return view('certificates.index', compact('certificates', 'restaurant'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $restaurantId = $request->get('restaurant_id');
        $certificateTypes = CertificateType::where('is_active', true)->orderBy('name')->get();
        $issuingAuthorities = IssuingAuthority::where('is_active', true)->orderBy('name')->get();
        return view('certificates.create', compact('restaurantId', 'certificateTypes', 'issuingAuthorities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the request data
        \Log::info('Certificate store request data:', $request->all());
        
        try {
            $request->validate([
                'restaurant_id' => 'required|exists:restaurants,id',
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'issue_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:issue_date',
                'issuing_authority' => 'required|string|max:255',
                'certificate_number' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'status' => 'required|in:active,inactive,expired,pending',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Certificate validation failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Certificate creation error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while creating the certificate: ' . $e->getMessage())->withInput();
        }

        $data = $request->except(['certificate_file']);

        // Handle file upload
        if ($request->hasFile('certificate_file')) {
            $data['certificate_file'] = $request->file('certificate_file')->store('certificates', 'public');
        }

        try {
            $certificate = Certificate::create($data);
            \Log::info('Certificate created successfully:', ['id' => $certificate->id, 'name' => $certificate->name]);
            return redirect()->route('certificates.index', ['restaurant_id' => $request->restaurant_id])
                ->with('success', 'Certificate created successfully!');
        } catch (\Exception $e) {
            \Log::error('Database error during certificate creation:', ['message' => $e->getMessage(), 'data' => $data, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $certificate = Certificate::with('restaurant')->findOrFail($id);
        return view('certificates.show', compact('certificate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificateTypes = CertificateType::where('is_active', true)->orderBy('name')->get();
        $issuingAuthorities = IssuingAuthority::where('is_active', true)->orderBy('name')->get();
        
        return view('certificates.edit', compact('certificate', 'certificateTypes', 'issuingAuthorities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $certificate = Certificate::findOrFail($id);
        
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'issue_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:issue_date',
                'issuing_authority' => 'required|string|max:255',
                'certificate_number' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'status' => 'required|in:active,inactive,expired,pending',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Certificate validation failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Certificate update error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while updating the certificate: ' . $e->getMessage())->withInput();
        }

        $data = $request->except(['certificate_file']);

        // Handle file upload
        if ($request->hasFile('certificate_file')) {
            // Delete old file if exists
            if ($certificate->certificate_file) {
                \Storage::disk('public')->delete($certificate->certificate_file);
            }
            $data['certificate_file'] = $request->file('certificate_file')->store('certificates', 'public');
        }

        try {
            $certificate->update($data);
            \Log::info('Certificate updated successfully:', ['id' => $certificate->id, 'name' => $certificate->name]);
            return redirect()->route('certificates.show', $certificate->id)
                ->with('success', 'Certificate updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Database error during certificate update:', ['message' => $e->getMessage(), 'data' => $data, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $certificate = Certificate::findOrFail($id);
        $restaurantId = $certificate->restaurant_id;
        
        // Delete file if exists
        if ($certificate->certificate_file) {
            \Storage::disk('public')->delete($certificate->certificate_file);
        }
        
        $certificate->delete();
        
        return redirect()->route('certificates.index', ['restaurant_id' => $restaurantId])
            ->with('success', 'Certificate deleted successfully.');
    }
}
