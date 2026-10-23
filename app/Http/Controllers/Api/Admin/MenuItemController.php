<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    use PaginationTrait;

    public function index(Request $request)
    {
        $query = MenuItem::with('category');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $items = $query->latest()->paginate($this->getPerPage($request));

        return ApiResponse::paginated($items, 'Menu items retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'sometimes|boolean',
        ]);

        $data = $request->except('image');
        $data['is_available'] = $request->input('is_available', true);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu_items', 'public');
        }

        $menuItem = MenuItem::create($data);

        return ApiResponse::success($menuItem->load('category'), 'Menu item created successfully.', 201);
    }

    public function show($id)
    {
        $menuItem = MenuItem::with('category')->findOrFail($id);

        return ApiResponse::success($menuItem, 'Menu item retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'sometimes|boolean',
        ]);

        $data = $request->only(['category_id', 'name', 'description', 'price', 'is_available']);

        if ($request->hasFile('image')) {
            if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('menu_items', 'public');
        }

        $menuItem->update($data);

        return ApiResponse::success($menuItem->load('category'), 'Menu item updated successfully.');
    }

    public function toggleStatus($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        $message = $menuItem->is_available 
            ? 'Menu item is now available.' 
            : 'Menu item is now unavailable.';

        return ApiResponse::success($menuItem->load('category'), $message);
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);

        if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        return ApiResponse::success(null, 'Menu item deleted successfully.');
    }
}