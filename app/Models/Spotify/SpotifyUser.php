<?php

namespace App\Models\Spotify;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotifyUser
 * 
 * @property string $name
 * @property string $user_id
 * @property string $access_token
 * @property string $refresh_token
 * @property string $scope
 * @property int $followers
 * @property string $avatar_url
 * @property string $type
 * @property string $token_refreshed_at
 */
class SpotifyUser extends Model
{
    protected $fillable = [
        'name',
        'user_id', 
        'access_token',
        'refresh_token',
        'scope',
        'followers',
        'avatar_url',
        'type',
        'token_refreshed_at',
    ];

    public $timestamps = true;
}
