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
