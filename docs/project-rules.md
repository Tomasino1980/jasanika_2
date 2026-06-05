# Project Rules

## Purpose

Jasanika_2 is a modular WordPress framework and theme ecosystem designed for long-term maintainability, clarity and AI-assisted development.

The primary goal is not rapid feature development.

The primary goal is a clean architecture that remains understandable and maintainable for years.

---

# Core Principles

## Simplicity First

Prefer simple solutions over complex abstractions.

If a problem can be solved with a straightforward implementation, do not introduce additional layers.

---

## Modularity

Every module must have a single responsibility.

A module should solve one problem and expose a clearly defined public API.

Avoid tightly coupled systems.

---

## Maintainability

Code must remain understandable after several years without requiring external documentation or third-party frameworks.

Readability is preferred over cleverness.

---

## Predictability

The same problem should always be solved in the same way throughout the project.

Avoid introducing multiple patterns for the same purpose.

---

# Technology Rules

## Allowed Technologies

* WordPress 7+
* PHP 8.3+
* HTML5
* CSS3
* Vanilla JavaScript

---

## Forbidden Technologies

* React
* Vue
* Angular
* jQuery
* Bootstrap
* Tailwind
* Elementor
* WPBakery
* Divi Builder

External dependencies require explicit approval.

---

# Architecture Rules

## Single Responsibility Principle

Each class should have one responsibility.

If a class starts solving multiple problems, it must be split.

---

## Dependency Injection

Services must be resolved through the Service Container.

Avoid hidden dependencies.

---

## Forbidden Patterns

Do not use:

* Singleton pattern
* Global state
* Service Locator pattern
* Static utility classes without justification

---

## No Generic Helpers

Avoid creating classes or files named:

* Helper
* Helpers
* Utils
* Utility
* Common
* Misc

Names must clearly describe responsibility.

---

# Development Workflow

## Milestone Driven Development

Development is performed exclusively through milestones.

Implementation may begin only after milestone approval.

---

## Milestone Definition

Every milestone must contain:

* goal
* scope
* requirements
* acceptance criteria
* expected output

---

## Scope Protection

Only implement functionality defined in the approved milestone.

Do not implement future milestones.

Do not expand milestone scope without approval.

---

# AI Agent Rules

## Agent Role

The AI agent is an implementation tool.

The AI agent is not:

* architect
* product owner
* project manager
* release manager

Architecture decisions are made before implementation begins.

---

## Required Context

Before implementation the agent must load and respect:

* docs/project-rules.md
* docs/folder-structure.md
* docs/roadmap.md
* docs/design-system.md
* docs/typography.md
* docs/ai-workflow.md

---

## AI Development Principle

The agent receives:

* milestone specification
* architecture documentation
* project documentation
* implementation task

The agent implements only the requested functionality.

---

# Documentation Rules

Documentation is part of the product.

Documentation is not optional.

---

## Documentation Updates

Documentation must be updated whenever:

* architecture changes
* project structure changes
* workflow changes
* public APIs change

---

## Architecture Decisions

Important architectural decisions must be documented.

Create Architecture Decision Records (ADR) when necessary.

---

# Git Rules

## Main Branch

The main branch contains the current approved project state.

---

## Milestone Commits

One milestone = one commit.

Commit format:

M<number> - Milestone Name

Examples:

M0 - Initialize

M1 - Core Foundation

M2 - Service Container

---

## Milestone Amend Rule

The latest milestone commit may be amended.

This is allowed only while no work has started on the next milestone.

Example:

M32 completed

↓

Documentation adjustment

↓

Amend M32

↓

Start M33

Once M33 begins, M32 becomes immutable.

---

# Code Quality Rules

## Naming

Names must clearly express intent.

Avoid abbreviations unless commonly accepted.

---

## Readability

Readable code is preferred over compact code.

---

## Comments

Use comments only when the purpose is not obvious from the code itself.

Do not comment every line.

---

## Future Proofing

Do not build systems for hypothetical future requirements.

Implement only what is required by the current milestone.

---

# Final Rule

When uncertain:

Prefer simplicity.

Prefer clarity.

Prefer maintainability.

Avoid unnecessary abstraction.
