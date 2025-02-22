<?php

namespace App\Traits;

use App\Models\Vote;

trait HasVoting
{
    /**
     * Determine if the user with the given IP address and optionally the given user ID
     * has already voted for the given poll.
     *
     * @param int $pollId
     * @param string $ip
     * @param int|null $userId
     * @return bool
     */
    public function isVoted(int $pollId, string $ip, ?int $userId = null): bool
    {
        return Vote::where('poll_id', $pollId)->where(function ($query) use ($ip, $userId) {
            $query->where('ip', $ip);
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })->exists();
    }
}
