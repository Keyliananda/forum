# Package Readiness Notes

This app is still app-first. The current package boundary is prepared by keeping forum code in explicit domain namespaces:

- `App\Forum\Discourse`
- `App\Forum\Reputation`
- `App\Forum\Social`
- `App\Http\Controllers\Api\Forum`

Future package split:

- `forum-core`: models, migrations, policies, actions, scoring, reputation, governance, evidence.
- `forum-livewire`: Blade/Livewire/Flux UI.
- `forum-api`: API controllers, resources, OpenAPI docs, Sanctum abilities.
- `forum-connectors`: import/export, social signal providers, external platform clients.
- `forum-testing`: factories, test helpers, demo seeders.

Extraction rules:

- Core must not depend on Livewire or Flux.
- Connector code must go through contracts and must not mutate core models directly.
- Scores and snapshots must be reproducible from raw events.
- Public APIs must use versioned routes and stable canonical JSON.
- Demo app should eventually consume local path packages like an external Laravel app.
