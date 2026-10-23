<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');

        $imageKeys = ['why_choose_us_image'];
        foreach ($imageKeys as $key) {
            if (isset($settings[$key]) && $settings[$key]) {
                $settings[$key . '_url'] = asset('storage/' . $settings[$key]);
            }
        }

        return ApiResponse::success($settings, 'Site settings retrieved successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'address' => 'sometimes|required|string|max:255',
            'phone' => ['sometimes', 'required', 'string', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'email' => 'sometimes|required|email|max:255',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'map_latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'map_longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'map_embed_url' => 'sometimes|nullable|url',
            'why_choose_us_description' => 'sometimes|string',
            'why_choose_us_image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $allowedKeys = [
            'address', 'phone', 'email',
            'facebook', 'twitter', 'instagram', 'youtube', 'linkedin',
            'map_latitude', 'map_longitude', 'map_embed_url',
            'why_choose_us_description'
        ];

        $updatedSettings = [];

        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                $setting = SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
                $updatedSettings[$key] = $setting->value;
            }
        }

        if ($request->hasFile('why_choose_us_image')) {
            $oldImage = SiteSetting::where('key', 'why_choose_us_image')->value('value');
            
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $newImagePath = $request->file('why_choose_us_image')->store('settings', 'public');
            
            $setting = SiteSetting::updateOrCreate(
                ['key' => 'why_choose_us_image'],
                ['value' => $newImagePath]
            );
            
            $updatedSettings['why_choose_us_image'] = $setting->value;
            $updatedSettings['why_choose_us_image_url'] = asset('storage/' . $setting->value);
        }

        return ApiResponse::success($updatedSettings, 'Site settings updated successfully.');
    }
}