# Changelog

All notable project changes are documented in this file.

The changelog is updated after every completed milestone.

---

# M0 - Initialize

...

---

# M25 - Component Styling Framework

Version: 0.25

Date: 2026-06-07

...

---

# M26 - Site Builder & Settings UI Framework

Version: 0.26

Date: 2026-06-07

## Added

- Settings UI Framework V2 — tabbed categories (General, Appearance, Content, Marketing, Advanced)
- Section system (Section.php) for grouping related settings within categories
- Generic reusable Setting class for inline setting registration
- Header Builder (HeaderManager, HeaderRenderer) — header configuration and rendering
- Footer Builder (FooterManager, FooterRenderer) — footer configuration and rendering
- Hero Builder (HeroManager, HeroRenderer, HeroSlide) — hero management and rendering
- Slider foundation — 3 slides stored in Settings Framework (no CPT, no Gutenberg)
- Logo System V2 — Desktop, Mobile, Retina logo support with position/size controls
- Layout Controls — header/content/sidebar/footer width, section padding/margin
- Site Builder debug output in WP_DEBUG mode
- Header/footer/hero CSS classes in frontend.css
- Component-driven Settings UI using Card component

## Changed

- Application.php — complete refactor: 40+ new Site Builder settings registered
- SettingsPage — flat layout replaced with tabbed category/section architecture
- ThemeRenderer — integrated HeaderRenderer, FooterRenderer, HeroRenderer
- DesignTokenGenerator — new layout control tokens (header/content/sidebar/footer width)
- DesignSettingsManager — added getLayoutSetting() method
- header.php — delegates header rendering to HeaderRenderer
- footer.php — delegates footer rendering to FooterRenderer (landing-page aware)
- layout.php — added hero section between header and content
- config/settings.php — extended with all Site Builder default values
- config/app.php — version updated to 0.26
- frontend.css — version bump, added hero/header/footer builder CSS
- components.css — version bump
- style.css — version updated to 0.26

## Fixed

- N/A

---

# M34 - Unified Form Layout System

Version: 0.34

Date: 2026-06-07

## Added

- FormRow component (FormRow.php) — renders each setting as a CSS grid row: 280px label+description | 1fr input
- FormSection component (FormSection.php) — renders each settings section as a panel with header, divider, and content body
- Unified Form Layout System in src/Admin/UI/Form/
- All settings sections (General, Appearance, Content, Marketing, Advanced) now use the unified FormSection + FormRow layout
- Unified input class `jas-form-input` — same height (2.5rem), padding (0.5rem 0.75rem), border-radius (2px) for all field types
- Section panels with dark background, subtle gradient overlay on header, light border, clear divider

## Changed

- SettingsPage.php — all section rendering rewritten to use FormSection/FormRow; bypasses do_settings_fields() for direct field iteration; color scheme and presets sections also use FormSection
- TextField.php — render() outputs only input HTML (no description); uses jas-form-input class
- SelectField.php — render() outputs only select HTML; uses jas-form-input class
- NumberField.php — render() outputs only input HTML; uses jas-form-input/jas-form-input--narrow
- ColorField.php — render() outputs only color picker HTML (no description)
- MediaField.php — render() outputs only media input HTML (no inline description)
- admin.css — complete rewrite of form layout section: Form Section panels, Form Row grid, Form Input unified styling, responsive breakpoints; legacy SettingsCard and form-table styles kept for compatibility
- config/app.php, style.css — version 0.34
- docs/current-state.md — updated to 0.34

## Fixed

- All inputs now have consistent height, spacing, padding, border-radius
- Labels are aligned in a fixed-width column (280px desktop, stacked on mobile)
- Sections are visually separated with header/divider/content panels
- No more inconsistent input sizing across field types

## Added

- HeroLayout class with 5 layout presets (centered, left-aligned, split, minimal, fullscreen)
- Hero Description setting for longer descriptive text
- Hero Background System: type selector (color/image/gradient), bg color, gradient start/end colors
- Hero Overlay System: enable/disable toggle, overlay color setting
- Hero Buttons V2: primary and secondary buttons with label, URL, and style selector
- Hero Height Modes: auto, medium (400px), large (600px), fullscreen (100vh)
- HeroRenderer V2: full layout-driven rendering with standard, split, and content block modes
- Hero CSS (hero.css): dedicated file, token-driven, responsive, layout-specific classes
- Debug output for WP_DEBUG with layout, height, background, overlay, buttons info

## Changed

