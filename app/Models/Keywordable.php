<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keywordable extends Model
{
    use HasFactory;
    protected $fillable = ['keyword_id', 'keywordable_id', 'keywordable_type'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
