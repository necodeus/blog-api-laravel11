<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotifyAlbum
 * 
 * @property string $type
 * @property string $album_id
 * @property string $name
 * @property string $artists
 * @property string $image_uri
 * @property string $tracks
 * @property int $total_tracks
 * @property string $release_date
 */
class SpotifyAlbum extends Model
{
    protected $fillable = [
        'type',
        'album_id',
        'name',
        'artists',
        'image_uri',
        'tracks',
        'total_tracks',
        'release_date',
    ];

    protected $casts = [
        'tracks' => 'array',
        'artists' => 'array',
    ];

    public const CREATED_AT = null;

    public const UPDATED_AT = null;
}
