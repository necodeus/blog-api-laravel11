<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotifyArtist
 * 
 * @property string $name
 * @property string $artist_id
 */
class SpotifyArtist extends Model
{
    protected $fillable = [
        'name',
        'artist_id',
    ];

    public const CREATED_AT = null;

    public const UPDATED_AT = null;
}
