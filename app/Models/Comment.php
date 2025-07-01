<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Allow mass-assignment
    protected $fillable = [
        'branch_id',
        'author',
        'comment',
    ];

    // Relationship: each comment belongs to one branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
