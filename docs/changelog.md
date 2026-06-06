# Changelog

All notable project changes are documented in this file.

The changelog is updated after every completed milestone.

---

# M0 - Initialize

Date: YYYY-MM-DD

## Added

* Repository initialized
* Documentation structure created
* AI workflow defined
* Project rules defined
* Folder structure defined

## Changed

- N/A

## Fixed

- N/A

---

# M1 - Core Foundation

Date: YYYY-MM-DD

## Added

- Composer integration
- PSR-4 autoloading
- Bootstrap class
- Application class
- Framework initialization flow

## Changed

- Theme startup now uses framework bootstrap

## Fixed

- N/A

---

# M2 - Service Container

Version: 0.2

Date: 2026-06-05

## Added

- Container class with service registration
- Container service resolution (singleton behavior)
- Container service existence check
- Application now owns a Container instance
- getContainer() method on Application

## Changed

- N/A

## Fixed

- N/A

---

# M3 - Module System

Version: 0.3

Date: 2026-06-05

## Added

- ModuleInterface with register() and boot() contracts
- ModuleManager for module registration and lifecycle
- ModuleManager registered in the Container
- Application now owns a ModuleManager instance
- getModuleManager() method on Application

## Changed

- N/A

## Fixed

- N/A

---

# M5 - Hook System

Version: 0.5

Date: 2026-06-05

## Added

- HookManager with addAction() and addFilter() methods
- HookableInterface with register() contract
- HookManager registered in the Container
- getHookManager() method on Application

## Changed

- N/A

## Fixed

- N/A

---

# M4 - Configuration System

Version: 0.4

Date: 2026-06-05

## Added

