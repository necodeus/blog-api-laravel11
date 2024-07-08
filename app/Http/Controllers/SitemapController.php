<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        $urls = DB::table('urls')
            ->where('urls.language', '=', 'pl')
            ->get([
                'urls.path',
                'urls.updated_at',
            ]);

        $sitemap = $urls->map(function ($url) {
            $priority = 0.8;

            if ($url->path === '/') {
                $priority = 1;
            }

            return [
                'path' => $url->path,
                'change_frequency' => 'always',
                'priority' => $priority,
                'updated_at' => $url->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'sitemap' => $sitemap,
        ]);
    }
}
