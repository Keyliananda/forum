<?php

namespace App\Http\Controllers\Api\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DiscussionShowController extends Controller
{
    public function __invoke(Discussion $discussion): JsonResponse
    {
        $discussion->load(['topic', 'positions.claims.arguments']);
        $positions = [];

        foreach ($discussion->positions as $position) {
            $claims = [];

            foreach ($position->claims as $claim) {
                $arguments = [];

                foreach ($claim->arguments as $argument) {
                    $arguments[] = [
                        'id' => $argument->id,
                        'type' => $argument->type,
                        'body' => $argument->body,
                    ];
                }

                $claims[] = [
                    'id' => $claim->id,
                    'statement' => $claim->statement,
                    'arguments' => $arguments,
                ];
            }

            $positions[] = [
                'id' => $position->id,
                'title' => $position->title,
                'claims' => $claims,
            ];
        }

        return response()->json([
            'data' => [
                'id' => $discussion->id,
                'title' => $discussion->title,
                'slug' => $discussion->slug,
                'core_question' => $discussion->core_question,
                'topic' => [
                    'id' => $discussion->topic->id,
                    'name' => $discussion->topic->name,
                    'path' => $discussion->topic->path,
                ],
                'positions' => $positions,
            ],
        ]);
    }
}
