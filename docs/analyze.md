# Analyze

## Project

Jasanika_2

Modular WordPress Framework

Current Version: 0.28

---

# Current Milestone

M28.1 - Constructor Dependency Order Fix

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

---

# Existing Directory Structure

See:

docs/project-tree.txt

---

# Active Decisions

ADR documents will be created in:

docs/architecture/

No ADR exists yet.

---

# Known Technical Debt

None

---

# Known Risks

None

---

# Next Planned Milestone

M29 - Advanced Color System

---

# Notes

This document reflects the current implementation state of the project.

It must be updated after every completed milestone.