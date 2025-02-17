<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    public function index()
    {
        // Get the authenticated user and their role
        $user = auth()->user();
        \Log::info('Fetching venues for user:', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);
        
        // Query builder for venues
        $query = Venue::with('creator:id,firstname,lastname');
        
        // If user is administrator, only show their venues
        if ($user->role === 'administrator') {
            $query->where('created_by', $user->id);
        }
        
        $venues = $query->get()->map(function ($venue) {
            \Log::info('Mapping venue:', [
                'venue_id' => $venue->id,
                'created_by' => $venue->created_by,
                'image_path' => $venue->image
            ]);
            
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'description' => $venue->description,
                'status' => $venue->status,
                'image' => $venue->image ? asset('storage/' . $venue->image) : null,
                'creator_name' => $venue->creator ? $venue->creator->firstname . ' ' . $venue->creator->lastname : 'Unknown',
                'created_by' => $venue->created_by
            ];
        });
        
        return response()->json([
            'status' => 'success',
            'venues' => $venues
        ]);
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Venue store request:', $request->all());

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:available,occupied,maintenance',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Move the file directly to the public storage
                $image->move(public_path('storage/venues'), $imageName);
                
                // Store the path in database
                $imageName = 'venues/' . $imageName;

                \Log::info('Image saved:', [
                    'original_name' => $image->getClientOriginalName(),
                    'saved_name' => $imageName,
                    'path' => public_path('storage/venues/' . $imageName)
                ]);
            }

            $venue = Venue::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'image' => $imageName,
                'created_by' => auth()->id()
            ]);

            // Return the venue with full image URL
            return response()->json([
                'status' => 'success',
                'message' => 'Venue created successfully',
                'venue' => [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'description' => $venue->description,
                    'status' => $venue->status,
                    'image' => $venue->image ? asset('storage/' . $venue->image) : null,
                    'created_by' => $venue->created_by,
                    'creator_name' => $venue->creator ? $venue->creator->firstname . ' ' . $venue->creator->lastname : 'Unknown'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Venue creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create venue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $venue = Venue::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:available,occupied,maintenance',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($request->hasFile('image')) {
                // Delete old image
                if ($venue->image) {
                    $oldImagePath = public_path('storage/' . $venue->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Store new image
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('storage/venues'), $imageName);
                $venue->image = 'venues/' . $imageName;
            }

            $venue->name = $request->name;
            $venue->description = $request->description;
            $venue->status = $request->status;
            $venue->save();

            // Return the venue with full image URL
            $venue->image = $venue->image ? asset('storage/' . $venue->image) : null;

            return response()->json([
                'status' => 'success',
                'message' => 'Venue updated successfully',
                'venue' => $venue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update venue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $venue = Venue::findOrFail($id);

            // Delete venue image if exists
            if ($venue->image) {
                $imagePath = public_path('storage/' . $venue->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $venue->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Venue deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete venue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVenuesByAdmin($adminId)
    {
        try {
            \Log::info('Fetching venues for admin: ' . $adminId);
            
            $admin = User::findOrFail($adminId);
            $venues = Venue::where('created_by', $adminId)
                ->get()
                ->map(function ($venue) {
                    // Remove 'venues/' from the stored path since it's already included
                    $imagePath = $venue->image ? str_replace('venues/', '', $venue->image) : null;
                    
                    return [
                        'id' => $venue->id,
                        'name' => $venue->name,
                        'description' => $venue->description,
                        'status' => $venue->status,
                        'image' => $imagePath ? asset('storage/venues/' . $imagePath) : null
                    ];
                });
            
            return response()->json([
                'status' => 'success',
                'venues' => $venues,
                'admin_name' => $admin->firstname . ' ' . $admin->lastname
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching venues: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch venues',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateVenueStatus($id, $status)
    {
        try {
            $venue = Venue::findOrFail($id);
            $venue->status = $status;
            $venue->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Venue status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update venue status'
            ], 500);
        }
    }
} 