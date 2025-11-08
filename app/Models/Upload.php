<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'amount',
        'card_name',
        'description',
        'file_path',
        'original_name',
    ];

    /**
     * Relationship: Each upload belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
