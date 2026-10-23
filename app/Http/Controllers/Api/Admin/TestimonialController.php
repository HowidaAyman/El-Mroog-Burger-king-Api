<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::query();

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $testimonials = $query->latest()->get();

        return ApiResponse::success($testimonials, 'Testimonials retrieved successfully.');
    }

    public function toggleStatus($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        
        $testimonial->update([
            'is_active' => !$testimonial->is_active
        ]);

        $message = $testimonial->is_active 
            ? 'Testimonial activated successfully.' 
            : 'Testimonial deactivated successfully.';

        return ApiResponse::success($testimonial, $message);
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);

        if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->delete();

        return ApiResponse::success(null, 'Testimonial deleted successfully.');
    }
}