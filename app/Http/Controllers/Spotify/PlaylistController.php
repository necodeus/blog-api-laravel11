<?php

namespace App\Http\Controllers\Spotify;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

use App\Http\Controllers\Controller;

use App\Models\Spotify\SpotifyArtist;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyTrack;
use App\Models\Spotify\SpotifyUser;
use App\Models\Spotify\SpotifyPlaylist;

class PlaylistController extends Controller
{
    public function get(Request $request, Response $response, string $id, ?string $snapshot = 'latest')
    {
        try {
            $q = SpotifyPlaylist::orderBy('id', 'desc');
            $q->distinct('playlist_id');
            $q->where('playlist_id', '=', $id);
            if ($snapshot && $snapshot !== 'latest') {
                $q->where('id', '=', $snapshot);
            }
            $playlist = $q->first();

            if (is_null($playlist)) {
                throw new Exception('Playlista nie została znaleziona');
            }

            $artists = new Collection([]);
            $albums = new Collection([]);
            $users = new Collection([]);
            $users->push($playlist->user_id);

            $playlist->playlist_tracks = collect($playlist->tracks)->sortBy([
                ['added_at', 'desc'],
                // ['id', 'desc'],
            ]);

            $users->push($playlist->tracks->pluck('added_by')->all());

            $playlist->tracks = SpotifyTrack::find($playlist->tracks->pluck('id'))
                ->each(function (SpotifyTrack $x) use ($playlist) {
                    $hours = gmdate("H", round($x->duration_ms / 1000));
                    $minutes = gmdate("i", round($x->duration_ms / 1000));
                    $seconds = gmdate("s", round($x->duration_ms / 1000));
                    if ($x->duration_ms >= 3600000) {
                        $x->duration = (int) $hours . ":" . $minutes . ":" . $seconds;
                    } elseif ($x->duration_ms >= 60000) {
                        $x->duration = (int) $minutes . ":" . $seconds;
                    } else {
                        $x->duration = (int) $seconds;
                    }

                    // $x->new_seen_in_playlists = SpotifyPlaylist::whereIn('id', function($query) {
                    //     $query->from('spotify_playlists')->groupBy('playlist_id')->selectRaw('MAX(id)');
                    // })
                    // ->where('id', '!=', $playlist->id)
                    // ->where('tracks', 'LIKE', '%' . "\"id\":$x->id," . '%')
                    // ->pluck('name');
    
                    // Sprawdź, w jakich innych playlistach znajduje się ten utwór
                });

            $artists->push($playlist->tracks->pluck('artists')->all());
            $albums->push($playlist->tracks->pluck('album_id')->all());

            $playlist->artists = SpotifyArtist::find($artists->flatten()->unique(), ['id', 'artist_id', 'name']);
            $playlist->albums = SpotifyAlbum::find($albums->flatten()->unique(), ['id', 'album_id', 'name', 'image_uri']);
            $playlist->users = SpotifyUser::find($users->flatten()->unique());

            $playlist->snapshots = SpotifyPlaylist::orderBy('id', 'DESC')
                ->where('playlist_id', '=', $id)
                ->get(['id', 'created_at']);

            $playlist->current_snapshot = $snapshot;

            return $response
                ->setContent($playlist)
                ->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return $response
                ->setContent([
                    'error' => $e->getMessage(),
                ])
                ->header('Content-Type', 'application/json');
        }
    }
}
