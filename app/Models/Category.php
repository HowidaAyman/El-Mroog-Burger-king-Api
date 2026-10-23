<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected function casts(): array
{
    return [
        'is_active' => 'boolean',
    ];
}

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? asset('storage/' . $this->icon) : null;
    }

    protected $appends = ['image_url', 'icon_url'];
}