- Application.php — version 0.33, HeroLayout import, expanded hero settings (18 new settings), hero.css registration
- HeroManager.php — full rewrite with layout, background, overlay, dual button accessors, backward compatibility
- HeroRenderer.php — full rewrite with layout-driven rendering, dual buttons, overlay control, updated debug
- ThemeRenderer.php — enqueues jasanika-hero stylesheet
- frontend.css — hero section removed (moved to hero.css)
- config/app.php, style.css — version 0.33

## Added

- ColorPicker Component (ColorPicker.php) — PHP component that renders modern color picker fields with swatch preview and HEX input
- Modern Color Picker UI (admin-color-picker.js) — Vanilla JavaScript floating color picker with saturation square, hue slider, opacity slider, HEX/RGB inputs
- Color Palette System — 8 palette definitions (Default, Modern, Minimal, Business, Dark, Light, Warm, Cold) with one-click apply
- Theme Preview Card — live preview of header, buttons, card, and typography in Color Scheme section
- Appearance Grid Layout — two-column grid for color scheme fields with responsive single-column fallback
- Frost Glass Styling — rgba(36,33,43,0.92) panels with backdrop-filter blur, minimal border system
- ColorField render() rewritten to use ColorPicker component with hidden field for Settings API submission

## Changed

- Application.php — version 0.32, admin-color-picker.css/admin-color-picker.js asset registration, enqueued on Jasanika admin pages
- ColorField.php — render() delegates to ColorPicker::render() with hidden+hex field
- SettingsPage.php — renderColorSchemeSection() with palette presets, theme preview card, two-column grid
- config/app.php — version updated to 0.32
- style.css — version updated to 0.32
- docs/analyze.md — version 0.32, M32 completed

## Fixed

- N/A

## Added

- Theme Settings Compiler (ThemeSettingsCompiler.php) — dedicated service that reads all appearance settings and generates CSS variables
- Frontend CSS Variable Injection — renderThemeSettingsInlineCss() outputs `<style id="jasanika-theme-settings">` block via wp_head
- Theme Preset Application Engine — actual token definitions for all presets (Default, Modern, Minimal, Business), applyPresetToSettings() writes values to SettingsManager
- Frontend CSS Dynamic Variables — all hardcoded colors/text/fonts replaced with CSS custom properties (--jas-color-background, --jas-color-text, --jas-color-heading, etc.)
- Dynamic Container Width — ThemeSettingsCompiler generates container width from settings
- Dynamic Logo Dimensions — logo width/height from settings available as CSS variables
- Debug Support — renderThemeSettingsDebug() outputs Preset, Primary Color, Container Width, Logo in WP_DEBUG mode

## Changed

- Application.php — version 0.31, ThemeSettingsCompiler initialization, updated ThemeRenderer constructor with compiler dependency, actual preset token definitions, all asset versions updated to 0.31
- ThemeRenderer.php — accepts ThemeSettingsCompiler, two new wp_head hooks for inline CSS and debug, getThemeSettingsCompiler() accessor
- ThemePresetManager.php — actual preset token overrides for all 4 presets, applyPresetToSettings(), getAppliedTokens()
- frontend.css — all hardcoded color/text/border/font values replaced with CSS variable references, version 0.31
- tokens.css — renamed to use --jas-color-* naming, hardcoded backgrounds replaced with dynamic variables, version 0.31
- config/app.php — version updated to 0.31
- style.css — version updated to 0.31

## Fixed

- N/A

## Added

