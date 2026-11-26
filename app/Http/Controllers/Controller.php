<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Ambil jumlah item per halaman yang valid dari query string.
     */
    protected function resolvePerPage(Request $request, array $allowed = [10, 15, 25, 50, 100], int $default = 10): int
    {
        $perPage = (int) $request->input('per_page', $default);
        return in_array($perPage, $allowed, true) ? $perPage : $default;
    }
}
