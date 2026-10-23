<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::latest()->get();

        return ApiResponse::success($features, 'Features retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:1024',
        ]);

        $data = $request->except('icon');

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('features', 'public');
        }

        $feature = Feature::create($data);

        return ApiResponse::success($feature, 'Feature created successfully.', 201);
    }

    public function update(Request $request, $id)
    {
        $feature = Feature::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'icon' => 'sometimes|image|mimes:jpeg,png,jpg,webp,svg|max:1024',
        ]);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('icon')) {
            if ($feature->icon && Storage::disk('public')->exists($feature->icon)) {
                Storage::disk('public')->delete($feature->icon);
            }
            $data['icon'] = $request->file('icon')->store('features', 'public');
        }

        $feature->update($data);

        return ApiResponse::success($feature, 'Feature updated successfully.');
    }

    public function destroy($id)
    {
        $feature = Feature::findOrFail($id);

        if ($feature->icon && Storage::disk('public')->exists($feature->icon)) {
            Storage::disk('public')->delete($feature->icon);
        }

        $feature->delete();

        return ApiResponse::success(null, 'Feature deleted successfully.');
    }
}