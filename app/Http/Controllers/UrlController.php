<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UrlController extends Controller
{
    public function single(Request $request): JsonResponse
    {
        $start = microtime(true);

        $url = DB::table('urls')
            ->where('path', '=', $request->input('path'))
            ->first();

        if (!$url) {
            return response()->json([
                'time' => microtime(true) - $start,
                'url' => $url,
            ]);
        }

        $data = [];

        switch ($url->content_type) {
            case 'POSTS': {
                $posts = DB::table('posts')
                    ->join('urls', function ($join) {
                        $join->on('posts.id', '=', 'urls.content_id')
                            ->where('urls.content_type', '=', 'POST')
                            ->where('urls.language', '=', 'pl');
                    })
                    ->join('authors', 'posts.editor_account_id', '=', 'authors.id')
                    ->leftJoin('urls as author_urls', function ($join) {
                        $join->on('authors.id', '=', 'author_urls.content_id')
                            ->where('author_urls.content_type', '=', 'AUTHOR')
                            ->where('author_urls.language', '=', 'pl');
                    })
                    ->orderBy('urls.created_at', 'desc')
                    ->limit(10)
                    ->get([
                        'posts.id',
                        'urls.path',
                        'posts.title',
                        'urls.created_at',
                        'authors.name as author_name',
                        'authors.avatar_image_id',
                        'author_urls.path as author_path'
                    ]);

                foreach ($posts as &$post) {
                    $post->author_picture = [
                        "25x25" => "https://images.necodeo.com/{$post->avatar_image_id}/25x25",
                    ];

                    unset($post->avatar_image_id);
                }

                unset($post);

                $data = [
                    'posts' => $posts,
                ];

                break;
            }
            case 'POST': {
                $post = DB::table('posts')
                    ->where('id', '=', $url->content_id)
                    ->first([
                        'id',
                        'title',
                        'content',
                        'editor_account_id',
                        'main_image_id',
                        'created_at',
                    ]);

                $postAuthor = DB::table('authors')
                    ->leftJoin('urls', function ($join) {
                        $join->on('authors.id', '=', 'urls.content_id')
                            ->where('urls.content_type', '=', 'AUTHOR')
                            ->where('urls.language', '=', 'pl');
                    })
                    ->where('authors.id', '=', $post->editor_account_id)
                    ->first([
                        'authors.id',
                        'urls.path',
                        'authors.name',
                        'authors.avatar_image_id',
                        'authors.bio',
                    ]);

                $post->cover_picture = [
                    "1200x430" => "https://images.necodeo.com/{$post->main_image_id}/1200x430",
                    "785x420" => "https://images.necodeo.com/{$post->main_image_id}/785x420",
                ];

                $postAuthor->author_picture = [
                    "25x25" => "https://images.necodeo.com/{$postAuthor->avatar_image_id}/25x25",
                    "55x55" => "https://images.necodeo.com/{$postAuthor->avatar_image_id}/55x55",
                ];

                unset($post->main_image_id);
                unset($postAuthor->avatar_image_id);

                $otherPosts = DB::table('posts')
                    ->where('posts.id', '!=', $post->id)
                    ->join('urls', 'posts.id', '=', 'urls.content_id')
                    ->where('urls.content_type', '=', 'POST')
                    ->where('urls.language', '=', 'pl')
                    ->orderBy('urls.created_at', 'desc')
                    ->limit(10)
                    ->get([
                        'posts.id',
                        'posts.title',
                        'urls.path',
                    ]);

                $data = [
                    'post' => $post,
                    'postAuthor' => $postAuthor,
                    'otherPosts' => $otherPosts,
                ];

                break;
            }
            case 'AUTHORS': {
                $authors = DB::table('authors')
                    ->leftJoin('urls', 'authors.id', '=', 'urls.content_id')
                    ->where('urls.content_type', '=', 'AUTHOR')
                    ->where('urls.language', '=', 'pl')
                    ->orderBy('name', 'asc')
                    ->limit(10)
                    ->get([
                        'authors.id',
                        'authors.name',
                        'authors.slug',
                        'authors.bio',
                        'authors.avatar_image_id',
                        'urls.created_at',
                        'urls.updated_at',
                        'urls.path',
                    ]);

                $numberOfPosts = DB::table('posts')
                    ->select('editor_account_id', DB::raw('count(*) as count'))
                    ->groupBy('editor_account_id')
                    ->get()
                    ->keyBy('editor_account_id');

                foreach ($authors as &$author) {
                    $author->numberOfPosts = $numberOfPosts->get($author->id)->count ?? 0;
                }

                unset($author);

                $authors = $authors->sortByDesc('numberOfPosts')->values();

                $data = [
                    'authors' => $authors,
                ];

                break;
            }
            case 'AUTHOR': {
                $author = DB::table('authors')
                    ->where('id', '=', $url->content_id)
                    ->first();

                $posts = DB::table('posts')
                    ->where('publisher_account_id', '=', $author->id)
                    ->join('urls', 'posts.id', '=', 'urls.content_id')
                    ->where('urls.content_type', '=', 'POST')
                    ->where('urls.language', '=', 'pl')
                    ->orderBy('urls.created_at', 'desc')
                    ->limit(10)
                    ->get([
                        'posts.id',
                        'posts.title',
                        'posts.main_image_id',
                        'urls.path',
                    ]);

                foreach ($posts as &$post) {
                    $post->image = "https://images.necodeo.com/{$post->main_image_id}/785x420";

                    $post->tagName = 'EXAMPLE';

                    unset($post->main_image_id);
                }

                unset($post);

                $data = [
                    'author' => $author,
                    'posts' => $posts,
                ];

                break;
            }
        }

        return response()->json([
            'time' => microtime(true) - $start,
            'url' => $url,
            ...$data,
        ]);
    }
}
