<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EmotionController extends Controller
{
    public function add(Request $request): JsonResponse
    {
        $uaHash = md5($request->header('User-Agent')); // TODO: params? validation?
        $ipAddress = $request->ip(); // TODO: params? validation?

        // check if content type is provided
        if (!$request->input('content_type')) {
            return response()->json([
                'error' => 'Content type is required',
            ]);
        }

        // check if content type is valid
        if (!in_array($request->input('content_type'), ['POST', 'COMMENT'])) {
            return response()->json([
                'error' => 'Invalid content type',
            ]);
        }

        // check if emotion is provided
        if (!$request->input('emotion')) {
            return response()->json([
                'error' => 'Emotion is required',
            ]);
        }

        // check if emotion is valid
        if (!in_array($request->input('emotion'), ['LIKE', 'LOVE', 'HAHA', 'WOW', 'SAD', 'ANGRY'])) {
            return response()->json([
                'error' => 'Invalid emotion',
            ]);
        }

        // check if content ID is provided
        if (!$request->input('content_id')) {
            return response()->json([
                'error' => 'Content ID is required',
            ]);
        }

        // check if content exists
        switch ($request->input('content_type')) {
            case 'POST': {
                $content = DB::table('posts')
                    ->where('id', '=', $request->input('content_id'))
                    ->first();
                break;
            }
            case 'COMMENT': {
                $content = DB::table('comments')
                    ->where('id', '=', $request->input('content_id'))
                    ->first();
                break;
            }
        }

        // return error if content not found
        if (!$content) {
            return response()->json([
                'error' => 'Content not found',
            ]);
        }

        // check if user has already voted
        $emotion = DB::table('emotions')
            ->where('content_type', '=', $request->input('content_type'))
            ->where('content_id', '=', $request->input('content_id'))
            ->where('ua_hash', '=', $uaHash)
            ->where('ipv4_address', '=', $ipAddress)
            ->first();

        // return error if user has already voted
        if ($emotion) {
            return response()->json([
                'error' => 'You have already voted for this content',
            ]);
        }

        // insert emotion into database
        $emotion = DB::table('emotions')->insert([
            'content_type' => $request->input('content_type'),
            'content_id' => $request->input('content_id'),
            'emotion' => $request->input('emotion'),
            'ipv4_address' => $ipAddress,
            'ua_hash' => $uaHash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // return error if failed to add emotion
        if (!$emotion) {
            return response()->json([
                'error' => 'Failed to add emotion',
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
