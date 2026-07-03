# Domain Glossary

This glossary fixes the shared product language for the structured discourse forum. It is intentionally concise and implementation-facing; every term should map cleanly to future models, actions, policies, events, or UI components.

## Space

A Space is a discourse room. The default Space is public and globally readable. Later Spaces may be private, invite-only, unlisted, or tied to a product context such as a Pianouniverse course, piece, teacher group, or community.

## Topic

A Topic is a navigational subject area. Topics can be nested to create a hierarchy such as `Feminismus -> Sprache -> Gendern` or `Energie -> Waermepumpen -> Altbau`. Topics are context and navigation; they are not the debate itself.

## Discussion

A Discussion is a concrete debate around one core question. A Discussion belongs to a Space and at least one Topic. It contains Positions, Claims, Arguments, Evidence, quality signals, governance settings, and result snapshots.

## Core Question

The Core Question is the precise question a Discussion tries to answer. It should be narrow enough to evaluate but broad enough to allow competing Positions.

Example: `Sind Waermepumpen im Gebaeudebestand sinnvoll?`

## Position

A Position is a possible answer to the Core Question. Positions collect Claims and Arguments. A Position can be supported, challenged, merged with another Position, or evaluated through Result Profiles.

## Claim

A Claim is an atomic, reusable statement. Claims can appear across multiple Positions and Discussions. A Claim can be factual, causal, normative, definitional, predictive, or interpretive.

Example: `Waermepumpen sind effizient.`

## Argument

An Argument connects Claims, Positions, Evidence, and other Arguments. It can support, oppose, rebut, undercut, or contextualize another node in the discourse.

## Argument Link

An Argument Link is a graph edge between two argument nodes or Claims. Link types include `supports`, `attacks`, `rebuts`, `undercuts`, `requires`, `qualifies`, `duplicates`, and `related`.

## Evidence

Evidence is a cited source or source excerpt attached to a Claim, Argument, Argument Link, or Position. Evidence is not automatically true; it becomes useful when its relevance, accuracy, and source quality are verifiable.

## Source Verification

Source Verification records whether Evidence is reachable, correctly quoted, relevant, outdated, disputed, inaccessible, or contradicted. Verification can come from the community, moderators, experts, automated metadata checks, or external fact-check connectors.

## Challenge

A Challenge is a structured objection against a Claim, Argument, Evidence item, or Argument Link. Challenges can target logic, relevance, source usage, scope, assumptions, or missing context.

## Rebuttal

A Rebuttal is a response to a Challenge or opposing Argument. A good Rebuttal does not simply disagree; it explains why the challenged statement, link, or source still holds or should be narrowed.

## Argument Quality Vote

An Argument Quality Vote evaluates an Argument by quality dimensions such as clarity, relevance, logical validity, source usage, fairness, and rebuttal strength. It is separate from agreement or popularity.

## Claim Robustness

Claim Robustness is an explainable snapshot of how well a Claim holds under Evidence, attacks, defenses, source quality, open Challenges, and verification state. It is a derived score, not a truth declaration.

## Governance Profile

A Governance Profile defines how a Discussion is run. Examples include `open_democratic`, `moderated`, `expert_reviewed`, `reputation_weighted`, `private_jury`, and `hybrid`.

## Reputation

Reputation is earned through useful contributions such as strong Arguments, reliable Evidence, fair Challenges, accepted Rebuttals, and constructive moderation signals. Reputation is scoped globally, per Space, per Topic, or per Discussion.

## Result Profile

A Result Profile defines how a Discussion result is weighted. It can prioritize dimensions such as Argument quality, source quality, community consensus, expert review, Reputation, recency, Claim Robustness, or External Signal inputs.

## External Signal

An External Signal is an imported or manually recorded signal from outside the forum, such as Instagram likes, Reddit comments, YouTube engagement, academic citations, or fact-check ratings. External Signals are context, not internal Argument Quality Votes.

## Snapshot

A Snapshot is a versioned, reproducible derived read model. Scores, result profiles, reputation summaries, social aggregates, and claim robustness values should be stored as snapshots with an algorithm version and explanation.
