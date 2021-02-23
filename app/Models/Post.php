<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'code', 'thumbnail', 'content', 'author_id', 'author_username', 'like_count', 'positive', 'neutral', 'negative'];
    protected $dates = ['date'];
    protected $appends = ['formatted_date'];

    public function getFormattedDateAttribute()
    {
        return isset($this->date) && !is_null($this->date) ? $this->date->format('d-m-Y') : null;
    }
}
