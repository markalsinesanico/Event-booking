<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function getDashboardData()
    {
        $totalBookings = Booking::count();
        $totalUsers = User::where('role', 'user')->count();

        return response()->json([
            'status' => 'success',
            'summary' => [
                'totalBookings' => $totalBookings,
                'totalUsers' => $totalUsers
            ]
        ]);
    }

    public function getEvents()
    {
        $events = Booking::with(['user', 'venue'])
            ->orderBy('start_date', 'asc')
            ->where('start_date', '>=', now())
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'venue' => $booking->venue ? $booking->venue->name : 'N/A',
                    'fullName' => $booking->user ? $booking->user->firstname . ' ' . $booking->user->lastname : 'N/A',
                    'email' => $booking->user ? $booking->user->email : 'N/A',
                    'phone' => $booking->user ? $booking->user->phone : 'N/A',
                    'category' => $booking->category,
                    'startDate' => $booking->start_date,
                    'endDate' => $booking->end_date,
                    'status' => $booking->status
                ];
            });

        return response()->json([
            'status' => 'success',
            'events' => $events
        ]);
    }

    public function approveEvent($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'approved';
        $booking->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Event approved successfully'
        ]);
    }

    public function deleteEvent($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully'
        ]);
    }
} 