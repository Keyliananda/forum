<?php

namespace App\Http\Controllers\Api\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DiscussionExportController extends Controller
{
    public function __invoke(Discussion $discussion): JsonResponse
    {
        $discussion->load(['topic', 'positions.claims.arguments.evidence', 'resultSnapshots']);
        $positions = [];

        foreach ($discussion->positions as $position) {
            $claims = [];

            foreach ($position->claims as $claim) {
                $arguments = [];

                foreach ($claim->arguments as $argument) {
                    $evidenceItems = [];

                    foreach ($argument->evidence as $evidence) {
                        $evidenceItems[] = [
                            'title' => $evidence->title,
                            'url' => $evidence->url,
                            'status' => $evidence->verification_status,
                        ];
                    }

                    $arguments[] = [
                        'type' => $argument->type,
                        'body' => $argument->body,
                        'evidence' => $evidenceItems,
                    ];
                }

                $claims[] = [
                    'statement' => $claim->statement,
                    'arguments' => $arguments,
                ];
            }

            $positions[] = [
                'title' => $position->title,
                'claims' => $claims,
            ];
        }

        return response()->json([
            'schema' => 'forum.canonical.v1',
            'discussion' => [
                'id' => $discussion->id,
                'slug' => $discussion->slug,
                'title' => $discussion->title,
                'core_question' => $discussion->core_question,
                'topic_path' => $discussion->topic->path,
                'positions' => $positions,
            ],
        ]);
    }
}
