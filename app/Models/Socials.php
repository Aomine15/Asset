<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socials extends Model
{
    protected $fillable = ['user_id', 'google_id', 'facebook_id', 'github_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
