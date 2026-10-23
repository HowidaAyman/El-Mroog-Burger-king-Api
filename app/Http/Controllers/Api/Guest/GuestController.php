<?php

namespace App\Http\Controllers\Api\Guest;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Chef;
use App\Models\Feature;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    use PaginationTrait;
    public function getMenu()
    {
        $categories = Category::where('is_active', true)
            ->with(['menuItems' => function ($query) {
                $query->where('is_available', true);
            }])->get();

        return ApiResponse::success($categories, 'Menu retrieved successfully.');
    }
    public function getCategoryMenu($id)
    {
        $category = Category::where('is_active', true)
            ->with(['menuItems' => function ($query) {
                $query->where('is_available', true);
            }])
            ->findOrFail($id);

        return ApiResponse::success($category, 'Category menu retrieved successfully.');
    }

    public function getChefs()
    {
        $chefs = Chef::latest()->get();

        return ApiResponse::success($chefs, 'Chefs retrieved successfully.');
    }

    public function getTestimonials()
    {
        $testimonials = Testimonial::where('is_active', true)->latest()->get();

        return ApiResponse::success($testimonials, 'Testimonials retrieved successfully.');
    }

    public function getAboutUs()
    {
        $about = AboutUs::first();
        $features = Feature::latest()->get();
        $settings = SiteSetting::all()->pluck('value', 'key');

        return ApiResponse::success([
            'about' => $about,
            'features' => $features,
            'why_choose_us' => [
                'image' => $settings['why_choose_us_image'] ?? null,
                'image_url' => isset($settings['why_choose_us_image']) ? asset('storage/' . $settings['why_choose_us_image']) : null,
                'description' => $settings['why_choose_us_description'] ?? null,
            ]
        ], 'About Us page data retrieved successfully.');
    }

    public function getSiteSettings()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        
        if (isset($settings['why_choose_us_image'])) {
            $settings['why_choose_us_image_url'] = asset('storage/' . $settings['why_choose_us_image']);
        }

        return ApiResponse::success($settings, 'Site settings retrieved successfully.');
    }

    public function getBlogPosts(Request $request)
    {
        $query = Post::with(['category:id,name,slug', 'user:id,name'])
            ->where('is_active', true)
            ->withCount('comments');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('blog_category_id', $request->category_id);
        }

        if ($request->has('tag_id') && $request->tag_id != '') {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'featured':
                    $query->inRandomOrder();
                    break;
                case 'popular':
                    $query->orderBy('comments_count', 'desc');
                    break;
                case 'latest':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $posts = $query->paginate($this->getPerPage($request, 10));

        return ApiResponse::paginated($posts, 'Blog posts retrieved successfully.');
    }

    public function getSinglePost(Request $request, $slug)
    {
        $post = Post::with([
            'category:id,name,slug',
            'user:id,name',
            'tags:id,name,slug',
            'comments'
        ])
        ->where('is_active', true)
        ->where('slug', $slug)
        ->firstOrFail();

        $relatedPosts = Post::with(['category:id,name,slug'])
            ->where('is_active', true)
            ->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->limit(3)
            ->get();

        $recentPosts = Post::with(['category:id,name,slug'])
            ->where('is_active', true)
            ->where('id', '!=', $post->id)
            ->latest()
            ->limit(5)
            ->get();

        $featuredPosts = Post::with(['category:id,name,slug'])
            ->where('is_active', true)
            ->where('id', '!=', $post->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $popularPosts = Post::with(['category:id,name,slug'])
            ->where('is_active', true)
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->limit(4)
            ->get();

        $latestPosts = Post::with(['category:id,name,slug'])
            ->where('is_active', true)
            ->latest()
            ->limit(4)
            ->get();

        $categories = BlogCategory::where('is_active', true)
            ->withCount('posts')
            ->get();

        $tags = Tag::where('is_active', true)->get();

        return ApiResponse::success([
            'post' => $post,
            'related_posts' => $relatedPosts,
            'sidebar' => [
                'recent_posts' => $recentPosts,
                'featured_posts' => $featuredPosts,
                'popular_posts' => $popularPosts,
                'latest_posts' => $latestPosts,
                'categories' => $categories,
                'tags' => $tags,
            ]
        ], 'Post retrieved successfully.');
    }

    public function getBlogCategories()
    {
        $categories = BlogCategory::where('is_active', true)
            ->withCount('posts')
            ->get();

        return ApiResponse::success($categories, 'Blog categories retrieved successfully.');
    }
}