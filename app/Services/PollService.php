<?php

namespace App\Services;

use App\Models\Poll;
use App\Models\Vote;
use App\Traits\HasVoting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class PollService
{

    use HasVoting;

    public function index(array $attributes = [], bool $public = false): array
    {
        $page = Arr::get($attributes, 'page', 1);
        $limit = Arr::get($attributes, 'limit', 10);
        $offSet = ($page - 1) * $limit;

        $polls = $this->pollQuery()
            ->when($public, fn($query) => $query->where('ends_at', '>', now()))
            ->offset($offSet)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        return $polls->map(fn($poll) => $this->pollFormat($poll))->toArray();;
    }

    public function store(array $attributes): array
    {
        return DB::transaction(function () use ($attributes) {
            $poll = Poll::create([
                'question' => $attributes['question'],
                'ends_at' => $attributes['ends_at'],
            ]);

            $options = array_map(fn($option) => ['title' => $option, 'poll_id' => $poll->id], $attributes['options']);

            $poll->pollOptions()->createMany($options);

            return $this->pollFormat($poll);
        });
    }

    public function show(string $uid): array
    {
        $poll = $this->pollQuery()
            ->whereUid($uid)
            ->first();

        return $this->pollFormat($poll);
    }

    public function getPollById(int $id)
    {
        $poll = $this->pollQuery()->find($id);

        return $this->pollFormat($poll);
    }

    public function vote(Request $request, ?int $userId = null)
    {
        $deviceId = $request->cookie('device_id') ?? Str::uuid()->toString();
        Cookie::queue('device_id', $deviceId, 60 * 24 * 30); // Store for 30 days

        $sessionId = session()->getId();
        $fingerprint = $request->input('fingerprint');

        return Vote::create([
            'poll_id' => $request->poll_id,
            'poll_option_id' => $request->poll_option_id,
            'user_id' => $userId,
            'device_id' => $deviceId,
            'session_id' => $sessionId,
            'fingerprint' => $fingerprint,
        ]);
    }

    public function delete(int $id): bool
    {
        return Poll::destroy($id);
    }

    private function pollQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Poll::with(['pollOptions' => function ($query) {
            $query->select('id', 'poll_id', 'title')
                ->withCount('votes');
        }])
            ->withCount(['votes']);
    }

    private function pollFormat(Poll $poll): array
    {
        return [
            'id' => $poll->id,
            'uid' => $poll->uid,
            'question' => $poll->question,
            'options' => $poll->pollOptions->map(function ($option) {
                return [
                    'id' => $option->id,
                    'title' => $option->title,
                    'total_votes' => $option->votes_count ?? 0,
                ];
            })->toArray(),
            'ends_at' => $poll->ends_at,
            'isExpired' => Carbon::now()->gt($poll->ends_at),
            'total_votes' => $poll->votes_count ?? 0,
        ];
    }
}
