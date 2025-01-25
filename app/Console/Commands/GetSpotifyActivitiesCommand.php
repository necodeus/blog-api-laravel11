<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use App\Models\Spotify\SpotifyUser;
use App\Services\Spotify\SpotifyService;

class GetSpotifyActivitiesCommand extends Command
{
    protected $signature = 'spotify:activities';

    protected function devices(string $bearerToken)
    {
        $userDevices = SpotifyService::getDevices($bearerToken);

        if (empty($userDevices)) {
            return [];
        }

        $devices = [];

        array_map(function ($device) use (&$devices) {
            $devices[] = [
                'type' => $device->type,
                'id' => $device->id,
                'name' => $device->name,
                'is_active' => $device->is_active,
                'volume' => $device->volume_percent,
            ];
        }, $userDevices->devices);

        return $devices;
    }

    protected function history(string $bearerToken)
    {
        $recentlyPlayed = SpotifyService::getRecentlyPlayed($bearerToken);

        if (empty($recentlyPlayed)) {
            return [];
        }

        Carbon::setLocale('pl');

        $history = [];

        array_map(function ($item) use (&$history) {
            $artists = [];

            array_map(function ($artist) use (&$artists) {
                $artists[] = $artist->name;
            }, $item->track->artists);

            $history[] = (object) [
                'type' => 'history',
                'id' => $item->track->id,
                'name' => $item->track->name,
                'artists' => implode(', ', $artists),
                'played_at' => $item->played_at,
                'played_ago' => (new Carbon($item->played_at))->diffForHumans(),
                'images' => $item->track->album->images,
                'preview' => $item->track->preview_url,
            ];
        }, $recentlyPlayed->items);

        return $history;
    }

    protected function state(string $bearerToken)
    {
        $response = SpotifyService::getPlayerState($bearerToken);

        if (empty($response)) {
            return [];
        }

        $progress = $response->progress_ms;
        $id = $response->item->id;
        $name = $response->item->name;
        $artists = [];
        array_map(function ($artist) use (&$artists) {
            $artists[] = $artist->name;
        }, $response->item->artists);
        $duration = $response->item->duration_ms;
        $is_playing = $response->is_playing;
        $images = $response?->item?->album?->images;
        $preview = $response?->item?->preview_url;

        return [
            'type' => 'current',
            'id' => $id,
            'name' => $name,
            'artists' => implode(', ', $artists),
            'duration' => $duration,
            'progress' => $progress,
            'is_playing' => $is_playing,
            'images' => $images,
            'preview' => $preview,
        ];
    }

    public function handle()
    {
        $authors = DB::table('authors')->whereNotNull('spotify_user_id')->get();

        foreach ($authors as $author) {
            print "Aktualizacja aktywności dla autora: {$author->name}\n";
            $this->updateSpotifyActivity($author->id);
        }
    }

    protected function updateSpotifyActivity(string $authorId)
    {
        $author = DB::table('authors')->where('id', $authorId)->first();

        if (empty($author)) {
            print "Autor nie został znaleziony\n";
            logger()->error('Autor nie został znaleziony');
            return;
        }

        if (empty($author->spotify_user_id)) {
            print "Autor nie jest powiązany z kontem Spotify\n";
            logger()->error('Autor nie jest powiązany z kontem Spotify');
            return;
        }

        $spotifyUser = SpotifyUser::where('user_id', $author->spotify_user_id)->first();

        if (empty($spotifyUser)) {
            print "Konto Spotify nie zostało znalezione\n";
            logger()->error('Konto Spotify nie zostało znalezione');
            return;
        }

        try {
            $bearerToken = $spotifyUser->access_token;
            $refreshedAt = $spotifyUser->token_refreshed_at;

            if (empty($refreshedAt) || Carbon::parse($refreshedAt)->addSeconds(1800)->isPast()) {
                $response = SpotifyService::getRefreshToken(env('SPOTIFY_CLIENT_ID'), env('SPOTIFY_CLIENT_SECRET'), $spotifyUser->refresh_token);
                
                $spotifyUser->update([
                    'access_token' => $response->access_token,
                    'token_refreshed_at' => Carbon::now(),
                    'scope' => $response->scope,
                ]);
            }

            $devices = $this->devices($bearerToken);
            $state = $this->state($bearerToken);
            $history = $this->history($bearerToken);

            $player = [];

            if (!empty($state)) {
                $player = array_merge($player, [$state]);
            }

            if (is_array($history)) {
                $player = array_merge($player, $history);
            }

            $responseToSave = [
                'devices' => $devices,
                'player' => $player,
            ];
        } catch (Throwable $t) {
            print $t->getMessage();
            logger()->error($t->getMessage());
            return;
        }

        DB::table('authors')
            ->where('id', $authorId)
            ->update([
                'spotify_activity' => json_encode($responseToSave),
            ]);
    }
}
