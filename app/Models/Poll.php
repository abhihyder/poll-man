<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Poll extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uid = Str::random();
        });
    }

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'poll_id');
    }
}
