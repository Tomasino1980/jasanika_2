# Analyze

## Project

Jasanika_2

Modular WordPress Framework

Current Version: 0.31

---

# Current Milestone

M31 - Dynamic Theme Settings Engine

Status: Completed

---

# Completed Milestones

- M0 - Initialize
- M1 - Core Foundation
- M2 - Service Container
- M3 - Module System
- M4 - Configuration System
- M5 - Hook System
- M6 - Asset Manager
- M7 - Settings Foundation
- M8 - Admin Menu Foundation
- M9 - Settings Page Foundation
- M10 - Settings Registry
- M11 - Theme Options Registry
- M12 - Settings Field Architecture
- M13 - Architecture Debt Refactoring & Repository Documentation
- M14 - Registry Driven Settings Architecture
- M15 - Field Consolidation
- M16 - Media & Logo Foundation
- M17 - Media Infrastructure Refinement
- M17.1 - Asset Registration Lifecycle Fix
- M18 - Frontend Foundation & Theme Rendering
- M19 - Content Rendering & Template Architecture
- M20 - Navigation & Site Identity Architecture
- M21 - Widget Areas & Layout Regions Architecture
- M22 - Theme Customizer & Design Settings Integration
- M23 - Dynamic Layout System
- M24 - Design Token Engine & Theme Preset Foundation
- M25 - Component Styling Framework
- M26 - Site Builder & Settings UI Framework
- M27 - Theme Presets & Settings UX Framework
- M28 - Dynamic Header Builder
- M28.1 - Constructor Dependency Order Fix
- M28.2 - Bootstrap Documentation & Architecture Cleanup
- M29 - Settings UI Refactor & Design System
- M30 - Admin UI Dark Card Layout
- M31 - Dynamic Theme Settings Engine

---

# Current Architecture

The project has been transformed into a configurable Site Builder with a user-friendly Settings UI Framework (M27) and Dynamic Header Builder (M28).

**Dynamic Header Builder (M28):**
- HeaderLayout engine with 7 layout definitions (logo-left, logo-center, logo-right, logo-menu, logo-menu-search, logo-menu-cta, logo-menu-search-cta)
- Layout validation and zone-based rendering configuration
- Layout switching immediately affects frontend output
- Header settings expanded: layout, responsive heights (desktop/tablet/mobile), CTA button, top bar
- HeaderRenderer now fully responsible for all header HTML output
- No header HTML outside HeaderRenderer

**Responsive Logo Rendering V3 (M28):**
- Desktop, Mobile, Retina logo with automatic selection via CSS
- Responsive display (mobile logo on <768px, retina on high-DPI)
- Site title fallback when no logo is set
- Logo width/height/position controls

**Sticky Header (M28):**
- jas-header--sticky class generated from settings
- CSS position: sticky with smooth transition
- Scroll detection via header.js adds jas-header--scrolled class
- Mobile compatible

**Search Toggle (M28):**
- Desktop: search icon with expandable inline form
- Mobile: fullscreen search overlay with focus management
- Controlled via header.js, accessible aria attributes
- Uses existing search-form markup

**CTA Button System (M28):**
- Fully configurable via settings (label, URL, style)
- Uses Component Framework Button component
- Styles: Primary, Secondary, Outline
- Token-driven styling

**Top Bar System (M28):**
- Optional top bar above header
- Configurable content, background, and text color
- Token driven via CSS custom properties
- Responsive (compact on mobile)

**Mobile Navigation Foundation (M28):**
- MobileMenu class with breakpoint and JS configuration
- Mobile hamburger toggle with aria-expanded
- Foundation for future Mega Menu support

**Header CSS Architecture (M28):**
- Dedicated assets/css/header.css (token-driven, no hardcoded values)
- All header/navigation/sticky/search/top-bar/logo styles in single file
- Removed duplicated header styles from frontend.css
- Mobile and tablet responsive breakpoints

