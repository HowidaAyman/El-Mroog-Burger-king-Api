<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@elmrooj.com',
            'password' => Hash::make('123456'),
            'role' => UserRole::ADMIN,
        ]);
        \App\Models\Reservation::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '01234567890',
        'date' => now()->addDays(2)->format('Y-m-d'),
        'time' => '19:00',
        'guests' => 4,
        'status' => 'pending',
    ]);

    \App\Models\Reservation::create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '09876543210',
        'date' => now()->addDays(1)->format('Y-m-d'),
        'time' => '20:30',
        'guests' => 2,
        'status' => 'confirmed',
    ]);
    $burgers = \App\Models\Category::create([
    'name' => 'Burgers',
    'slug' => 'burgers',
]);

\App\Models\Category::create([
    'name' => 'Snacks',
    'slug' => 'snacks',
]);

\App\Models\Category::create([
    'name' => 'Beverages',
    'slug' => 'beverages',
]);

\App\Models\MenuItem::create([
    'category_id' => $burgers->id,
    'name' => 'Mini cheese Burger',
    'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.',
    'price' => 9.00,
    'image' => 'menu_items/dummy-burger.jpg', // مجرد مسار وهمي
    'is_available' => true,
]);

\App\Models\MenuItem::create([
    'category_id' => $burgers->id,
    'name' => 'Double size burger',
    'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.',
    'price' => 11.00,
    'image' => 'menu_items/dummy-burger.jpg',
    'is_available' => true,
]);

\App\Models\Chef::create([
    'name' => 'Adam Phillips',
    'position' => 'CEO, Co Founder',
    'image' => 'chefs/adam.jpg',
    'twitter' => 'https://twitter.com/adam',
    'facebook' => 'https://facebook.com/adam',
    'linkedin' => 'https://linkedin.com/in/adam',
    'instagram' => 'https://instagram.com/adam',
]);

\App\Models\Chef::create([
    'name' => 'Dylan Adams',
    'position' => 'Master Chef',
    'image' => 'chefs/dylan.jpg',
    'twitter' => 'https://twitter.com/dylan',
    'facebook' => 'https://facebook.com/dylan',
    'linkedin' => 'https://linkedin.com/in/dylan',
    'instagram' => 'https://instagram.com/dylan',
]);

\App\Models\Chef::create([
    'name' => 'Jhon Doe',
    'position' => 'Master Chef',
    'image' => 'chefs/jhon.jpg',
    'twitter' => null,
    'facebook' => null,
    'linkedin' => null,
    'instagram' => null,
]);

\App\Models\Chef::create([
    'name' => 'Josh Dunn',
    'position' => 'Master Chef',
    'image' => 'chefs/josh.jpg',
    'twitter' => null,
    'facebook' => null,
    'linkedin' => null,
    'instagram' => null,
]);
\App\Models\Testimonial::create([
    'client_name' => 'Client Name',
    'profession' => 'Profession',
    'image' => 'testimonials/client1.jpg',
    'content' => 'Lorem ipsum dolor sit amet. Phasel nec preti mi. Curabit facilis ornare velit non vulput.',
    'is_active' => true,
]);

\App\Models\Testimonial::create([
    'client_name' => 'Client Name',
    'profession' => 'Profession',
    'image' => 'testimonials/client2.jpg',
    'content' => 'Lorem ipsum dolor sit amet. Phasel nec preti mi. Curabit facilis ornare velit non vulput.',
    'is_active' => true,
]);

\App\Models\Testimonial::create([
    'client_name' => 'Client Name',
    'profession' => 'Profession',
    'image' => 'testimonials/client3.jpg',
    'content' => 'Lorem ipsum dolor sit amet. Phasel nec preti mi. Curabit facilis ornare velit non vulput.',
    'is_active' => true,
]);
$defaultSettings = [
    'address' => '123 Street, NY, USA',
    'phone' => '+012 345 6789',
    'email' => 'info@example.com',
    'facebook' => 'https://facebook.com/restaurant',
    'twitter' => 'https://twitter.com/restaurant',
    'instagram' => 'https://instagram.com/restaurant',
    'youtube' => 'https://youtube.com/restaurant',
    'linkedin' => 'https://linkedin.com/company/restaurant',
    'map_latitude' => '40.7128',
    'map_longitude' => '-74.0060',
    'map_embed_url' => 'https://www.google.com/maps/embed?pb=...',
];

foreach ($defaultSettings as $key => $value) {
    \App\Models\SiteSetting::create([
        'key' => $key,
        'value' => $value,
    ]);
}
$blogCategory = \App\Models\BlogCategory::create([
    'name' => 'Food',
    'slug' => 'food',
]);

