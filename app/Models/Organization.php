<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'email'];

    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