**Debug Support (M28):**
- WP_DEBUG mode outputs header configuration as HTML comment
- Shows: layout, sticky, search, CTA, logo statuses
- No debug output in production

**Theme Presets UI (M27):**
- Visual preset selection cards in Appearance → Presets section
- Available presets: Default, Modern, Minimal, Business, Custom
- PresetCard component for visual preset selection with radio buttons
- Preset switching integrated with ThemePresetManager
- Custom mode enables full editing of Color Scheme and Typography

**Theme Presets UI (M27):**
- Visual preset selection cards in Appearance → Presets section
- Available presets: Default, Modern, Minimal, Business, Custom
- PresetCard component for visual preset selection with radio buttons
- Preset switching integrated with ThemePresetManager
- Custom mode enables full editing of Color Scheme and Typography

**Appearance Dashboard (M27):**
- New Appearance Overview admin sub-page
- Card-based read-only summary of current theme configuration
- Shows: active preset, color scheme, typography, header/hero/footer status, layout, logo
- Uses Component Framework Card components for consistent rendering

**Settings UI Framework V3 (M27):**
- Settings Search: client-side real-time filtering with result highlighting
- Collapsible Sections: toggle-able panels with sessionStorage state persistence
- Settings Cards: improved grouping with consistent spacing and labels
- Color Scheme Builder: 8 color settings (primary, secondary, accent, background, surface, text, heading, border)
- Expanded Typography: 6 font options (System, Inter, Roboto, Poppins, Montserrat, Open Sans)
- Admin CSS with dedicated styles for all new UI components
- Fixed rendering inconsistencies in form table layout

**Collapsible Panel:**
- Reusable CollapsiblePanel component
- Accessible toggle with aria-expanded, aria-controls
- State preserved across tab navigation via sessionStorage
- Badge support for field count display

**Settings Search:**
- SettingsSearch class with search input and inline JS
- Real-time client-side filtering of settings sections
- Search matches field labels, descriptions, and categories
- Highlighted matching results
- Result count display

**Color Scheme Builder:**
- 8 color settings: primary, secondary, accent, background, surface, text, heading, border
- New DesignTokenRegistry tokens for all color settings
- DesignTokenGenerator outputs all color tokens
- Token-driven color management via DesignSettingsManager getters

**Typography Expansion:**
- TypographySetting expanded to 6 font options
- DesignSettingsManager getFontFamily() supports all fonts
- Heading font family token (--jas-font-family-heading) registered

**Preview Architecture Foundation (M27):**
- FrontendRefreshEvent value object for future M28 Live Preview
- Event-driven architecture: setting change → refresh event → frontend refresh
- shouldRefresh() method filters appearance-related settings

**Architecture documents:**
- project-rules.md
- folder-structure.md
- roadmap.md
- ai-workflow.md
- design-system.md
- typography.md
- architecture-rules.md

---

**Settings UI Refactor & Design System (M29):**
- Two-level settings navigation: top-level tabs (General, Appearance, Content, Marketing, Advanced) with second-level sub-navigation for sections
- Settings Card System (SettingsCard.php): reusable card component with header (title), optional description, settings content, and optional action buttons
- Admin Design Registry (AdminDesignRegistry.php): design tokens for admin UI — Radius (XS=2px, SM=4px, MD=6px), Spacing (8px grid), Colors (border, surface, background, text, muted, accent), Shadows (minimal)
- Subtle border system using token-driven values (rgba borders, no strong separators)
- Design Style Registry (DesignStyleRegistry.php): UI style presets — Classic, Modern, Glass (Foundation Only, no visual implementation)
- Enhanced Settings Search: filters cards, highlights matches, hides irrelevant sections, shows result count
- Collapsible Sections: large settings groups with only first section expanded by default, field key-based grouping
- Responsive admin UI: breakpoints at 1366px, 1024px, 782px, 600px — no horizontal scrolling
- Content sub-sections: Blog, Search, Archives, Single Post (architecture ready)
- Marketing sub-sections: Social, Analytics, SEO, Integrations (architecture ready)
- Advanced sub-sections: Performance, Debug, Custom CSS, Custom JS (architecture ready)
- Admin CSS rewritten with --jas-admin-* design tokens, card system, responsive breakpoints, and improved visual hierarchy