$tag1 = \App\Models\Tag::create(['name' => 'National', 'slug' => 'national']);
$tag2 = \App\Models\Tag::create(['name' => 'International', 'slug' => 'international']);

$admin = \App\Models\User::first();

\App\Models\Post::create([
    'user_id' => $admin->id,
    'blog_category_id' => $blogCategory->id,
    'title' => 'Lorem ipsum dolor sit amet',
    'slug' => 'lorem-ipsum-dolor-sit-amet',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
    'image' => 'posts/sample-post.jpg',
])->tags()->attach([$tag1->id, $tag2->id]);

$post = \App\Models\Post::first();


$comment1 = \App\Models\Comment::create([
    'post_id' => $post->id,
    'parent_id' => null,
    'name' => 'Josh Dunn',
    'email' => 'josh@example.com',
    'website' => null,
    'comment' => 'Lorem ipsum dolor sit amet. Integer lorem augue purus mollis sapien...',
]);

$reply1 = \App\Models\Comment::create([
    'post_id' => $post->id,
    'parent_id' => $comment1->id,
    'name' => 'Josh Dunn',
    'email' => 'josh@example.com',
    'website' => null,
    'comment' => 'Reply to the first comment...',
]);

\App\Models\Comment::create([
    'post_id' => $post->id,
    'parent_id' => $reply1->id,
    'name' => 'Josh Dunn',
    'email' => 'josh@example.com',
    'website' => null,
    'comment' => 'Reply to the reply (nested level 3)...',
]);

$comment2 = \App\Models\Comment::create([
    'post_id' => $post->id,
    'parent_id' => null,
    'name' => 'Josh Dunn',
    'email' => 'josh@example.com',
    'website' => null,
    'comment' => 'Another standalone comment...',
]);

$admin = \App\Models\User::first();

\App\Models\Comment::create([
    'post_id' => $post->id,
    'parent_id' => $comment1->id,
    'user_id' => $admin->id,
    'name' => $admin->name,
    'email' => $admin->email,
    'website' => null,
    'comment' => 'Reply from the admin...',
]);
\App\Models\AboutUs::create([
    'title' => 'Cooking Since 1990',
    'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus nec pretium mi. Curabitur facilisis ornare velit non vulputate. Aliquam metus tortor, auctor id gravida condimentum, viverra quis sem. Curabitur non nisl nec nisi scelerisque maximus.',
    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
]);

$features = [
    ['title' => "World's best Chef", 'description' => 'Lorem ipsum dolor sit amet eli. Phasel nec preti mi.', 'icon' => 'features/chef.png'],
    ['title' => 'Natural ingredients', 'description' => 'Lorem ipsum dolor sit amet eli. Phasel nec preti mi.', 'icon' => 'features/ingredients.png'],
    ['title' => 'Best quality products', 'description' => 'Lorem ipsum dolor sit amet eli. Phasel nec preti mi.', 'icon' => 'features/quality.png'],
    ['title' => 'Fresh vegetables & Meet', 'description' => 'Lorem ipsum dolor sit amet eli. Phasel nec preti mi.', 'icon' => 'features/vegetables.png'],
    ['title' => 'Fastest door delivery', 'description' => 'Lorem ipsum dolor sit amet eli. Phasel nec preti mi.', 'icon' => 'features/delivery.png'],
    ['title' => 'Ground beef & Low fat', 'description' => 'Lorem ipsum dolor sit amet eli. Phasel nec preti mi.', 'icon' => 'features/beef.png'],
];

foreach ($features as $feature) {
    \App\Models\Feature::create($feature);
}


\App\Models\SiteSetting::create([
    'key' => 'why_choose_us_image',
    'value' => 'settings/why-choose-us.jpg',
]);

\App\Models\SiteSetting::create([
    'key' => 'why_choose_us_description',
    'value' => 'Lorem ipsum dolor sit amet consect adipis elit. Phasel nec preti mi...',
]);

\App\Models\Contact::create([
    'name' => 'Ahmed Ali',
    'email' => 'ahmed@example.com',
    'subject' => 'Inquiry about booking',
    'message' => 'Hello, I want to book a table for 10 people next Friday. Is it possible?',
    'is_read' => false,
]);

\App\Models\Contact::create([
    'name' => 'Sara Mohamed',
    'email' => 'sara@example.com',
    'subject' => 'Feedback',
    'message' => 'The food was amazing! Thank you for the great service.',
    'is_read' => true,
]);
    }
}
