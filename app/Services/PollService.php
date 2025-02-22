<?php

namespace App\Services;

use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollService
{

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

        return $polls->map(function ($poll) {
            return $this->pollFormat($poll);
        })->toArray();
    }

    public function store(array $attributes): array
    {
        return DB::transaction(function () use ($attributes) {
            $poll = Poll::create([
                'question' => $attributes['question'],
                'ends_at' => $attributes['ends_at'],
            ]);

            $options = array_map(fn($option) => ['title' => $option, 'poll_id' => $poll->id], $attributes['options']);

            $poll->options()->createMany($options);

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

    public function vote(Request $request)
    {
        $ip = $request->ip();
        $userId = Auth::id();
        return Vote::create(
            [
                'poll_id' => $request->poll_id,
                'option_id' => $request->option_id,
                'ip' => $ip,
                'user_id' => $userId
            ]
        );
    }

    public function delete(int $id): bool
    {
        return Poll::destroy($id);
    }

    public function isVoted(int $pollId, string $ip, ?int $userId = null): bool
    {
        return Vote::where('poll_id', $pollId)->where(function ($query) use ($ip, $userId) {
            $query->where('ip', $ip);
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })->exists();
    }

    private function pollQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Poll::with(['options' => function ($query) {
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
            'options' => $poll->options->map(function ($option) {
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
