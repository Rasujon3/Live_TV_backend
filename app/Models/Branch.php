<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location_url'
    ];
    protected $appends = ['average_rating'];
    protected $hidden = ['ratings'];
    public function contactInfos()
    {
        return $this->hasMany(BranchContactInfo::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function getAverageRatingAttribute()
    {
        if ($this->ratings->isEmpty()) {
            return null;
        }

        return (float) ($this->ratings->avg('rating'));
    }
}
