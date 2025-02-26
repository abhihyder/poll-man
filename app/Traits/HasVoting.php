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
    public function isVoted(int $pollId, ?string $deviceId, ?string $sessionId, ?string $fingerprint, ?int $userId = null): bool
    {
        return Vote::where('poll_id', $pollId)->where(function ($query) use ($deviceId, $sessionId, $fingerprint, $userId) {
            if ($deviceId) {
                $query->orWhere('device_id', $deviceId);
            }
            if ($sessionId) {
                $query->orWhere('session_id', $sessionId);
            }
            if ($fingerprint) {
                $query->orWhere('fingerprint', $fingerprint);
            }
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })->exists();
    }
}
