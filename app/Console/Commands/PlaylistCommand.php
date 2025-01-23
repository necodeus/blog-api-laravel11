<?php

namespace App\Commands;

use Illuminate\Console\Command;

use App\Models\Spotify\SpotifyArtist;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyTrack;
use App\Models\Spotify\SpotifyUser;
use App\Models\Spotify\SpotifyPlaylist;

use App\Services\Spotify\SpotifyService;

// WARN his is very old code, it's not working anymore
class PlaylistCommand extends Command
{
    protected $signature = 'playlist:fetch';

    protected $description = 'Fetch playlist from Spotify';

    public function handle()
    {
        // TODO Get playlist by user id and playlist id (user has to be logged in)

        $pppppp = "78UPb7XeovLpM5UiqTfYem";

        $res = SpotifyService::getPlaylist("", $pppppp);

        SpotifyService::getRefreshToken("", "", "");
        SpotifyService::getPlaylist("", $pppppp);

        $owner = SpotifyUser::firstOrCreate([
            'user_id' => $res->owner->id,
        ], [
            'name' => $res->owner->display_name,
            'user_id' => $res->owner->id,
        ]);

        $pages = ceil($res->tracks->total / $res->tracks->limit);
        $all_tracks = $res->tracks->items;
        $current_page_tracks = $res->tracks->items;

        $tracks = [];

        for ($i = 0; $i < $pages; $i++) {
            if ($i > 0) {
                $raw_tracks = SpotifyService::getTracks($pppppp, 100 * $i, 100);
                $all_tracks = array_merge($all_tracks, $raw_tracks->items);
                $current_page_tracks = $raw_tracks->items;
            }

            foreach ($current_page_tracks as $track) {
                $track_artists = [];
                foreach ($track->track->artists as $artist) {
                    $track_artists[] = SpotifyArtist::firstOrCreate([
                        'artist_id' => $artist->id,
                    ], [
                        'artist_id' => $artist->id,
                        'name' => $artist->name,
                    ])->id;
                }

                $album = SpotifyAlbum::firstOrCreate([
                    'album_id' => $track->track->album->id,
                ], [
                    'album_id' => $track->track->album->id,
                    'type' => $track->track->album->album_type,
                    'total_tracks' => $track->track->album->total_tracks,
                    'release_date' => $track->track->album->release_date,
                    'name' => $track->track->album->name,
                    'image_uri' => $track->track->album->images[0]->url,
                    'artists' => '',
                    'tracks' => '',
                ])->id;

                $adder = SpotifyUser::firstOrCreate([
                    'user_id' => $track->added_by->id,
                ], [
                    'name' => '',
                    'user_id' => $track->added_by->id,
                ]);

                $t = SpotifyTrack::firstOrCreate([
                    'track_id' => $track->track->id,
                ], [
                    'track_id' => $track->track->id,
                    'name' => $track->track->name,
                    'duration_ms' => $track->track->duration_ms,
                    'explicit' => $track->track->explicit,
                    'popularity' => $track->track->popularity,
                    'preview_uri' => $track->track->preview_url,
                    'artists' => $track_artists,
                    'album_id' => $album,
                    'user_id' => null,
                ]);

                $tracks[] = [
                    'id' => $t->id,
                    'added_at' => $track->added_at,
                    'added_by' => $adder->id,
                ];
            }
        }

        $playlist_id = explode(":", $res->uri)[2];

        $playlist = SpotifyPlaylist::orderBy('id', 'DESC')
            ->where('playlist_id', '=', $playlist_id)
            ->distinct('playlist_id')
            ->first();

        if (
            $playlist === null
            || (md5(json_encode($playlist->tracks)) != md5(json_encode($tracks)) && is_null($playlist) === false)
            || ($playlist->public != $res->public && is_null($playlist) === false)
            || ($playlist->name != $res->name && is_null($playlist) === false)
            || ($playlist->image_uri != $res->images[0]->url && is_null($playlist) === false)
            || ($playlist->followers != $res->followers->total && is_null($playlist) === false)
            || ($playlist->description != $res->description && is_null($playlist) === false)
            || ($playlist->collaborative != $res->collaborative && is_null($playlist) === false)
        ) {

            SpotifyPlaylist::create([
                'playlist_id' => explode(":", $res->uri)[2],
                'collaborative' => $res->collaborative,
                'description' => $res->description,
                'followers' => $res->followers->total,
                'image_uri' => $res->images[0]->url,
                'name' => $res->name,
                'public' => $res->public,
                'tracks' => $tracks,
                'total_tracks' => $res->tracks->total,
                'user_id' => $owner->id,
            ]);
        }
    }
}
