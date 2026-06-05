# Folder Structure

## Purpose

This document defines the official project structure.

All source code must follow this structure.

Files may only be created inside approved directories.

---

# Root Structure

```text
jasanika_2/

├── assets/
├── config/
├── docs/
├── src/
├── templates/
├── tests/

├── functions.php
├── style.css
├── screenshot.png
├── README.md
```

---

# assets

Contains all frontend assets.

```text
assets/

├── css/
├── js/
├── images/
├── fonts/
```

Rules:

* CSS only inside css/
* JavaScript only inside js/
* Images only inside images/
* No PHP files

---

# config

Framework configuration.

```text
config/

├── app.php
├── modules.php
├── assets.php
```

Rules:

* Configuration only
* No business logic
* No WordPress hooks

---

# docs

Project documentation.

```text
docs/

├── project-rules.md
├── folder-structure.md
├── roadmap.md
├── design-system.md
├── typography.md
├── ai-workflow.md

├── architecture/
├── milestones/
├── adr/
```

---

# src

Framework source code.

```text
src/

├── Core/
├── Container/
├── Modules/
├── Config/
├── Assets/
├── Hooks/
├── Admin/
├── WooCommerce/
├── Support/
```

---

# Core

Framework bootstrap and initialization.

```text
src/Core/

Bootstrap.php
Application.php
```

Responsibilities:

* framework startup
* initialization
* lifecycle

---

# Container

Dependency Injection Container.

```text
src/Container/

Container.php
ServiceProvider.php
```

Responsibilities:

* service registration
* service resolution

---

# Modules

All project modules.

```text
src/Modules/

Dashboard/
Blog/
Shop/
SEO/
```

Rules:

* one module = one directory
* isolated functionality

---

# Config

Configuration services.

```text
src/Config/
```

Responsibilities:

* configuration loading
* configuration validation

---

# Assets

Asset management.

```text
src/Assets/
```

Responsibilities:

* CSS registration
* JS registration
* versioning

---

# Hooks

WordPress integration layer.

```text
src/Hooks/
```

Responsibilities:

* actions
* filters
* hook registration

---

# Admin

Administration layer.

```text
src/Admin/
```

Responsibilities:

* admin pages
* admin menus
* settings pages

---

# WooCommerce

WooCommerce integration.

```text
src/WooCommerce/
```

Responsibilities:

* WooCommerce compatibility
* shop integration

---

# Support

Shared support classes.

```text
src/Support/
```

Allowed:

* Value Objects
* Exceptions
* Interfaces

Forbidden:

* business logic
* helper dumping ground

---

# templates

Theme templates.

```text
templates/

├── parts/
├── pages/
├── archive/
├── woocommerce/
```

Rules:

* HTML output only
* No business logic

---

# tests

Automated tests.

```text
tests/

├── Unit/
├── Integration/
```

---

# File Creation Rules

Before creating a new file:

1. Check whether similar functionality already exists.
2. Place the file in the correct directory.
3. Follow Single Responsibility Principle.
4. Follow naming conventions.

---

# Forbidden Directories

Do not create:

```text
Helpers/
Utils/
Common/
Misc/
Temp/
Old/
Backup/
```

without explicit approval.

---

# Final Rule

When uncertain:

Place code according to responsibility, not convenience.
