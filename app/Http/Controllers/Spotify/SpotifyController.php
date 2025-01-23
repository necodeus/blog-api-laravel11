<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
use App\Models\Spotify\SpotifyUser;
use App\Services\Spotify\SpotifyService;

class SpotifyController extends Controller
{
    public function redirectToSpotify()
    {
        $clientId = env('SPOTIFY_CLIENT_ID');

        $state = bin2hex(random_bytes(16));
        session(['oauth_state' => $state]);

        return SpotifyService::authorizeWithSpotify($clientId, $state, route('spotify.callback'));
    }

    public function handleCallback()
    {
        $state = $_GET['state'] ?? '';

        if ($state !== session('oauth_state')) {
            die('Błąd autoryzacji');
        }

        $code = $_GET['code'] ?? '';

        if (empty($code)) {
            die('Błąd autoryzacji');
        }

        $clientId = env('SPOTIFY_CLIENT_ID');
        $clientSecret = env('SPOTIFY_CLIENT_SECRET');

        $token = SpotifyService::getToken($clientId, $clientSecret, $code, route('spotify.callback'));

        if (empty($token->access_token)) {
            die('Błąd autoryzacji');
        }

        session(['spotify_token' => $token->access_token]);

        $spotifyUser = SpotifyService::getCurrentUser($token->access_token);

        $existingUser = SpotifyUser::where('user_id', $spotifyUser->id)->first();

        if ($existingUser) {
            $existingUser->update([
                'access_token' => $token->access_token,
                'refresh_token' => $token->refresh_token,
                'scope' => $token->scope,
                'followers' => $spotifyUser->followers->total,
                'avatar_url' => $spotifyUser->images[0]->url,
                'type' => $spotifyUser->type,
                'token_refreshed_at' => null,
            ]);

            return response()->json($existingUser);
        }

        $createdUser = SpotifyUser::create([
            'name' => $spotifyUser->display_name,
            'user_id' => $spotifyUser->id,
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'scope' => $token->scope,
            'followers' => $spotifyUser->followers->total,
            'avatar_url' => $spotifyUser->images[0]->url,
            'type' => $spotifyUser->type,
            'token_refreshed_at' => null,
        ]);

        return response()->json($createdUser);
    }
}
