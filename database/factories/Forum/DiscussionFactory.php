<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Space;
use App\Forum\Discourse\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Discussion>
 */
class DiscussionFactory extends Factory
{
    protected $model = Discussion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $topic = Topic::factory()->create();
        $title = fake()->unique()->sentence(6);

        return [
            'space_id' => $topic->space_id,
            'topic_id' => $topic->id,
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'core_question' => rtrim(fake()->sentence(8), '.').'?',
            'body' => fake()->paragraph(),
            'status' => 'open',
            'governance_profile' => 'open_democratic',
            'locked_at' => null,
            'last_replied_at' => now(),
        ];
    }

    public function forTopic(Topic $topic): static
    {
        return $this->state(fn (array $attributes): array => [
            'space_id' => $topic->space_id,
            'topic_id' => $topic->id,
        ]);
    }

    public function forSpace(Space $space): static
    {
        return $this->state(fn (array $attributes): array => [
            'space_id' => $space->id,
        ]);
    }
}
