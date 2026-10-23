<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected function casts(): array
{
    return [
        'is_active' => 'boolean',
    ];
}

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}