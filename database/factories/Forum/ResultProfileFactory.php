<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\ResultProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResultProfile>
 */
class ResultProfileFactory extends Factory
{
    protected $model = ResultProfile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'key' => null,
            'name' => fake()->words(2, true),
            'weights' => [
                'argument_quality' => 25,
                'source_quality' => 25,
                'community' => 20,
                'reputation' => 20,
                'external_signals' => 10,
            ],
        ];
    }
}
