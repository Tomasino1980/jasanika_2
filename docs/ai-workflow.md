# AI Workflow

## Purpose

This document defines how AI agents participate in project development.

The workflow is model-independent.

The same workflow must work with:

* DeepSeek
* Qwen
* GPT
* Claude
* Kimi
* future models

---

# Core Principle

AI is an implementation tool.

AI is not the project owner.

AI is not the architect.

AI is not the release manager.

AI implements approved specifications.

---

# Priority Rule

If an AI response conflicts with project documentation, project documentation always wins.

Priority order:

1. project-rules.md
2. folder-structure.md
3. roadmap.md
4. architecture documents
5. milestone specification
6. AI output

---

# Development Process

Project Idea

↓

Architecture

↓

Milestone Definition

↓

Milestone Approval

↓

AI Implementation

↓

Review

↓

Testing

↓

Milestone Completion

---

# Milestone Requirements

Implementation may begin only after milestone approval.

Every milestone must define:

* objective
* scope
* requirements
* acceptance criteria
* expected output

---

# AI Responsibilities

The AI agent may:

* create files
* modify files
* implement functionality
* refactor approved code
* suggest improvements

The AI agent may not:

* redefine architecture
* expand milestone scope
* introduce new technologies
* introduce external dependencies
* create future milestone functionality

without approval.

---

# Required Context

Before implementation the AI must load:

* docs/project-rules.md
* docs/folder-structure.md
* docs/roadmap.md
* docs/design-system.md
* docs/typography.md
* docs/ai-workflow.md

and all milestone-specific documentation.

---

# Implementation Rules

Implement only the requested functionality.

Do not implement future milestones.

Do not create additional features.

Do not create speculative functionality.

---

# Documentation Driven Development

Documentation is the source of truth.

Code follows documentation.

Documentation never follows generated code.

---

# Milestone Review

Before milestone completion:

* verify requirements
* verify acceptance criteria
* verify folder structure
* verify naming consistency
* verify documentation impact

---

# Milestone Completion

Before milestone completion the AI must provide:

## Modified Files

List of changed files.

---

## Created Files

List of created files.

---

## Acceptance Criteria

Checklist of completed requirements.

---

## Suggested Commit Message

Format:

M<number> - Milestone Name

Example:

M3 - Module System

---

# Milestone Amend Rule

The latest milestone may be amended.

Allowed:

* documentation updates
* workflow updates
* architecture clarification

Condition:

No work has started on the next milestone.

Once the next milestone begins, the previous milestone becomes immutable.

---

# Failure Rule

If implementation significantly violates:

* architecture
* project rules
* milestone scope

the session is considered failed.

The implementation may be discarded and restarted.

---

# Final Rule

When uncertain:

Stop.

Ask for clarification.

Never invent architecture.
