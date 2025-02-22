<?php

namespace Database\Seeders;

use App\Models\Poll;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $poll = Poll::create(
            [

                'question' => 'What is your favorite color?',
                'ends_at' => now()->addDays(1)
            ]
        );

        $poll->options()->createMany([
            ['title' => 'Red'],
            ['title' => 'Green'],
            ['title' => 'Blue'],
            ['title' => 'Yellow'],
        ]);
    }
}
