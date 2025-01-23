<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotifyTrack
 * 
 * @property string $track_id
 * @property string $name
 * @property int $duration_ms
 * @property bool $explicit
 * @property int $popularity
 * @property string $preview_uri
 * @property string $artists
 * @property string $album_id
 */
class SpotifyTrack extends Model
{
    protected $fillable = [
        'track_id',
        'name',
        'duration_ms',
        'explicit',
        'popularity',
        'preview_uri',
        'artists',
        'album_id',
    ];

    protected $casts = [
        'artists' => 'array',
    ];

    public const CREATED_AT = null;

    public const UPDATED_AT = null;
}
