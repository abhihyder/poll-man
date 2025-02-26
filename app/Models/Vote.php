<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    protected $guarded = [];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function pollOption(): BelongsTo
    {
        return $this->belongsTo(PollOption::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
