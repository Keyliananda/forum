<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\DiscussionResultSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscussionResultSnapshot>
 */
class DiscussionResultSnapshotFactory extends Factory
{
    protected $model = DiscussionResultSnapshot::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discussion_id' => Discussion::factory(),
            'profile_key' => 'balanced',
            'weights' => [
                'argument_quality' => 25,
                'source_quality' => 25,
                'community' => 20,
                'reputation' => 20,
                'external_signals' => 10,
            ],
            'position_scores' => [],
            'breakdown' => [],
            'computed_at' => now(),
        ];
    }
}
