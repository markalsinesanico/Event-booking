<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'category' => 'required|string',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
                'venueId' => 'required|exists:venues,id'
            ]);

            // Convert dates to proper format
            $startDate = Carbon::parse($request->startDate)->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($request->endDate)->format('Y-m-d H:i:s');

            // Check if venue is available for these dates
            $existingBooking = Booking::where('venue_id', $request->venueId)
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate]);
                })
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The venue is already booked for these dates'
                ], 422);
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'venue_id' => $request->venueId,
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'category' => $request->category,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully',
                'booking' => $booking
            ]);
        } catch (\Exception $e) {
            \Log::error('Booking creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserBookings($userId)
    {
        try {
            // Ensure user can only access their own bookings
            if (auth()->id() != $userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $bookings = Booking::where('user_id', $userId)
                ->with('venue') // Include venue details if needed
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAdminBookings()
    {
        try {
            // Check if user is admin or administrator
            if (!in_array(auth()->user()->role, ['admin', 'administrator'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Add with() to eager load the venue relationship and its creator
            $bookings = Booking::with(['venue', 'venue.creator', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Log the bookings for debugging
            \Log::info('Admin bookings retrieved:', ['count' => $bookings->count()]);

            return response()->json([
                'status' => 'success',
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in getAdminBookings:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBookingStatus(Request $request, $bookingId)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,rejected'
            ]);

            \Log::info('Updating booking status:', [
                'booking_id' => $bookingId,
                'new_status' => $request->status
            ]);

            $booking = Booking::with('venue')->findOrFail($bookingId);
            
            // Log the current state
            \Log::info('Current booking and venue state:', [
                'booking' => $booking->toArray(),
                'venue' => $booking->venue ? $booking->venue->toArray() : null
            ]);

            $booking->status = $request->status;

            // If booking is approved, update venue status to occupied
            if ($request->status === 'approved') {
                if ($booking->venue) {
                    \Log::info('Updating venue status to occupied');
                    
                    $venue = $booking->venue;
                    $venue->status = 'occupied';
                    $venue->save();
                    
                    \Log::info('Venue status after update:', [
                        'venue_id' => $venue->id,
                        'new_status' => $venue->status
                    ]);
                } else {
                    \Log::warning('No venue found for booking:', ['booking_id' => $bookingId]);
                }
            } elseif ($request->status === 'rejected' && $booking->venue) {
                // If booking is rejected, ensure venue is available
                $venue = $booking->venue;
                $venue->status = 'available';
                $venue->save();
                
                \Log::info('Venue status set to available after rejection');
            }

            $booking->save();

            // Verify the changes
            $updatedBooking = Booking::with('venue')->find($bookingId);
            \Log::info('Final state after update:', [
                'booking' => $updatedBooking->toArray(),
                'venue' => $updatedBooking->venue ? $updatedBooking->venue->toArray() : null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking status updated successfully',
                'booking' => $updatedBooking
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating booking status:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update booking status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteBooking($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $venueId = $booking->venue_id; // Get the venue ID before deleting the booking
            
            $booking->delete();

            // Update venue status to available
            $venueController = new VenueController();
            $venueController->updateVenueStatus($venueId, 'available');

            return response()->json([
                'status' => 'success',
                'message' => 'Booking deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete booking'
            ], 500);
        }
    }
} 