<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccessApiController extends Controller
{
    /**
     * Display a listing of accesses
     */
    public function index(Request $request)
    {
        $query = Access::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('access_name', 'like', "%{$search}%")
                  ->orWhere('access_description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'deleted') {
                $query->where('is_deleted', true);
            } else {
                $query->where('is_deleted', false);
            }
        } else {
            $query->where('is_deleted', false); // default: hanya yang aktif
        }

        $accesses = $query->orderBy('created_at', 'desc')
                         ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'message' => 'Accesses retrieved successfully',
            'data' => $accesses->items(),
            'pagination' => [
                'current_page' => $accesses->currentPage(),
                'per_page' => $accesses->perPage(),
                'total' => $accesses->total(),
                'last_page' => $accesses->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created access
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_name' => 'required|string|max:255',
            'access_description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $access = Access::create([
            'access_name' => $request->access_name,
            'access_description' => $request->access_description,
            'is_deleted' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Access created successfully',
            'data' => $access
        ], 201);
    }

    /**
     * Display the specified access
     */
    public function show($id)
    {
        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'success' => false,
                'message' => 'Access not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Access retrieved successfully',
            'data' => $access
        ]);
    }

    /**
     * Update the specified access
     */
    public function update(Request $request, $id)
    {
        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'success' => false,
                'message' => 'Access not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'access_name' => 'required|string|max:255',
            'access_description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $access->update([
            'access_name' => $request->access_name,
            'access_description' => $request->access_description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Access updated successfully',
            'data' => $access->fresh()
        ]);
    }

    /**
     * Remove the specified access
     */
    public function destroy($id)
    {
        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'success' => false,
                'message' => 'Access not found'
            ], 404);
        }

        // Soft delete dengan mengubah is_deleted menjadi true
        $access->update(['is_deleted' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Access deleted successfully'
        ]);
    }
}