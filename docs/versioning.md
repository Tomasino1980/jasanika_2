# Jasanika_2 Versioning

## Purpose

This document defines versioning rules for the Jasanika_2 framework.

Versioning is milestone-based and directly reflects project progress.

---

## Development Versioning

During development, versions are mapped directly to milestone numbers.

Format:

0.<milestone_number>

Examples:

M0 -> 0.0

M1 -> 0.1

M2 -> 0.2

M10 -> 0.10

M25 -> 0.25

M100 -> 0.100

M250 -> 0.250

M1000 -> 0.1000

The current project version must always match the latest completed milestone.

---

## Version 1.0.0

Version 1.0.0 is reserved for the first feature-complete release.

Feature-complete means:

* Core framework architecture is complete.
* Planned framework functionality is implemented.
* Required modules are implemented.
* Framework behaves according to project goals.
* Project owner approves the release.

Only the project owner may declare version 1.0.0.

AI agents must never independently promote the project to version 1.0.0.

---

## Required Version Updates

After every completed milestone, the AI agent must update:

* style.css
* docs/analyze.md
* docs/changelog.md

The version number must be identical in all locations.

Example:

Completed milestone:

M25

Required version:

0.25

---

## Changelog Rules

Every milestone must create or update a changelog entry.

Each changelog entry must contain:

* Version
* Milestone
* Added
* Changed
* Fixed
* Removed

If a section is not applicable, use:

N/A

---

## style.css

The WordPress theme version must always match the current project version.

Example:

Version: 0.25

---

## analyze.md

The current project version must always be recorded in analyze.md.

Example:

Current Version: 0.25

Current Milestone: M25

Status: Completed

---

## Release Readiness

The project is considered a development release until version 1.0.0.

All versions below 1.0.0 are development versions.

The milestone number determines the development version.

Version 1.0.0 is a project-owner decision, not an automatic milestone result.

---

## Agent Responsibilities

When completing a milestone, the AI agent must:

1. Update the project version.
2. Update style.css.
3. Update docs/analyze.md.
4. Update docs/changelog.md.
5. Verify version consistency.
6. Include version information in the completion report.

Version consistency is part of milestone acceptance criteria.
