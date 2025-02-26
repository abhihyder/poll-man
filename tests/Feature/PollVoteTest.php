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
     */
    public function testUserCanVoteInPoll()
    {
        $user = User::factory()->create();

        $poll = Poll::factory()
            ->has(PollOption::factory()->count(3))
            ->create(['ends_at' => now()->addDays(1)]);

        // Simulate a logged-in user
        $this->actingAs($user);


        $response = $this->postJson(route('poll.vote'), [
            'poll_id' => $poll->id,
            'poll_option_id' => $poll->pollOptions->first()->id,
            'device_id' => 'test-device-id',
            'session_id' => 'test-session-id',
            'fingerprint' => 'test-fingerprint',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Poll Voted!']);
    }

    /**
     * Test that a user cannot vote more than once in the same poll.
     *
     * This test ensures that if a user attempts to vote a second time in the same poll,
     * the application returns a 422 status code and provides an appropriate error message.
     * This behavior prevents duplicate votes from being recorded.
     */
    public function testUserCannotVoteMoreThanOnce()
    {
        $user = User::factory()->create();

        $poll = Poll::factory()
            ->has(PollOption::factory()->count(3))
            ->create(['ends_at' => now()->addDays(1)]);

        // Simulate a logged-in user
        $this->actingAs($user);

        // First vote request with device_id and session_id
        $this->postJson(route('poll.vote'), [
            'poll_id' => $poll->id,
            'poll_option_id' => $poll->pollOptions->first()->id,
            'device_id' => 'test-device-id',
            'session_id' => 'test-session-id',
            'fingerprint' => 'test-fingerprint',
        ]);

        // Second vote request with the same identifiers
        $response = $this->postJson(route('poll.vote'), [
            'poll_id' => $poll->id,
            'poll_option_id' => $poll->pollOptions->first()->id,
            'device_id' => 'test-device-id',
            'session_id' => 'test-session-id',
            'fingerprint' => 'test-fingerprint',
        ]);

        // Assert the response
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'You have already voted for this poll.',
                'errors' => [
                    'poll_id' => ['You have already voted for this poll.'],
                ],
            ]);
    }
}
