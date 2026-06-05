# Jasanika Architecture Rules

## Purpose

These rules define long-term architectural principles for the Jasanika Framework.

All future milestones must respect these rules.

---

# Single Responsibility Principle

Each class should have a single responsibility.

Bad:

* SettingsPage renders fields
* SettingsPage sanitizes fields
* SettingsPage stores settings
* SettingsPage defines setting types

Good:

* SettingsPage renders page structure
* Field classes render individual fields
* SettingsManager stores settings
* Setting classes define settings

---

# Registry First (Mandatory)

Whenever a system grows beyond a few items, use a registry.

Examples:

* SettingsRegistry
* ModuleRegistry
* Future WidgetRegistry
* Future ThemeOptionRegistry

Avoid hardcoded lists.

Registries are a primary architectural pattern of the framework.

---

# Composition Over Expansion

When a class exceeds its original responsibility:

Do not continue adding methods.

Create dedicated components.

Example:

Bad:

SettingsPage

* renderColorField()
* renderTextField()
* renderNumberField()
* renderSelectField()

Good:

ColorField
TextField
NumberField
SelectField

SettingsPage composes fields.

---

# Infrastructure Before Features

Always build supporting architecture before adding large feature sets.

Preferred:

1. Registry
2. Interfaces
3. Services
4. UI

Avoid implementing large UI systems before infrastructure exists.

---

# Avoid Monolithic Classes

Classes should remain focused.

Warning signs:

* More than 300 lines
* Many render methods
* Many sanitize methods
* Multiple responsibilities

When this occurs:

Refactor into dedicated classes.

---

# Backward Compatibility

Refactoring milestones must preserve existing functionality.

Architecture improvements must not break:

* Existing settings
* Existing admin pages
* Existing configuration
* Existing modules

---

# Framework Versioning

Version number follows milestone numbering.

Examples:

M1 = 0.1
M10 = 0.10
M100 = 0.100
M1000 = 0.1000

Only the project owner may declare version 1.0.

AI agents must never automatically promote the framework to version 1.0.

---

# Architectural Debt Registry

The following areas are intentionally simplified.

Future milestones should prioritize refactoring these areas before expanding functionality.

---

## Admin Menu Separation

Current state:

AdminMenu is partially responsible for page registration and framework-related content.

Target architecture:

AdminMenu
│
├── DashboardPage
├── SettingsPage
└── Future Pages

Rules:

* AdminMenu registers pages only.
* AdminMenu must not render page content.
* Page rendering belongs to dedicated page classes.

---

## Framework Information Service

Current state:

Framework version is passed directly into multiple classes.

Target architecture:

FrameworkInfoService

Responsibilities:

* Framework name
* Framework version
* Framework metadata

Rules:

* Avoid duplicating version information across classes.
* Use a dedicated service as a single source of truth.

---

## Settings Architecture

Current state:

SettingsPage knows about specific settings.

Examples:

* site_layout
* primary_color
* typography
* container_width

Target architecture:

SettingsRegistry
↓
Field Objects
↓
SettingsPage

Rules:

* SettingsPage coordinates rendering only.
* SettingsPage should not contain field-specific logic.
* Field classes own rendering and sanitization.
* New settings should not require new render methods inside SettingsPage.

---

## Configuration Immutability

Configuration is read-only.

Rules:

* ConfigRepository provides read access only.
* Do not introduce runtime configuration mutation.
* Avoid methods such as:

$config->set(...)

Preferred flow:

Configuration Files
↓
ConfigRepository
↓
Read Access

---

## Asset Manager Scope

AssetManager is infrastructure.

Rules:

AssetManager must never contain:

* CSS generation
* Build systems
* Minification
* Theme logic
* Business logic

AssetManager responsibilities:

* Register assets
* Enqueue assets

Nothing more.

---

## Registry Driven Design

When a feature becomes configurable or extensible:

Prefer a registry before adding more hardcoded entries.

Examples:

* SettingsRegistry
* ModuleRegistry
* Future WidgetRegistry
* Future ThemeOptionRegistry

Avoid:

if (...) { ... }
elseif (...) { ... }
elseif (...) { ... }

for growing feature sets.

---

## Refactoring Before Expansion

When an architectural debt item exists:

Prefer refactoring before adding new functionality.

Priority order:

1. Maintainability
2. Architecture
3. Features

Avoid feature growth that increases architectural debt.

---

## Milestone Review Requirement

Before starting a new milestone:

Review:

* architecture-rules.md
* changelog.md
* analyze.md

Evaluate:

* Existing architectural debt
* Potential responsibility violations
* Existing registries that should be reused

New milestones should extend architecture rather than bypass it.

---

## Framework Evolution Policy

The framework should evolve through:

Interfaces
↓
Registries
↓
Services
↓
Pages / UI

Avoid:

UI
↓
Business Logic
↓
Infrastructure

Infrastructure must exist before large user-facing features.
