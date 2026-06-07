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