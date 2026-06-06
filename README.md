# Jasanika Framework

Modular WordPress framework built for long-term maintainability, clarity and AI-assisted development.

Jasanika_2 is a custom WordPress theme framework designed with an elegant, boutique-style aesthetic for handmade crafts and e-commerce. It follows milestone-driven development with a strong emphasis on clean architecture.

---

## Current Status

- **Current Version:** 0.17
- **Current Milestone:** M17 - Media Infrastructure Refinement
- **Status:** Active Development

---

## Architecture

### Core

Framework bootstrap and initialization. Entry point is `Bootstrap::init()`, which creates the `Application` instance and starts the framework lifecycle.

### Container

Dependency Injection Container for service registration and resolution. Services are registered once and resolved as singletons.

### FrameworkInfo

Central metadata service providing framework name and version as a single source of truth, eliminating version duplication across classes.

### Modules

Module system with registration and lifecycle management. Each module is isolated in its own directory under `src/Modules/`.

### Configuration

Configuration system using PHP config files loaded from `config/*.php`. Config values are accessed via dot-notation through `ConfigRepository`. Configuration is read-only.

### Hooks

WordPress hook abstraction layer. `HookManager` provides `addAction()` and `addFilter()` methods.

### Assets

Asset management with `AssetManager` for registering and enqueuing CSS and JavaScript files. Asset objects are immutable value objects.

### Settings Registry

`SettingsRegistry` stores `SettingInterface` objects and provides lookup by key. Used as the central repository for theme setting definitions and their defaults.

Each setting now provides metadata:

- `getLabel()` — human-readable field label
- `getFieldType()` — field type (select, color, number, text, media)
- `getOptions()` — allowed option values (for select fields)

### Media Infrastructure

Media support is a first-class framework service:

- **MediaManager** — registered in the Container, accessible via `Application::getMediaManager()`
- **MediaField** — renders HTML with WordPress Media Library integration
- **media-field.js** — dedicated JavaScript asset managed by AssetManager (no inline scripts)
- **AssetManager** — registers and enqueues media-field.js with jQuery dependency

Flow:

```
MediaManager
    ↓
Container / Application
    ↓
MediaField
    ↓
AssetManager → media-field.js → WordPress Media API
```

### Field Architecture

Fields implement `FieldInterface` (render, sanitize, get label, get default). Available field types:

- `SelectField` — dropdown selection
- `ColorField` — hex color input
- `NumberField` — numeric range input
- `TextField` — generic text input
- `MediaField` — WordPress Media Library image selection

All concrete field classes extend `AbstractField`, which consolidates shared state (key, label, default, description, SettingsManager) and constructor behavior.

Architecture hierarchy:

```
FieldInterface
    ↑
AbstractField
    ↑
┌──────┬──────┬──────┬──────┬───────┐
│      │      │      │      │       │
Text  Color  Number Select  Media
Field Field  Field  Field   Field
```

Settings fields are created automatically via `FieldFactory`, which maps the setting's `getFieldType()` to the appropriate concrete field class. Adding a new Setting no longer requires modifying `SettingsPage` or `Application`.

FieldFactory now also receives `AssetManager` and passes it to `MediaField` for script enqueuing.

### Registry-Driven Flow

```
Setting
  ↓
SettingsRegistry
  ↓
FieldFactory
  ↓
Field
  ↓
SettingsPage
```

### Admin System

Admin page structure:

- **DashboardPage** — renders the Jasanika Dashboard (framework info)
- **SettingsPage** — renders the Settings form, coordinating Field objects
- **SettingsManager** — bridges `SettingsRegistry` with the WordPress Options API
- **MediaManager** — attachment validation and URL resolution service (registered in Container)
- **AdminMenu** — responsible solely for menu/submenu page registration

### Media JavaScript

Media Library integration JavaScript lives in `assets/admin/js/media-field.js`, registered and enqueued through `AssetManager`. No inline script blocks remain in `MediaField`.

---

## Documentation

- [Project Analysis](docs/analyze.md) — Current project state and completed milestones
- [Changelog](docs/changelog.md) — All notable project changes
- [Architecture Rules](docs/architecture-rules.md) — Long-term architectural principles
- [Versioning](docs/versioning.md) — Versioning rules and policies
- [Project Rules](docs/project-rules.md) — Core project rules
- [Folder Structure](docs/folder-structure.md) — Official directory structure
- [Design System](docs/design-system.md) — Color palette and visual identity
- [Typography](docs/typography.md) — Font definitions and usage
- [AI Workflow](docs/ai-workflow.md) — AI agent development workflow
- [Roadmap](docs/roadmap.md) — Project milestones

---

## Project Structure

```
jasanika_2/

├── assets/          # Frontend assets (css/, js/, images/, fonts/)
├── config/          # Framework configuration (app.php, modules.php, etc.)
├── docs/            # Project documentation
├── src/             # Framework source code
│   ├── Admin/       # Admin pages, menu, fields
│   ├── Assets/      # Asset registration and enqueuing
│   ├── Config/      # Configuration loading and access
│   ├── Container/   # Dependency Injection Container
│   ├── Contracts/   # Shared interfaces
│   ├── Core/        # Bootstrap, Application, FrameworkInfo
│   ├── Hooks/       # WordPress hook abstraction
│   ├── Media/       # Media infrastructure (MediaManager)
│   ├── Modules/     # Module system
│   ├── Settings/    # Setting definitions and registry
│   ├── Support/     # Shared value objects and exceptions
│   └── WooCommerce/ # WooCommerce integration
├── templates/       # Theme templates
├── tests/           # Automated tests
├── functions.php    # Theme entry point
├── style.css        # Theme stylesheet
└── README.md        # This file
```

---

## Development Rules

All development follows the principles defined in [Architecture Rules](docs/architecture-rules.md):

1. **Single Responsibility Principle** — Each class has one responsibility.
2. **Registry First** — Use registries for extensible feature sets.
3. **Composition Over Expansion** — Create dedicated components rather than expanding classes.
4. **Infrastructure Before Features** — Build supporting architecture before large features.
5. **Avoid Monolithic Classes** — Keep classes focused and under 300 lines.
6. **Backward Compatibility** — Preserve existing functionality during refactoring.
7. **Refactoring Before Expansion** — Prioritize maintainability and architecture over features.

---

## Versioning

Version numbers follow milestone numbering. See [Versioning Policy](docs/versioning.md) for details.

- Development versions: `0.<milestone_number>` (e.g., 0.15 for M15)
- Version 1.0.0 is reserved for the first feature-complete release and may only be declared by the project owner.