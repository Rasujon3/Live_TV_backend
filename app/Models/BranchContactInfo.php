<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchContactInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'contact_no'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
