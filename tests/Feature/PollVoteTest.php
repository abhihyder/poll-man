<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollVoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can vote in a poll.
     *
     */
    public function testUserCanVoteInPoll()
    {
        $user = User::factory()->create();

        $poll = Poll::factory()
            ->has(PollOption::factory()->count(3))
            ->create(['ends_at' => now()->addDays(1)]);

        // Simulate a logged-in user
        $this->actingAs($user);

        // Make a vote request
        $response = $this->postJson(route('poll.vote'), [
            'poll_id' => $poll->id,
            'option_id' => $poll->options->first()->id,
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Poll Voted!']);

        // Assert the vote was recorded
        $this->assertDatabaseHas('votes', [
            'poll_id' => $poll->id,
            'option_id' => $poll->options->first()->id,
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Test that a user cannot vote more than once in the same poll.
     *
     * This test ensures that if a user attempts to vote a second time in the same poll,
     * the application returns a 422 status code and provides an appropriate error message.
     * This behavior prevents duplicate votes from being recorded.
     *
     */

    public function testUserCannotVoteMoreThanOnce()
    {
        $user = User::factory()->create();

        $poll = Poll::factory()
            ->has(PollOption::factory()->count(3))
            ->create(['ends_at' => now()->addDays(1)]);

        // Simulate a logged-in user
        $this->actingAs($user);

        // First vote request
        $this->postJson(route('poll.vote'), [
            'poll_id' => $poll->id,
            'option_id' => $poll->options->first()->id,
        ]);

        // Second vote request
        $response = $this->postJson(route('poll.vote'), [
            'poll_id' => $poll->id,
            'option_id' => $poll->options->first()->id,
        ]);

        // // Assert the response
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'You have already voted for this poll.',
                'errors' => [
                    'poll_id' => ['You have already voted for this poll.'],
                ],
            ]);
    }
}