---

# Implemented Components

- Composer
- PSR-4 Autoloading
- Bootstrap
- Application
- Container
- ModuleInterface
- ModuleManager
- Config
- ConfigRepository
- HookManager
- HookableInterface
- Asset
- AssetManager
- SettingInterface
- SettingsManager
- AdminPage
- AdminMenu
- SettingsPage
- SettingsRegistry
- SiteLayoutSetting
- LogoSetting
- PrimaryColorSetting
- TypographySetting
- ContainerWidthSetting
- FieldInterface
- SelectField
- ColorField
- NumberField
- TextField
- FieldFactory
- FrameworkInfo
- DashboardPage
- AbstractField
- MediaManager
- MediaField
- ContentRenderer
- Template hierarchy (page, single, archive, search, 404)
- Content components (page-header, content-card, content-meta, empty-state)
- Archive rendering pipeline
- Search results with empty state
- 404 error page
- Content styling (typography, archive grid, cards, pagination, empty state, error page)
- Template resolution in ThemeRenderer
- NavigationManager with menu registration and rendering
- SiteIdentityRenderer with logo/title/tagline rendering
- Navigation components (site-branding, navigation, footer-navigation)
- Header composition with branding and primary nav
- Footer composition with nav, copyright, version
- Navigation styling (header, dropdowns, mobile toggle, footer)
- Responsive navigation layout (768px, 1024px breakpoints)
- WidgetAreaManager with sidebar registration (primary-sidebar, footer-left, footer-center, footer-right)
- LayoutRegionRenderer with sidebar and footer region rendering
- Content + Sidebar layout (right sidebar) support
- Widget styling (widget titles, lists, forms, responsive layout)
- Footer widget regions with responsive three-column grid
- Sidebar template with graceful empty state
- DesignSettingsManager with centralized frontend design configuration
- DesignTokenGenerator with CSS custom property generation
- Design debug panel (HTML comment) in WP_DEBUG mode
- Site layout body class support (boxed/full-width)
- tokens.css with primary color and layout token consumption
- LayoutManager with layout resolution (content-sidebar, full-width, landing-page)
- LayoutRenderer with centralized layout rendering
- Layout templates (content-sidebar, full-width, landing-page)
- Layout debug information (HTML comment) in WP_DEBUG mode
- Layout CSS classes (jas-layout, jas-layout--sidebar, jas-layout--full-width, jas-layout--landing)
- Landing page layout (no sidebar, no footer widgets)
- DesignTokenRegistry with token definitions (Color, Typography, Spacing, Layout, Border Radius)
- ThemePresetManager with preset registration (default, modern, minimal, business, custom)
- Semantic color tokens (--jas-color-primary through --jas-color-heading, --jas-color-secondary, --jas-color-accent)
- Typography scale tokens (--jas-font-size-xs through --jas-font-size-2xl)
- Heading font token (--jas-font-family-heading)
- Spacing system tokens (--jas-space-xs through --jas-space-xl)
- Border radius tokens (--jas-radius-sm, --jas-radius-md, --jas-radius-lg)
- Extended design token debug output with preset and token count
- Preset-aware token generation architecture
- .jas-theme CSS context for future theme switching
- ComponentRegistry with component registration (button, card, alert, form-field)
- ComponentRenderer with renderButton(), renderCard(), renderAlert(), renderFormField()
- Button component with primary, secondary, outline variants
- Card component with header, body, footer sections
- Alert component with info, success, warning, error types
- Form field component with text, email, search, textarea, select support
- components.css — token-driven component styling (no hardcoded values)
- Component debug output in WP_DEBUG mode
- Generic reusable Setting class for inline setting registration
- Section system (Section.php) for settings grouping within categories
- Tabbed Settings UI with URL-safe tab state
- Header Builder (HeaderManager, HeaderRenderer) with Logo V2 support
- Footer Builder (FooterManager, FooterRenderer) with column layouts
- Hero Builder (HeroManager, HeroRenderer, HeroSlide) with static and slider modes
- Slider foundation (3 slides, Settings Framework storage)
- Layout controls (header/content/sidebar/footer width, section padding/margin)
- Site Builder debug output in WP_DEBUG mode
- Desktop, Mobile, Retina logo support with position/size controls
- **M27 NEW:** Theme Presets UI with visual PresetCard component
- **M27 NEW:** Custom Preset Mode
- **M27 NEW:** Appearance Dashboard with card-based overview
- **M27 NEW:** Settings Search with client-side filtering
- **M27 NEW:** Collapsible Sections with state persistence
- **M27 NEW:** Color Scheme Builder (8 color settings)
- **M27 NEW:** Expanded Typography (6 font options)
- **M27 NEW:** Admin CSS for all new UI components
- **M27 NEW:** FrontendRefreshEvent for future Live Preview (M28)
- **M28 NEW:** HeaderLayout engine with 7 layout definitions and zone-based rendering
- **M28 NEW:** Dynamic HeaderRenderer with full settings-driven output
- **M28 NEW:** Responsive Logo Rendering V3 (desktop, mobile, retina, site title fallback)
- **M28 NEW:** Sticky Header with scroll detection and smooth transition
- **M28 NEW:** Search Toggle (desktop inline, mobile fullscreen overlay)
- **M28 NEW:** CTA Button System (label, URL, style via Component Framework)
- **M28 NEW:** Top Bar System (content, background, text color)
- **M28 NEW:** MobileMenu foundation class with breakpoint and JS config
- **M28 NEW:** assets/css/header.css (token-driven, no hardcoded values)
- **M28 NEW:** assets/js/header.js (sticky, search overlay, mobile nav)
- **M28 NEW:** WP_DEBUG header configuration debug output
- **M28 NEW:** Expanded header settings (layout, responsive heights, CTA, top bar)
- **M29 NEW:** Two-level settings navigation (top-level tabs + second-level sub-navigation)
- **M29 NEW:** SettingsCard system — reusable card component for all settings fields
- **M29 NEW:** AdminDesignRegistry — admin design tokens (radius, spacing, colors, shadows)
- **M29 NEW:** DesignStyleRegistry — UI style presets (Classic, Modern, Glass foundation)
- **M29 NEW:** Enhanced settings search — filters cards, highlights, hides irrelevant sections
- **M29 NEW:** Collapsible sections with first-group-only expanded by default
- **M29 NEW:** Content sub-sections (Blog, Search, Archives, Single Post)
- **M29 NEW:** Marketing sub-sections (Social, Analytics, SEO, Integrations)
- **M29 NEW:** Advanced sub-sections (Performance, Debug, Custom CSS, Custom JS)
- **M29 NEW:** Responsive admin UI — breakpoints at 1366px, 1024px, 782px, 600px
- **M29 NEW:** Admin CSS rewritten with --jas-admin-* tokens, card system, subtle borders
- **M30 NEW:** Dark Card Layout — dark surface (#24212b) for all settings cards
- **M30 NEW:** Subtle border system — 1px rgba borders, token-driven (--jas-admin-color-border)
- **M30 NEW:** Standardized radius — cards/buttons 4px, inputs 2px
- **M30 NEW:** Consistent card padding — 24px card, 16px fields, 8px labels
- **M30 NEW:** Card header with subtle divider between header/description/body
- **M30 NEW:** Visual hierarchy — Background → Card → Section → Field → Input
- **M30 NEW:** Input alignment cleanup — uniform row height, consistent label/input widths
- **M30 NEW:** Submit button styled with brand accent color
- **M30 NEW:** Dark navigation bar for top-level tabs
- **M30 NEW:** Token expansion — surface-hover, input-bg, input-text, header-bg, divider, border-strong

---

# M31 — Dynamic Theme Settings Engine

Version: 0.31

Status: Completed

## Added

**Theme Settings Compiler (ThemeSettingsCompiler.php):**
- Dedicated service that reads all appearance settings and generates CSS variables
- Normalizes color scheme (primary, secondary, accent, background, surface, text, heading, border)
- Generates container width, site layout, and typography CSS variables
- Generates logo dimension CSS variables (--jas-logo-width, --jas-logo-height)
- Generates layout control tokens (header, content, sidebar, footer widths, padding, margin)
- getConfig() method returns human-readable configuration array for debug output

**Frontend CSS Variable Injection (M31):**
- New `renderThemeSettingsInlineCss()` method in ThemeRenderer outputs `<style id="jasanika-theme-settings">` block
- Inline style block contains all compiled CSS custom properties at `:root` level
- Registered via `wp_head` action hook, decoupled from DesignTokenGenerator

**Theme Preset Application Engine (M31):**
- ThemePresetManager now has actual token definitions for all presets
- Default: standard Jasanika design (purple primary, dark background)
- Modern: softer purple, slightly lighter background
- Minimal: monochromatic grey palette
- Business: blue primary, navy background, gold accent
- Custom: no overrides (full manual control)
- New `applyPresetToSettings()` method writes preset values to SettingsManager
- New `getAppliedTokens()` method returns resolved preset token set

**Frontend CSS Dynamic Variables (M31):**
- All hardcoded color values in frontend.css replaced with CSS custom properties
- Body background now uses `var(--jas-color-background)`
- Body text now uses `var(--jas-color-text)`
- Heading colors use `var(--jas-color-heading)`
- Heading font family uses `var(--jas-font-family-heading)` (was hardcoded Playfair Display)
- Button backgrounds use `var(--jas-color-primary)` with `var(--jas-color-background)` for contrast
- Border colors use `var(--jas-color-border)`
- Surface/panel backgrounds use `var(--jas-color-surface)`
- Token-link hover colors use `var(--jas-color-primary-hover)`
- All with sensible CSS fallbacks

**Container Width Integration (M31):**
- Container width already used `var(--jas-container-width)` in CSS
- ThemeSettingsCompiler now dynamically generates this from settings
- Boxed layout wrapper background uses dynamic `var(--jas-color-background)`

**Debug Support (M31):**
- New `renderThemeSettingsDebug()` in ThemeRenderer for WP_DEBUG mode
- Outputs: Preset, Primary Color, Container Width, Logo status
- Never visible in production

## Changed

- Application.php — version 0.31, ThemeSettingsCompiler initialization, updated constructor signature for ThemeRenderer, actual preset token definitions, updated asset versions to 0.31
- ThemeRenderer.php — accepts ThemeSettingsCompiler dependency, two new hooks for inline CSS and debug output, renderThemeSettingsInlineCss(), renderThemeSettingsDebug(), getThemeSettingsCompiler() accessor
- ThemePresetManager.php — actual token definitions in all presets, new applyPresetToSettings() method, new getAppliedTokens() method, updated PHPDoc
- frontend.css — all hardcoded color/text/font values replaced with CSS custom properties, version 0.31
- tokens.css — all hardcoded color values replaced with CSS custom properties, renamed from --jas-primary-color to --jas-color-primary, version 0.31
- config/app.php — version updated to 0.31
- style.css — version updated to 0.31

## Fixed

- N/A

---

# Known Technical Debt

None

---

# Known Risks

None

---

# Next Planned Milestone

M32 - Advanced Color System

---

# Notes

This document reflects the current implementation state of the project.

It must be updated after every completed milestone.