<?php

namespace App\Http\Controllers;

use App\Events\VoteUpdated;
use App\Http\Requests\PollCreateRequest;
use App\Http\Requests\VoteRequest;
use App\Services\PollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    public function __construct(protected PollService $pollService)
    {
        //
    }

    /**
     * Show the public poll index
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $polls = $this->pollService->index($request->all(), true);

        return view('poll.index', ['polls' => $polls]);
    }

    public function dashboard(Request $request)
    {
        $polls = $this->pollService->index($request->all());

        return view('dashboard', ['polls' => $polls]);
    }

    public function store(PollCreateRequest $request)
    {
        try {
            $poll = $this->pollService->store($request->all());
            return response()->json(['success' => true, 'message' => 'Poll Created!', 'poll' => $poll], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, string $uid)
    {
        $poll = $this->pollService->show($uid);
        $isVoted = $this->pollService->isVoted($poll['id'], $request->ip(), Auth::id());
        return view('poll.view', ['poll' => $poll, 'isVoted' => $isVoted]);
    }

    public function vote(VoteRequest $request)
    {
        try {
            $vote = $this->pollService->vote($request, Auth::id());
            broadcast(new VoteUpdated($vote->poll_id));
            return response()->json(['success' => true, 'message' => 'Poll Voted!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->pollService->delete($id);
            return response()->json(['success' => true, 'message' => 'Poll Deleted!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
