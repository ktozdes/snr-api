<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialNetworkUser extends Model
{
    use HasFactory;
    protected $fillable = ['insta_id', 'username', 'posts_count', 'comments_count'];
}
