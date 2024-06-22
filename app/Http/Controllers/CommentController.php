<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        $comments = DB::table('comments')
            ->where('post_id', '=', $request->input('postId'))
            ->get();

        return response()->json([
            'comments' => $comments,
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        // check if parent comment exists
        if ($request->input('parentId')) {
            $parent = DB::table('comments')
                ->where('id', '=', $request->input('parentId'))
                ->first();

            if (!$parent) {
                return response()->json([
                    'error' => 'Parent comment not found',
                ]);
            }
        }

        // check if content is provided
        if (!$request->input('content')) {
            return response()->json([
                'error' => 'Comment content is required',
            ]);
        }

        // check if post ID is provided
        if (!$request->input('postId')) {
            return response()->json([
                'error' => 'Post ID is required',
            ]);
        }

        // check if post exists
        $post = DB::table('posts')
            ->where('id', '=', $request->input('postId'))
            ->first();

        // return error if post not found
        if (!$post) {
            return response()->json([
                'error' => 'Post not found',
            ]);
        }

        $now = now();

        // insert comment into database
        $comment = DB::table('comments')->insert([
            'post_id' => $request->input('postId'),
            'parent_id' => $request->input('parentId'),
            'content' => $request->input('content'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // return error if comment not added
        if (!$comment) {
            return response()->json([
                'error' => 'Failed to add comment',
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
