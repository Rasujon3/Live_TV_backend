<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tv extends Model
{
    protected $fillable = [
        'name',
        'channel_id',
        'img_url',
        'status',
    ];
}
