<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Restaurant;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class ReviewController extends Controller
{
    /**
     * Create a new review
     * POST /api/reviews
     */
    public function store(Request $request)
    {
        try {
            // Debug: Log all request data
            \Log::info('Review Request Debug', [
                'all_data' => $request->all(),
                'input_data' => $request->input(),
                'customer_id' => $request->input('customer_id'),
                'restaurant_id' => $request->input('restaurant_id'),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);

            // Validation (temporarily relaxed for testing)
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|integer',
                'restaurant_id' => 'required|integer', 
                'rating' => 'required|integer|min:1|max:5',
                'description' => 'required|string|min:10|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'debug_data' => $request->all()
                ], 422);
            }

            // Check if customer exists
            $customer = Customer::find($request->customer_id);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                    'debug_customer_id' => $request->customer_id
                ], 200);
            }

            // Check if restaurant exists
            $restaurant = Restaurant::find($request->restaurant_id);
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found',
                    'debug_restaurant_id' => $request->restaurant_id
                ], 200);
            }

            // Check if customer already reviewed this restaurant
            $existingReview = Review::where('customer_id', $request->customer_id)
                ->where('restaurant_id', $request->restaurant_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this restaurant. You can update your existing review instead.',
                    'existing_review_id' => $existingReview->id
                ], 409);
            }

            // Create review
            $review = Review::create([
                'customer_id' => $request->customer_id,
                'restaurant_id' => $request->restaurant_id,
                'rating' => $request->rating,
                'description' => $request->description,
                'status' => 'active'
            ]);

            // Load relationships
            $review->load('customer', 'restaurant');

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'data' => [
                    'review' => [
                        'id' => $review->id,
                        'customer_id' => $review->customer_id,
                        'customer_name' => $review->customer->first_name . ' ' . $review->customer->last_name,
                        'restaurant_id' => $review->restaurant_id,
                        'restaurant_name' => $review->restaurant->business_name,
                        'rating' => $review->rating,
                        'description' => $review->description,
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                        'created_at_human' => $review->created_at->diffForHumans()
                    ]
                ]
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => $e->getMessage(),
                'debug_data' => $request->all()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create review',
                'error' => $e->getMessage(),
                'debug_data' => $request->all()
            ], 500);
        }
    }

    /**
     * Get all reviews for a specific restaurant
     * GET /api/restaurants/{restaurant_id}/reviews
     */
    public function getRestaurantReviews($restaurantId)
    {
        try {
            // Check if restaurant exists
            $restaurant = Restaurant::find($restaurantId);

            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found'
                ], 200);
            }

            // Get reviews with customer information
            $reviews = Review::where('restaurant_id', $restaurantId)
                ->where('status', 'active')
                ->with('customer')
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate statistics
            $totalReviews = $reviews->count();
            $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

            // Format reviews
            $formattedReviews = $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'customer_id' => $review->customer_id,
                    'customer_name' => $review->customer->first_name . ' ' . $review->customer->last_name,
                    'rating' => $review->rating,
                    'description' => $review->description,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $review->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->id,
                        'name' => $restaurant->business_name,
                        'average_rating' => $averageRating,
                        'total_reviews' => $totalReviews
                    ],
                    'reviews' => $formattedReviews
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific review
     * GET /api/reviews/{id}
     */
    public function show($id)
    {
        try {
            $review = Review::with('customer', 'restaurant')->find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'review' => [
                        'id' => $review->id,
                        'customer_id' => $review->customer_id,
                        'customer_name' => $review->customer->first_name . ' ' . $review->customer->last_name,
                        'restaurant_id' => $review->restaurant_id,
                        'restaurant_name' => $review->restaurant->business_name,
                        'rating' => $review->rating,
                        'description' => $review->description,
                        'status' => $review->status,
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $review->updated_at->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a review
     * PUT /api/reviews/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $review = Review::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 200);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'description' => 'sometimes|required|string|min:10|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update review
            $review->update($request->only(['rating', 'description']));
            $review->load('customer', 'restaurant');

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully',
                'data' => [
                    'review' => [
                        'id' => $review->id,
                        'customer_id' => $review->customer_id,
                        'customer_name' => $review->customer->first_name . ' ' . $review->customer->last_name,
                        'restaurant_id' => $review->restaurant_id,
                        'restaurant_name' => $review->restaurant->business_name,
                        'rating' => $review->rating,
                        'description' => $review->description,
                        'updated_at' => $review->updated_at->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a review
     * DELETE /api/reviews/{id}
     */
    public function destroy($id)
    {
        try {
            $review = Review::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 200);
            }

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer's reviews
     * POST /api/reviews/my-reviews
     */
    public function getCustomerReviews(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get customer reviews
            $reviews = Review::where('customer_id', $request->customer_id)
                ->where('status', 'active')
                ->with('restaurant')
                ->orderBy('created_at', 'desc')
                ->get();

            // Format reviews
            $formattedReviews = $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'restaurant_id' => $review->restaurant_id,
                    'restaurant_name' => $review->restaurant->business_name,
                    'rating' => $review->rating,
                    'description' => $review->description,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $review->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_reviews' => $reviews->count(),
                    'reviews' => $formattedReviews
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customer reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}