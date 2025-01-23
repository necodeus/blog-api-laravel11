<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotifyPlaylist
 * 
 * @property bool $collaborative
 * @property string $description
 * @property int $followers
 * @property string $image_uri
 * @property string $name
 * @property int $user_id
 * @property bool $public
 * @property string $playlist_id
 * @property array $tracks
 * @property int $total_tracks
 */
class SpotifyPlaylist extends Model
{
    protected $fillable = [
        'collaborative',
        'description',
        'followers',
        'image_uri',
        'name',
        'user_id',
        'public',
        'playlist_id',
        'tracks',
        'total_tracks',
    ];

    protected $casts = [
        'tracks' => 'collection', // JSON -> Collection [playlist_tracks: id, added_add, added_by]
        'collaborative' => 'boolean',
        'public' => 'boolean',
    ];
}