- Config class with dot-notation get(), has(), all() methods
- ConfigRepository for loading PHP config files from config/*.php
- ConfigRepository registered in the Container
- Application now owns a ConfigRepository instance
- getConfigRepository() method on Application
- config/app.php with application metadata
- config/modules.php with module list
- Configuration system initialized during Application startup

## Changed

- N/A

## Fixed

- N/A

---

# M7 - Settings Foundation

Version: 0.7

Date: 2026-06-06

## Added

- SettingInterface with getKey() and getDefaultValue() contracts
- SettingsManager with register(), get(), set() methods
- SettingsManager registered in the Container
- getSettingsManager() method on Application
- config/settings.php with settings configuration

## Changed

- Version updated to 0.7

## Fixed

- N/A

---

# M6 - Asset Manager

Version: 0.6

Date: 2026-06-05

## Added

- Asset immutable value object with handle, source, version
- AssetManager with registerStyle(), registerScript(), enqueueStyle(), enqueueScript()
- AssetManager registered in the Container
- getAssetManager() method on Application
- config/assets.php with asset configuration

## Changed

- N/A

## Fixed

- N/A

---

# M8 - Admin Menu Foundation

Version: 0.8

Date: 2026-06-06

## Added

- AdminPage value object with page title, slug, callback
- AdminMenu with registerPage() and register() methods
- AdminMenu registered in the Container
- getAdminMenu() method on Application
- Jasanika Dashboard page in WordPress Admin

## Changed

- Version updated to 0.8

## Fixed

- N/A

---

# M9 - Settings Page Foundation

Version: 0.9

Date: 2026-06-06

## Added

- SettingsPage class with Settings API integration
- AdminMenu::registerSubPage() for submenu registration
- Settings submenu under Jasanika in WordPress Admin
- site_layout setting with full-width/boxed options
- config/settings.php defaults configuration

## Changed

- Version updated to 0.9
- AdminMenu::registerPages() now handles submenu pages

## Fixed

- N/A

---

# M10 - Settings Registry

Version: 0.10

Date: 2026-06-06

## Added

- SettingsRegistry class with register(), get(), all() methods
- SiteLayoutSetting implementing SettingInterface
- SiteLayoutSetting registered during Application startup
- SettingsRegistry registered in the Container
- getSettingsRegistry() method on Application

## Changed

- SettingsManager now accepts SettingsRegistry in constructor
- SettingsManager delegates to SettingsRegistry for setting storage and default value resolution
- Version updated to 0.10

## Fixed

- N/A

---

# M11 - Theme Options Registry

Version: 0.11

Date: 2026-06-06

## Added

- LogoSetting implementing SettingInterface
- PrimaryColorSetting implementing SettingInterface
- TypographySetting implementing SettingInterface
- ContainerWidthSetting implementing SettingInterface
- All four settings registered in SettingsRegistry during Application startup
- Logo, Primary Color, Typography, Container Width fields on Settings page
- Sanitization for primary_color, typography, container_width values

## Changed

- Version updated to 0.11
- config/settings.php defaults extended with new setting defaults

## Fixed

- N/A

---

# M12 - Settings Field Architecture

Version: 0.12

Date: 2026-06-06

## Added

- FieldInterface with getKey(), getLabel(), getDefault(), render(), sanitize() contracts
- SelectField implementing FieldInterface for select dropdown settings
- ColorField implementing FieldInterface for hex color settings
- NumberField implementing FieldInterface for numeric range settings
- TextField implementing FieldInterface for generic text settings
- Admin Fields architecture under src/Admin/Fields/

## Changed

- SettingsPage refactored from monolithic class to coordinator
- SettingsPage now accepts FieldInterface instances via variadic constructor
- All rendering logic moved from SettingsPage into dedicated field classes
- All sanitization logic moved from SettingsPage into dedicated field classes
- Application.php updated to create field instances and pass them to SettingsPage
- Version updated to 0.12

## Fixed

- N/A

---

# M13 - Architecture Debt Refactoring & Repository Documentation

Version: 0.13

Date: 2026-06-06

## Added

- FrameworkInfo service as single source of truth for framework name and version
- DashboardPage class for admin dashboard rendering
- Admin/Pages/ directory for dedicated page classes
- README.md with project description, architecture overview, and documentation links
- getFrameworkInfo() method on Application

## Changed

- AdminMenu no longer accepts version string; rendering responsibility removed
- AdminMenu::renderDashboard() removed (moved to DashboardPage)
- SettingsPage now accepts FrameworkInfo instead of raw version string
- Field classes (SelectField, ColorField, NumberField, TextField) support nullable defaults resolved from SettingsRegistry
- Application.php creates FrameworkInfo and DashboardPage instances
- Dashboard page callback now references DashboardPage instead of AdminMenu
- Version updated to 0.13 in style.css and config/app.php
- docs/architecture-rules.md added to project tree documentation

## Fixed

- Architecture debt: AdminMenu no longer violates Single Responsibility Principle
- Architecture debt: Framework version no longer duplicated across classes

---

# M14 - Registry Driven Settings Architecture

Version: 0.14

Date: 2026-06-06

## Added

- SettingInterface extended with getLabel(), getFieldType(), getOptions()
- FieldFactory class mapping field types to concrete Field classes
- Registry-driven settings architecture documented in analyze.md

## Changed

- SiteLayoutSetting, LogoSetting, PrimaryColorSetting, TypographySetting, ContainerWidthSetting now provide metadata (label, field type, options)
- SettingsPage refactored to use SettingsRegistry and FieldFactory instead of hardcoded field definitions
- Application.php no longer manually constructs Field instances; uses FieldFactory for automatic field creation
- Version updated to 0.14 in style.css, config/app.php, and README.md

## Fixed

- Architecture debt: SettingsPage no longer contains hardcoded field definitions
- Architecture debt: Adding a new Setting no longer requires modifying SettingsPage or Application.php

---

# M15 - Field Consolidation

Version: 0.15

Date: 2026-06-06

## Added

- AbstractField as abstract base class consolidating shared field state and constructor
- AbstractField listed in architecture hierarchy: FieldInterface → AbstractField → Concrete Fields

## Changed

- TextField now extends AbstractField instead of directly implementing FieldInterface
- ColorField now extends AbstractField instead of directly implementing FieldInterface
- NumberField now extends AbstractField instead of directly implementing FieldInterface
- SelectField now extends AbstractField instead of directly implementing FieldInterface
- Duplicate property declarations (key, label, settingsManager, default, description) removed from all four field classes
- Duplicate constructor logic removed from all four field classes
- Duplicate getKey() and getLabel() implementations removed from all four field classes
- Version updated to 0.15 in style.css, config/app.php, README.md, docs/analyze.md

## Fixed

- Architecture debt: duplicated state and constructor behavior consolidated into AbstractField

---

# M16 - Media & Logo Foundation

Version: 0.16

Date: 2026-06-06

## Added

- MediaManager infrastructure service with attachment validation and URL resolution
- MediaField extending AbstractField for WordPress Media Library integration
- MediaField stores attachment IDs only (no URLs, paths, or metadata)
- Media infrastructure documented in architecture-rules.md

## Changed

- LogoSetting now uses media field type instead of text
- FieldFactory supports media field type mapping
- Version updated to 0.16 in style.css, config/app.php, README.md, docs/analyze.md, FrameworkInfo

## Fixed

- N/A