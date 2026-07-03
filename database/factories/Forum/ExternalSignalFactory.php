<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Social\Models\ExternalSignal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalSignal>
 */
class ExternalSignalFactory extends Factory
{
    protected $model = ExternalSignal::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discussion_id' => Discussion::factory(),
            'platform' => 'instagram',
            'label' => 'Instagram Stimmung',
            'up_count' => fake()->numberBetween(0, 500),
            'down_count' => fake()->numberBetween(0, 100),
            'comment_count' => fake()->numberBetween(0, 50),
            'source_url' => fake()->url(),
            'captured_at' => now(),
        ];
    }
}
