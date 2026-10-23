<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PaginationTrait
{
    protected function getPerPage(Request $request, int $default = 15): int
    {
        $perPage = $request->input('per_page', $default);
        
        if (!is_numeric($perPage) || $perPage < 1) {
            return $default;
        }

        return min((int) $perPage, 100);
    }
}