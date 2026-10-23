<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected function casts(): array
{
    return [
        'is_active' => 'boolean',
    ];
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->with('replies')->latest();
    }


    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    protected $appends = ['image_url'];
}