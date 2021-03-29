<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'post_id', 'like_count', 'code', 'author_username', 'content', 'process_type', 'positive', 'neutral', 'negative', 'words', 'post'];
    protected $dates = ['date'];
    protected $appends = ['formatted_date'];

    public function getFormattedDateAttribute()
    {
        return isset($this->date) && !is_null($this->date) ? $this->date->format('d-m-Y H:m:s') : null;
    }
}
