<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'branch_id',
        'rating',
    ];

    /**
     * Each rating belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
