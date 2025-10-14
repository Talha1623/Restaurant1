<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RiderAuthController extends Controller
{
    /**
     * Rider Registration (Signup)
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:riders,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:6|confirmed',
                'dob' => 'nullable|date',
                'vehicle_type' => 'required|in:Bike,Car,Van,Scooter',
                'vehicle_number' => 'nullable|string|max:50',
                'license_number' => 'nullable|string|max:50',
                'city' => 'nullable|string|max:100',
                'postcode' => 'nullable|string|max:20',
                'house_number' => 'nullable|string|max:50',
                'street' => 'nullable|string|max:255',
                'building' => 'nullable|string|max:255',
                'ni_number' => 'nullable|string|max:50',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except(['password', 'password_confirmation', 'photo']);
            
            // Hash password
            $data['password'] = Hash::make($request->password);
            
            // Set default status
            $data['status'] = 'inactive'; // Admin will activate after verification
            $data['joining_date'] = now();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('riders/photos', 'public');
            }

            $rider = Rider::create($data);

            // Create token for the rider
            $token = $rider->createToken('rider-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Rider registered successfully! Your account is pending admin approval.',
                'data' => [
                    'rider' => $this->formatRider($rider),
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rider Login
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rider = Rider::where('email', $request->email)->first();

            if (!$rider || !Hash::check($request->password, $rider->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            // Check if rider is active
            if ($rider->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is inactive. Please contact admin.'
                ], 403);
            }

            // Create token
            $token = $rider->createToken('rider-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'rider' => $this->formatRider($rider),
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rider Logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Rider Profile
     */
    public function profile(Request $request)
    {
        try {
            $rider = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'rider' => $this->formatRider($rider)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Rider Profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $rider = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:riders,email,' . $rider->id,
                'phone' => 'sometimes|required|string|max:20',
                'dob' => 'nullable|date',
                'city' => 'nullable|string|max:100',
                'postcode' => 'nullable|string|max:20',
                'house_number' => 'nullable|string|max:50',
                'street' => 'nullable|string|max:255',
                'building' => 'nullable|string|max:255',
                'vehicle_number' => 'nullable|string|max:50',
                'license_number' => 'nullable|string|max:50',
                'bank_sort_code' => 'nullable|string|max:10',
                'bank_account_number' => 'nullable|string|max:20',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except(['photo', 'password', 'status', 'vehicle_type']);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($rider->photo) {
                    Storage::disk('public')->delete($rider->photo);
                }
                $data['photo'] = $request->file('photo')->store('riders/photos', 'public');
            }

            $rider->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'rider' => $this->formatRider($rider->fresh())
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rider = $request->user();

            // Verify current password
            if (!Hash::check($request->current_password, $rider->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 401);
            }

            // Update password
            $rider->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload Documents
     */
    public function uploadDocuments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'license_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'license_back_image' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'insurance_doc' => 'nullable|mimes:pdf,jpeg,png,jpg|max:4096',
                'mot_doc' => 'nullable|mimes:pdf,jpeg,png,jpg|max:4096',
                'right_to_work_doc' => 'nullable|mimes:pdf,jpeg,png,jpg|max:4096',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rider = $request->user();
            $data = [];

            // Handle license front image
            if ($request->hasFile('license_front_image')) {
                if ($rider->license_front_image) {
                    Storage::disk('public')->delete($rider->license_front_image);
                }
                $data['license_front_image'] = $request->file('license_front_image')->store('riders/license', 'public');
            }

            // Handle license back image
            if ($request->hasFile('license_back_image')) {
                if ($rider->license_back_image) {
                    Storage::disk('public')->delete($rider->license_back_image);
                }
                $data['license_back_image'] = $request->file('license_back_image')->store('riders/license', 'public');
            }

            // Handle insurance document
            if ($request->hasFile('insurance_doc')) {
                if ($rider->insurance_doc) {
                    Storage::disk('public')->delete($rider->insurance_doc);
                }
                $data['insurance_doc'] = $request->file('insurance_doc')->store('riders/docs', 'public');
            }

            // Handle MOT document
            if ($request->hasFile('mot_doc')) {
                if ($rider->mot_doc) {
                    Storage::disk('public')->delete($rider->mot_doc);
                }
                $data['mot_doc'] = $request->file('mot_doc')->store('riders/docs', 'public');
            }

            // Handle right to work document
            if ($request->hasFile('right_to_work_doc')) {
                if ($rider->right_to_work_doc) {
                    Storage::disk('public')->delete($rider->right_to_work_doc);
                }
                $data['right_to_work_doc'] = $request->file('right_to_work_doc')->store('riders/docs', 'public');
            }

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No documents provided'
                ], 422);
            }

            $rider->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully',
                'data' => [
                    'rider' => $this->formatRider($rider->fresh())
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot Password - Send Reset Link
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:riders,email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Here you would send password reset email
            // For now, just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format rider data for API response
     */
    private function formatRider($rider)
    {
        return [
            'id' => $rider->id,
            'name' => $rider->name,
            'email' => $rider->email,
            'phone' => $rider->phone,
            'dob' => $rider->dob,
            'ni_number' => $rider->ni_number,
            'city' => $rider->city,
            'house_number' => $rider->house_number,
            'street' => $rider->street,
            'building' => $rider->building,
            'postcode' => $rider->postcode,
            'vehicle_type' => $rider->vehicle_type,
            'vehicle_number' => $rider->vehicle_number,
            'license_number' => $rider->license_number,
            'status' => $rider->status,
            'joining_date' => $rider->joining_date,
            'bank_sort_code' => $rider->bank_sort_code,
            'bank_account_number' => $rider->bank_account_number,
            'photo' => $rider->photo ? asset('storage/' . $rider->photo) : null,
            'license_front_image' => $rider->license_front_image ? asset('storage/' . $rider->license_front_image) : null,
            'license_back_image' => $rider->license_back_image ? asset('storage/' . $rider->license_back_image) : null,
            'insurance_doc' => $rider->insurance_doc ? asset('storage/' . $rider->insurance_doc) : null,
            'mot_doc' => $rider->mot_doc ? asset('storage/' . $rider->mot_doc) : null,
            'right_to_work_doc' => $rider->right_to_work_doc ? asset('storage/' . $rider->right_to_work_doc) : null,
            'features' => $rider->features,
            'created_at' => $rider->created_at,
            'updated_at' => $rider->updated_at,
        ];
    }
}

