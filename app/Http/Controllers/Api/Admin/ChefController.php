<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Chef;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChefController extends Controller
{
    public function index()
    {
        $chefs = Chef::latest()->get();

        return ApiResponse::success($chefs, 'Chefs retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('chefs', 'public');
        }

        $chef = Chef::create($data);

        return ApiResponse::success($chef, 'Chef created successfully.', 201);
    }

    public function show($id)
    {
        $chef = Chef::findOrFail($id);

        return ApiResponse::success($chef, 'Chef retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $chef = Chef::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
        ]);

        $data = $request->only(['name', 'position', 'facebook', 'twitter', 'instagram', 'linkedin']);

        if ($request->hasFile('image')) {
            if ($chef->image && Storage::disk('public')->exists($chef->image)) {
                Storage::disk('public')->delete($chef->image);
            }
            $data['image'] = $request->file('image')->store('chefs', 'public');
        }

        $chef->update($data);

        return ApiResponse::success($chef, 'Chef updated successfully.');
    }

    public function destroy($id)
    {
        $chef = Chef::findOrFail($id);

        if ($chef->image && Storage::disk('public')->exists($chef->image)) {
            Storage::disk('public')->delete($chef->image);
        }

        $chef->delete();

        return ApiResponse::success(null, 'Chef deleted successfully.');
    }
}