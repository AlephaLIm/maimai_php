<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Sorted;
use App\Models\Chart;

class Paginate extends Controller
{
    public static function generate_pagination(Request $request, $sorted_charts) {
        $path = '/songs';

        $charts = self::paginate($sorted_charts, 50);
        $charts->withPath($path);

        return $charts;
    }

    public static function paginate($items, $perPage = 30, $page = null) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }
}
