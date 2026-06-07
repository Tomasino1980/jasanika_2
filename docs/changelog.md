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