<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'code', 'thumbnail', 'content', 'author_id', 'author_username', 'like_count', 'positive', 'neutral', 'negative'];
    protected $dates = ['date', 'date_update'];
    protected $appends = ['formatted_date', 'formatted_updated_date'];

    public function getFormattedDateAttribute()
    {
        return isset($this->date) && !is_null($this->date) ? $this->date->format('d-m-Y') : null;
    }
    public function getFormattedUpdatedDateAttribute()
    {
        return isset($this->date_update) && !is_null($this->date_update) ? $this->date_update->format('d-m-Y') : null;
    }
}
