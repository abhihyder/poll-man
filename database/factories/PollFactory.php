<?php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
{
    protected $model = Poll::class;

    public function definition()
    {
        return [
            'uid' => Str::random(),
            'question' => $this->faker->sentence,
            // 'ends_at' => $this->faker->dateTimeBetween('+1 week', '+2 weeks')
        ];
    }
}
