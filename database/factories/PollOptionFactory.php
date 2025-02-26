<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PollOption>
 */
class PollOptionFactory extends Factory
{
    protected $model = PollOption::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'poll_id' => Poll::factory(),
            'title' => $this->faker->sentence,
        ];
    }
}
