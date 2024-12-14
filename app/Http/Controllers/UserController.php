<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        try {
            $authUser = Auth::user();

            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            if ($authUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view all users',
                ], 403);
            }

            $users = User::all();

            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved Users',
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving users.',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $authUser = Auth::user();
            if ($authUser->id !== $user->id && $authUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this user',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved user',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user', ['id' => $id, 'user' => Auth::user(), 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the user.',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required',
                ], 422);
            }

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $authUser = Auth::user();
            if ($authUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this account',
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User account deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the user.',
            ], 500);
        }
    }


    
}
