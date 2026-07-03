<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\DiscussionReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscussionReply>
 */
class DiscussionReplyFactory extends Factory
{
    protected $model = DiscussionReply::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discussion_id' => Discussion::factory(),
            'parent_id' => null,
            'author_id' => User::factory(),
            'body' => fake()->paragraph(),
            'status' => 'visible',
            'hidden_at' => null,
        ];
    }
}