- Dark Card Layout — dark surface (#24212b) for all settings cards
- Subtle border system — 1px rgba borders, token-driven (--jas-admin-color-border)
- Standardized radius — cards/buttons 4px, inputs 2px
- Consistent card padding — 24px card, 16px fields, 8px labels
- Card header with subtle divider between header/description/body
- Visual hierarchy — Background → Card → Section → Field → Input
- Input alignment cleanup — uniform row height, consistent label/input widths
- Submit button styled with brand accent color
- Dark navigation bar for top-level tabs
- Token expansion — surface-hover, input-bg, input-text, header-bg, divider, border-strong

## Changed

- AdminDesignRegistry — M30 color tokens (dark surface, input colors, header-bg, divider, border-strong), radius MD aliased to SM (4px)
- admin.css — complete rewrite: dark card layout, dark form inputs, token-driven shadows, improved responsive styles
- Application.php — version 0.30, all asset versions updated
- config/app.php — version updated to 0.30
- style.css — version updated to 0.30
- docs/analyze.md — version 0.30, M30 completed, M30 implemented components

## Fixed

- N/A

---

# M29 - Settings UI Refactor & Design System

Version: 0.29

Date: 2026-06-07

## Added

- Two-level settings navigation: top-level tabs (General, Appearance, Content, Marketing, Advanced) with second-level sub-navigation for sections
- SettingsCard system (SettingsCard.php): reusable card component with header (title), optional description, settings content, and optional action buttons
- AdminDesignRegistry (AdminDesignRegistry.php): admin design tokens — Radius (XS=2px, SM=4px, MD=6px), Spacing (8px grid), Colors (border, surface, background, text, muted, accent), Shadows (minimal)
- DesignStyleRegistry (DesignStyleRegistry.php): UI style presets — Classic, Modern, Glass (Foundation Only)
- Enhanced Settings Search: filters cards, highlights matches, hides irrelevant sections, shows result count
- Collapsible Sections: large settings groups with first-group-only expanded by default, field key-based grouping
- Content sub-sections: Blog, Search, Archives, Single Post (architecture ready)
- Marketing sub-sections: Social, Analytics, SEO, Integrations (architecture ready)
- Advanced sub-sections: Performance, Debug, Custom CSS, Custom JS (architecture ready)
- Responsive admin UI: breakpoints at 1366px, 1024px, 782px, 600px — no horizontal scrolling
- Subtle border system using token-driven rgba values throughout admin CSS
- Preset card selector with enhanced visual states (hover, active, focus)

## Changed

- Application.php — version 0.29, AdminDesignRegistry/DesignStyleRegistry initialization, expanded settings categories (12 new sections), updated PHPDoc with M29 references
- SettingsPage — V4 with two-level navigation (tabs + sub-tabs), SettingsCard-based field rendering, collapsible groups for large sections
- SettingsSearch — enhanced filtering algorithm: filters whole cards, highlights titles and descriptions, hides irrelevant sub-tabs, debounced input
- admin.css — complete rewrite: --jas-admin-* design token system, SettingsCard component, two-level navigation styles, responsive breakpoints (4 breakpoints), border system with rgba tokens
- config/app.php — version updated to 0.29
- style.css — version updated to 0.29
- assets/css/components.css, tokens.css, frontend.css, header.css — version bump to 0.29

## Fixed

- PHP syntax error in Application.php (PHPDoc closing brace restored, class declaration reinserted after edit corruption)

---

# M27 - Theme Presets & Settings UX Framework

Version: 0.27

Date: 2026-06-07

## Added

- Theme Presets UI — visual preset selection cards (Default, Modern, Minimal, Business, Custom)
- PresetCard component — radio button-based visual preset selector
- Custom Preset Mode — enables full editing when Custom preset is active
- Appearance Dashboard — card-based read-only overview page (Appearance → Overview)
- Settings Search — client-side real-time filtering with result highlighting
- Collapsible Sections — toggle-able panels with sessionStorage state persistence
- Color Scheme Builder — 8 color settings (primary, secondary, accent, background, surface, text, heading, border)
- Expanded Typography — 6 font options (System, Inter, Roboto, Poppins, Montserrat, Open Sans)
- CollapsiblePanel component — reusable accessible collapsible section
- SettingsSearch class — settings search field and inline JS filtering
- AppearanceDashboard class — appearance overview with 8 info cards
- FrontendRefreshEvent value object — preview architecture foundation for M28
- Admin CSS (admin.css) — dedicated styles for all new M27 UI components
- Secondary and accent color tokens (--jas-color-secondary, --jas-color-accent)
- Heading font family token (--jas-font-family-heading)
- DesignTokenRegistry — secondary/accent color token definitions
- DesignSettingsManager — getters for all 8 color scheme values

## Changed

- Application.php — version 0.27, 8 new color scheme settings, Business/Custom presets, expanded typography, Appearance Dashboard page registration, admin CSS enqueue
- SettingsPage — V3 with search bar, collapsible panels, visual preset cards, restructured settings categories (Presets, Color Scheme, Typography sections)
- ThemePresetManager — getActivePresetLabel(), getActivePresetDescription(), isCustomMode()
- DesignTokenGenerator — generates all 8 color scheme tokens, heading font token
- DesignSettingsManager — getFontFamily() expanded with 6 fonts, getSecondaryColor() through getBorderColor() getters
- TypographySetting — expanded options to 6 fonts with key-value pairs
- config/settings.php — added active_preset and 7 color scheme defaults
- config/app.php — version updated to 0.27
- style.css — version updated to 0.27
- assets/css/admin.css — created with settings UI, preset cards, collapsible panel, search, dashboard styles
- assets/css/components.css — version bump

## Fixed

- Settings UI rendering inconsistencies — form table layout with proper spacing and labels
- Section grouping — split Site Identity and Color Scheme into dedicated sections
- Typography select options — now shows full font names instead of keys

---

# M28 - Dynamic Header Builder

Version: 0.28

Date: 2026-06-07

## Added

- HeaderLayout engine with 7 layout definitions (logo-left, logo-center, logo-right, logo-menu, logo-menu-search, logo-menu-cta, logo-menu-search-cta)
- Layout validation and zone-based rendering configuration
- Responsive Logo Rendering V3 (desktop, mobile, retina, site title fallback)
- Sticky Header with scroll detection and smooth transition (jas-header--scrolled class)
- Search Toggle (desktop inline form, mobile fullscreen overlay with focus management)
- CTA Button System (label, URL, Primary/Secondary/Outline styles via Component Framework)
- Top Bar System (content, background, text color, responsive)
- MobileMenu foundation class with breakpoint and JS configuration
- Mobile hamburger toggle with aria-expanded
- assets/css/header.css — token-driven header styling (no hardcoded values)
- assets/js/header.js — sticky header, search overlay, mobile nav functionality
- WP_DEBUG header configuration debug output (layout, sticky, search, CTA, logo)
- Header settings expansion: header_layout, header_height_desktop, header_height_tablet, header_height_mobile, header_show_cta, header_cta_label, header_cta_url, header_cta_style, header_top_bar_content, header_top_bar_bg, header_top_bar_text_color
- New settings sections: CTA Button, Top Bar (Appearance category)

## Changed

- Application.php — version 0.28, 10 new header settings, HeaderLayout/MobileMenu instances, ComponentRenderer dependency for HeaderRenderer, header.css/header.js asset registration, expanded header/CTA/top-bar settings sections
- HeaderManager.php — expanded with getLayout(), getDesktopHeaderHeight(), getTabletHeaderHeight(), getMobileHeaderHeight(), showCta(), getCtaLabel(), getCtaUrl(), getCtaStyle(), getTopBarContent(), getTopBarBackground(), getTopBarTextColor(), getLayoutEngine(), getDesktopLogoUrl(), getMobileLogoUrl(), getRetinaLogoUrl()
- HeaderRenderer.php — complete rewrite with dynamic layout engine, zone-based rendering, Logo V3, search toggle (desktop/mobile), CTA button via ComponentRenderer, top bar, sticky header, debug support
- ThemeRenderer.php — enqueues jasanika-header CSS and JS
- DesignTokenGenerator.php — added --jas-header-bg token for header color
- config/settings.php — 10 new M28 header setting defaults
- config/app.php — version updated to 0.28
- style.css — version updated to 0.28
- frontend.css — removed duplicated header styles (moved to header.css), version bump
- assets/css/components.css — version bump
- assets/css/tokens.css — version bump
- assets/css/admin.css — version bump

## Fixed

- Header styles no longer duplicated between frontend.css and header.css
- HeaderRenderer now single source of truth for all header HTML output

---

# M28.1 - Constructor Dependency Order Fix

Version: 0.28

Date: 2026-06-07

## Fixed

- Constructor initialization order bug: ComponentRenderer used by HeaderRenderer before initialization

## Changed

- Application.php — Component System initialization moved before Header Builder, all section comments replaced with consistent separator format

---

# M28.2 - Bootstrap Documentation & Architecture Cleanup

Version: 0.28

Date: 2026-06-07

## Added

- Architecture map PHPDoc block at top of Application.php showing the complete system hierarchy (Core → Settings → Admin → Navigation → Site Identity → Widgets → Design → Layout → Components → Header → Footer → Hero)
- Constructor divided into 16 architectural sections with standardized separator blocks (CORE SERVICES, SETTINGS SYSTEM, ADMIN SYSTEM, NAVIGATION SYSTEM, SITE IDENTITY SYSTEM, WIDGET SYSTEM, DESIGN TOKEN SYSTEM, LAYOUT SYSTEM, COMPONENT SYSTEM, HEADER BUILDER, FOOTER BUILDER, HERO BUILDER, THEME RENDERER, SETTINGS UI, ADMIN DASHBOARD, CONTAINER REGISTRATION)
- Dependency documentation above each section (Depends on / Provides)
- Milestone ownership comments throughout (M1-M28 references)
- Future bootstrap planning TODO notes for M30+ extraction
- Properties organized into logical groups with section headers (CORE, SETTINGS, ADMIN, NAVIGATION, SITE IDENTITY, WIDGETS, DESIGN, LAYOUT, COMPONENTS, HEADER, FOOTER, HERO)
- Section separator blocks in method areas (SETTINGS REGISTRATION, SETTINGS CATEGORIES, ASSET REGISTRATION, THEME RENDERER INITIALIZATION, CONTAINER SERVICES, DESIGN TOKEN REGISTRATION, THEME PRESETS REGISTRATION, COMPONENTS REGISTRATION, BOOT & PUBLIC ACCESSORS)
- FrameworkInfo.php — expanded PHPDoc with Responsibilities, Dependencies, Used by, Introduced, @todo
- ThemeRenderer.php — expanded class PHPDoc with Dependencies, Used by, Introduced sections, @todo for singleton pattern
- HeaderRenderer.php — expanded class PHPDoc with Dependencies, Used by, Introduced sections, @todo for zone extraction
- FooterRenderer.php — expanded class PHPDoc with Responsibilities, Rendering pipeline, Dependencies, Used by, Introduced sections, @todo tags
- HeroRenderer.php — expanded class PHPDoc with Responsibilities, Rendering modes, Dependencies, Used by, Introduced sections, @todo tags

## Changed

- Application.php — all private method PHPDoc blocks upgraded to include Responsibilities, Dependencies, Used by, and Introduced sections
- Application.php — method order reorganized into logical groups (registerDesignTokens, registerThemePresets, registerComponents moved to dedicated sections with section headers)
- Application.php — boot() method PHPDoc added with @todo for future M30+ separation of construction and boot phases

## Fixed

- N/A

---

# M35 - Settings Density & Container Cleanup

Version: 0.35

Date: 2026-06-07

## Changed

- Inputs: min-height 2rem, padding 0.375rem 0.5rem, font-size 0.8125rem
- Form row: grid 240px | 1fr, gap 16px, padding 8px 0
- Section content padding: 16px (was 24px)
- Section margin-bottom: 8px (was 24px)
- Section title font-size: 0.875rem (was 1rem)
- Label font-size: 0.75rem, description font-size: 0.6875rem
- Settings container wrapper: dark surface background, 1px border, 2px radius

## Added

- `.jas-settings-container` — main dark wrapper wrapping tabs, search, form sections

---

# M36 - Compact Form Controls UI

Version: 0.36

Date: 2026-06-07

## Changed

- Inputs: min-height 1.875rem (30px), padding 0.3125rem 0.5rem, vertical padding reduced
- Selects: min-height 1.875rem, matching input height exactly
- Form rows: padding 5px 0, gap 8px, align-items center (was start)
- Label font-weight: 500 (was 600)
- Color swatch: enlarged from 28×28px to 32×32px
- HEX input: flex: 1 full-width, compact height matching inputs, font-size 0.8125rem
- Color scheme grid: tighter cell padding 6px 10px, label font 10px
- Collapsible panels: toggle padding 0.5rem 0.75rem, body padding 6px 8px 8px, margin 4px
- All inputs/selects/color fields: identical height, border-radius (2px), border style

## Fixed

- Color picker field now fills full width of its grid cell (flex: 1 on hex input)
- Color scheme grid labels consistently uppercase with uniform letter-spacing

---

# M37 - Compact Settings Layout

Version: 0.37

Date: 2026-06-07

## Changed

- Form rows: grid 200px | 1fr, padding 4px, gap 6px (were 240px, 5px, 8px)
- Form section headers: padding 8px 8px 4px, title font-size 0.8125rem (were 16px, 0.875rem)
- Form section content: padding 8px (was 16px)
- Form section margin: 4px (was 8px)
- Tabs: padding 0.5rem 1rem, font-size 0.8125rem (were 0.75rem, 0.875rem)
- Sub-tabs: padding 0.25rem 0.625rem, font-size 0.75rem
- Container form/search padding: 8px (was 16px)
- Search input: compact height, 0.375rem vertical padding
- Collapsible toggle: padding 0.375rem 0.5rem, body padding 4px
- Settings page header: 8px bottom margin, 8px gap (were 16px)
- Preset cards: reduced padding and min-height
- Settings page: no bottom padding (was 32px)

## Changed (Color Picker)

- Theme Preview: now collapsible (default collapsed), toggle button with arrow icon
- Theme Preview body: compact padding 12px, smaller font sizes (10-12px)
- Color scheme grid: gap 6px, cell padding 4px 8px, label font 9px
- Palette swatches: 30×30px (were 36×36px), gap 4px (was 6px)
- Color picker gap: 4px (was 6px), hex max-width 96px
- Removed duplicate .jas-cp__swatch and .jas-cp__hex CSS blocks
- All preview sections: reduced padding and font sizes for higher density

## Fixed

- Theme Preview JS selector updated for renamed body-text class
- Removed duplicate CSS definitions in admin-color-picker.css

## Removed

- ~100 lines of duplicate CSS (old M32 swatch/hex definitions)