<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'phone' => 'required|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'user' // Set default role as user
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserProfile()
    {
        try {
            $user = auth()->user();
            \Log::info('User profile request:', [
                'user_id' => $user->id,
                'role' => $user->role
            ]);
            
            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'role' => $user->role,
                    'profile_image' => $user->profile_image
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting user profile:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get user profile'
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'phone' => 'required|string|max:20'
            ]);

            $user->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadProfileImage(Request $request)
    {
        \Log::info('Upload profile image request received', [
            'files' => $request->allFiles(),
            'headers' => $request->headers->all()
        ]);
        
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $user = $request->user();
            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                
                // Check if file is valid
                if (!$file->isValid()) {
                    throw new \Exception('Invalid file upload');
                }

                // Ensure the profile_images directory exists in public/storage
                $uploadPath = public_path('storage/profile_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Delete old image if exists
                if ($user->profile_image) {
                    $oldImagePath = public_path('storage/profile_images/' . $user->profile_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Generate unique filename
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Move the uploaded file directly to public/storage/profile_images
                $file->move($uploadPath, $imageName);

                // Update user profile_image
                $user->profile_image = $imageName;
                $user->save();

                \Log::info('Image uploaded successfully', [
                    'image_name' => $imageName,
                    'upload_path' => $uploadPath,
                    'public_url' => asset('storage/profile_images/' . $imageName)
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile image updated successfully',
                    'image_path' => $imageName,
                    'full_url' => asset('storage/profile_images/' . $imageName)
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No image file provided'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Profile image upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllCustomers()
    {
        try {
            // Change 'customer' to 'user' to fetch users with role 'user'
            $customers = User::where('role', 'user')->get();
            
            return response()->json([
                'status' => 'success',
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch customers'
            ], 500);
        }
    }

    public function deleteCustomer($id)
    {
        try {
            $customer = User::findOrFail($id);
            
            // Change check to verify 'user' role instead of 'customer'
            if ($customer->role !== 'user') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete non-user accounts'
                ], 403);
            }
            
            $customer->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user'
            ], 500);
        }
    }

    public function updateCustomer(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);
            
            // Verify it's a user account
            if ($customer->role !== 'user') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot modify non-user accounts'
                ], 403);
            }
            
            // Validate the request
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$id,
                'phone' => 'required|string|max:20'
            ]);

            // Update the user
            $customer->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'user' => $customer
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAdminUsers()
    {
        try {
            $adminUsers = User::whereIn('role', ['administrator', 'admin'])
                ->select('id', 'firstname', 'lastname', 'email', 'role', 'profile_image')
                ->get();

            return response()->json([
                'status' => 'success',
                'users' => $adminUsers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch admin users'
            ], 500);
        }
    }

    public function getAdmins(Request $request)
    {
        try {
            $query = User::query();
            
            // Filter by roles
            $query->whereIn('role', ['admin', 'administrator']);

            $users = $query->get();

            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch admin users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


