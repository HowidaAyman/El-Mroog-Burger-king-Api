<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function show()
    {
        $about = AboutUs::first();

        if (!$about) {
            return ApiResponse::success([
                'id' => null,
                'title' => null,
                'description' => null,
                'video_url' => null,
            ], 'No About Us content yet. Please create one.');
        }

        return ApiResponse::success($about, 'About Us retrieved successfully.');
    }

    public function update(Request $request)
    {
        $about = AboutUs::first();

        if (!$about) {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'video_url' => 'nullable|url',
            ]);

            $about = AboutUs::create($request->only(['title', 'description', 'video_url']));
            return ApiResponse::success($about, 'About Us created successfully.', 201);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'video_url' => 'nullable|url',
        ]);

        $data = $request->only(['title', 'description', 'video_url']);

        $about->update($data);

        return ApiResponse::success($about, 'About Us updated successfully.');
    }
}