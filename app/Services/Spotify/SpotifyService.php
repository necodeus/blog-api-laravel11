<?php

namespace App\Services\Spotify;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class SpotifyService
{
    public static function getUser(string $bearerToken, string $userId)
    {
        $marketCode = "PL";
        $fields = urlencode("");
        $additionalTypes = urlencode("");

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/users/$userId?market=$marketCode&fields=$fields&additional_types=$additionalTypes");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public static function getCurrentUser(string $bearerToken)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/me");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public static function getRefreshToken(string $clientId, string $clientSecret, string $refreshToken)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://accounts.spotify.com/api/token");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=$refreshToken");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Basic " . base64_encode("{$clientId}:{$clientSecret}"),
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public static function getTrack(string $bearerToken, string $trackId)
    {
        $marketCode = "PL";
        $fields = urlencode("");
        $additionalTypes = urlencode("");

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/tracks/$trackId?market=$marketCode&fields=$fields&additional_types=$additionalTypes");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public static function authorizeWithSpotify(string $clientId, string $state, string $redirectUri): RedirectResponse
    {
        $scope = implode(' ', [
            'user-read-recently-played', // poprzednio grany utwór
            'user-read-playback-state', // stan odtwarzania
        ]);

        $url = "https://accounts.spotify.com/authorize?response_type=code&client_id=$clientId&scope=$scope&state=$state&redirect_uri=$redirectUri";

        return Redirect::away($url);
    }

    public static function getToken(string $clientId, string $clientSecret, string $code, string $redirectUri)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://accounts.spotify.com/api/token");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirectUri");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Basic " . base64_encode("{$clientId}:{$clientSecret}"),
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return (object) array_merge((array) json_decode($response), ["http_code" => $code]);
    }

    public static function getArtist(string $bearerToken, string $artistId)
    {
        $marketCode = "PL";
        $fields = urlencode("");
        $additionalTypes = urlencode("");

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/artists/$artistId?market=$marketCode&fields=$fields&additional_types=$additionalTypes");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public static function getAlbum(string $bearerToken, string $albumId)
    {
        $market = "PL";
        $fields = urlencode("");
        $additionalTypes = urlencode("");

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/albums/$albumId?market=$market&fields=$fields&additional_types=$additionalTypes");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public static function getPlayerState(string $bearerToken)
    {
        $marketCode = "PL";

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/me/player?market={$marketCode}");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $response = json_decode($response);

        if (isset($response->error)) {
            throw new \Exception($code);
        }

        return $response;
    }

    public static function getDevices(string $bearerToken)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/me/player/devices");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $response = json_decode($response);

        if (isset($response->error)) {
            throw new \Exception($code);
        }

        return $response;
    }

    public static function getRecentlyPlayed(string $bearerToken)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/me/player/recently-played");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $response = json_decode($response);

        if (isset($response->error)) {
            throw new \Exception($code);
        }

        return $response;
    }

    public static function getPlaylist(string $bearerToken, string $playlistId)
    {
        $marketCode = "PL";
        $fields = urlencode("collaborative,description,followers,images,name,owner,public,tracks,uri");
        $additionalTypes = urlencode("track,episode");

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/playlists/$playlistId?market=$marketCode&fields=$fields&additional_types=$additionalTypes");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public static function getTracks(string $bearerToken, string $playlistId, int $offset = 0, int $limit = 100)
    {
        $marketCode = "PL";
        $fields = urlencode("");
        $additionalTypes = urlencode("track,episode");

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.spotify.com/v1/playlists/$playlistId/tracks?limit=$limit&offset=$offset&market=$marketCode&fields=$fields&additional_types=$additionalTypes");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$bearerToken}",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }
}
