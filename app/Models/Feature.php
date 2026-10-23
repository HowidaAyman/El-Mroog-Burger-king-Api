<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? asset('storage/' . $this->icon) : null;
    }

    protected $appends = ['icon_url'];
}