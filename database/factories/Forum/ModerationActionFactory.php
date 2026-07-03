<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\ModerationAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModerationAction>
 */
class ModerationActionFactory extends Factory
{
    protected $model = ModerationAction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $argument = Argument::factory()->create();

        return [
            'moderator_id' => User::factory(),
            'actionable_type' => Argument::class,
            'actionable_id' => $argument->id,
            'action' => 'hide',
            'public_reason' => fake()->sentence(),
            'internal_note' => fake()->sentence(),
            'policy_version' => 'w5.1',
        ];
    }
}
