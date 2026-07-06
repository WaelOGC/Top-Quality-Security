# TQS Theme — Theme Options Implementation Report

**Project:** Top Quality Security WordPress Theme (`tqs-theme` v2.0.0)  
**Date:** 6 July 2026  
**Scope:** Theme Options system for the WordPress admin dashboard (Customizer + supporting code)  
**Location in admin:** Appearance → Customize → **🛡️ TQS Theme Settings**

---

## 1. Executive Summary

This report documents the work completed during the development session to give the site administrator full control over theme-wide settings from the WordPress dashboard — without editing code.

The implementation follows a **hybrid architecture** agreed at the start of the session:

| Layer | Tool | Purpose |
|---|---|---|
| Site-wide settings | WordPress **Customizer** (`get_theme_mod`) | Colors, logos, contact info, homepage sections, footer, GDPR, SEO |
| Per-page overrides | **Meta boxes** (unchanged) | Hero image, hero title override, hide header/footer, gallery |
| Content | Pages, Diensten CPT, Menus | Page copy, services, navigation |
| Site identity (core) | WordPress **Site Identity** | Site title, tagline, favicon; logo syncs with TQS header logo |

No third-party options plugins (ACF, Kirki, Redux) were added. All settings use native WordPress APIs.

---

## 2. Conversation Timeline

### Phase 1 — Planning & Recommendations

Before any code was written, a full options audit was delivered covering:

- Recommended tools (Customizer vs. admin page vs. plugins)
- Proposed panel structure (18 sections)
- Priority roadmap (Phases 1–4)
- Gap analysis: homepage hero slider, Why Us cards, and brand colors were hardcoded in templates

The client agreed to proceed with **Phase 1 + Phase 2** (homepage controls + brand colors) and the hybrid Customizer approach.

### Phase 2 — Theme Options Implementation

The Customizer was expanded from 6 sections to a full **🛡️ TQS Theme Settings** panel with 18+ sections. New code was split into dedicated include files; templates were wired to read settings instead of hardcoded values.

### Phase 3 — Logo Fixes

The client reported that header and footer logos could not be changed from the Customizer, and that uploaded assets (`top-logo.png`, `top-Identity-logo.png`) were not displaying correctly.

Root causes identified and fixed:

1. **Invalid HTML** — `the_custom_logo()` output a nested `<a>` inside the existing brand link.
2. **CSS constraints** — logos were forced into a fixed 44×44 px square, distorting the image.
3. **Missing dedicated controls** — no explicit header/footer/identity logo fields in the TQS panel.
4. **No theme asset fallbacks** — files placed in `assets/images/` were not used until Customizer uploads existed.

### Phase 4 — Theme Upload Note (Out of Scope for Code)

A separate question arose about a WordPress upload error (*"The theme is missing the style.css stylesheet"*). Investigation confirmed `tqs-theme/style.css` is valid. That error is caused by incorrect zip packaging (wrong folder nesting), not a missing or broken stylesheet in the theme source.

---

## 3. Architecture

### 3.1 New Files

| File | Role |
|---|---|
| `tqs-theme/inc/customizer.php` | Registers all Customizer sections, settings, and controls |
| `tqs-theme/inc/theme-options.php` | Helper functions, getters, dynamic CSS, analytics, logo logic, selective refresh |

### 3.2 Modified Files

| File | Changes |
|---|---|
| `functions.php` | Requires `inc/` files; adds `custom-logo` support; breadcrumbs respect setting; SEO/schema uses identity logo; contact form uses custom messages |
| `front-page.php` | Hero slider, stats, services, Why Us, and CTA sections read from Customizer |
| `header.php` | Brand tagline, topbar location, CTA URLs, logo via `tqs_render_site_logo()` |
| `footer.php` | Logo, social toggle, legal badges (KvK/BTW/ND), column titles, cookie button labels |
| `404.php` | Title, message, and button text from Customizer |
| `archive-tqs_service.php` | Archive title and intro from Customizer |
| `page-contact.php` | Map toggle and custom embed URL |
| `page.php` | CTA banner uses Customizer values |
| `style.css` | Logo CSS updated for natural aspect ratio; footer logo styles |
| `assets/js/main.js` | Hero autoplay interval, cookie expiry, form error message from `tqsData` |

### 3.3 Unchanged (By Design)

Per-page meta boxes remain on the page/service editor:

- Page Hero Image
- Page Options (title override, hide hero/header/footer)
- Gallery images (Fotogalerij page)
- Service icon (Diensten CPT)

---

## 4. Customizer Panel Structure

**Path:** Appearance → Customize → 🛡️ TQS Theme Settings

### 4.1 Site Identity & Branding

| Setting key | Type | Description |
|---|---|---|
| `tqs_header_logo` | Image | Logo shown in the site header |
| `tqs_footer_logo` | Image | Optional footer override (falls back to header logo) |
| `tqs_identity_logo` | Image | SEO / search engine logo (JSON-LD schema) |
| `tqs_brand_sub` | Text | Tagline under site name in header (default: `BEVEILIGINGSDIENSTEN`) |
| `tqs_og_default_image` | Image | Default Open Graph / social share image |

**Bundled fallbacks** (when no Customizer upload exists):

- Header/footer → `assets/images/top-logo.png`
- Identity/SEO → `assets/images/top-Identity-logo.png`

**Logo sync:** Saving `tqs_header_logo` or WordPress Site Identity → Logo keeps both in sync via `customize_save_after`.

### 4.2 Brand Colors & Fonts

Eight color pickers map to CSS custom properties in `style.css`:

| Setting key | CSS variable | Default |
|---|---|---|
| `tqs_color_primary` | `--tqs-primary` | `#2D0A4E` |
| `tqs_color_secondary` | `--tqs-secondary` | `#8B2FC9` |
| `tqs_color_gold` | `--tqs-gold` | `#C9973A` |
| `tqs_color_gold_light` | `--tqs-gold-light` | `#E8C06A` |
| `tqs_color_dark` | `--tqs-dark` | `#1A0533` |
| `tqs_color_light` | `--tqs-light` | `#F9F6FF` |
| `tqs_color_text` | `--tqs-text` | `#4a3a5e` |
| `tqs_color_muted` | `--tqs-muted` | `#9080A8` |

Changed colors are output as an inline `<style>` block in `wp_head` via `tqs_output_brand_css()`. Unchanged colors continue to use `style.css` defaults.

### 4.3 Header & Navigation

| Setting key | Type | Default |
|---|---|---|
| `tqs_phone` | Text | `+31 (0)70 123 4567` |
| `tqs_email` | Text | `info@topqualitysecurity.com` |
| `tqs_show_topbar` | Checkbox | On |
| `tqs_topbar_location` | Text | `Den Haag, Nederland` |
| `tqs_header_cta_text` | Text | `Offerte Aanvragen` |
| `tqs_header_cta_url` | URL/path | `/contact` |
| `tqs_show_whatsapp_fab` | Checkbox | On |
| `tqs_show_breadcrumbs` | Checkbox | On |

### 4.4 Homepage — Hero Slider (Global)

| Setting key | Type | Default |
|---|---|---|
| `tqs_hero_autoplay` | Checkbox | On |
| `tqs_hero_autoplay_interval` | Number | `5500` (ms) |
| `tqs_hero_show_arrows` | Checkbox | On |
| `tqs_hero_show_dots` | Checkbox | On |
| `tqs_hero_btn1_text` / `tqs_hero_btn1_url` | Text / URL | Offerte Aanvragen / `/contact` |
| `tqs_hero_btn2_text` / `tqs_hero_btn2_url` | Text / URL | Onze Diensten → / `/onze-diensten` |

### 4.5 Hero Slide 1, 2, 3 (Per Slide)

Each slide (`tqs_slide_{0|1|2}_*`) has:

| Field | Type |
|---|---|
| `_enabled` | Checkbox |
| `_image` | Image (optional background photo) |
| `_gradient` | Textarea (CSS gradient fallback) |
| `_icon` | Text (emoji or icon) |
| `_badge` | Text |
| `_title` | Text |
| `_highlight` | Text (gold span) |
| `_subtitle` | Textarea |

Slides are assembled by `tqs_get_hero_slides()` in `inc/theme-options.php`.

### 4.6 Default Hero Image

| Setting key | Type | Purpose |
|---|---|---|
| `tqs_hero_image` | Image | Fallback hero background for inner pages when no page-specific image is set |

### 4.7 Homepage — Stats Bar

| Setting key | Type |
|---|---|
| `tqs_show_stats` | Checkbox (section visibility) |
| `tqs_stat_{0–3}_value` | Text |
| `tqs_stat_{0–3}_label` | Text |

### 4.8 Homepage — Services Section

| Setting key | Type |
|---|---|
| `tqs_show_services` | Checkbox |
| `tqs_services_eyebrow` | Text |
| `tqs_services_title` | Text |
| `tqs_services_lead` | Textarea |

Service cards themselves still come from the **Diensten** CPT.

### 4.9 Homepage — Why Us

| Setting key | Type |
|---|---|
| `tqs_show_why_us` | Checkbox |
| `tqs_why_eyebrow` | Text |
| `tqs_why_title` | Text |
| `tqs_why_text` | Textarea |
| `tqs_why_{0–3}_icon` | Text (Font Awesome class) |
| `tqs_why_{0–3}_title` | Text |
| `tqs_why_{0–3}_desc` | Textarea |

### 4.10 Homepage — CTA Banner

| Setting key | Type |
|---|---|
| `tqs_show_cta` | Checkbox |
| `tqs_cta_title` | Text |
| `tqs_cta_text` | Textarea |
| `tqs_cta_btn1_text` / `tqs_cta_btn1_url` | Text / URL |
| `tqs_cta_btn2_text` | Text (phone button; links to `tqs_phone`) |

### 4.11 Company Information

| Setting key | Type |
|---|---|
| `tqs_kvk` | Text |
| `tqs_btw` | Text |
| `tqs_nd_cert` | Text (default: `ND 7099`) |
| `tqs_address` | Text |
| `tqs_hours` | Textarea |
| `tqs_whatsapp_number` | Text |

### 4.12 Contact & Forms

| Setting key | Type |
|---|---|
| `tqs_contact_recipient` | Email (empty = header email) |
| `tqs_form_success_msg` | Textarea |
| `tqs_form_error_msg` | Textarea |
| `tqs_show_contact_map` | Checkbox |
| `tqs_maps_embed_url` | URL |

### 4.13 Footer Settings

| Setting key | Type |
|---|---|
| `tqs_footer_tagline` | Textarea |
| `tqs_copyright` | Text (`{year}` placeholder supported) |
| `tqs_show_footer_social` | Checkbox |
| `tqs_show_footer_kvk` | Checkbox |
| `tqs_show_footer_btw` | Checkbox |
| `tqs_show_footer_nd` | Checkbox |
| `tqs_footer_col_quick` | Text |
| `tqs_footer_col_services` | Text |
| `tqs_footer_col_contact` | Text |
| `tqs_facebook_url` | URL |
| `tqs_instagram_url` | URL |
| `tqs_linkedin_url` | URL |
| `tqs_x_url` | URL |

### 4.14 SEO & Analytics

| Setting key | Type |
|---|---|
| `tqs_home_meta_description` | Textarea |
| `tqs_ga_id` | Text (GA4, e.g. `G-XXXXXXXX`) |
| `tqs_gtm_id` | Text (e.g. `GTM-XXXXXXX`) |
| `tqs_archive_services_title` | Text |
| `tqs_archive_services_lead` | Textarea |

Built-in SEO defers to Yoast SEO or Rank Math when either plugin is active.

### 4.15 404 Page

| Setting key | Type |
|---|---|
| `tqs_404_title` | Text |
| `tqs_404_message` | Textarea |
| `tqs_404_btn_text` | Text |

### 4.16 GDPR Cookie Banner

| Setting key | Type |
|---|---|
| `tqs_cookie_enabled` | Checkbox |
| `tqs_cookie_text` | Textarea (HTML allowed) |
| `tqs_cookie_accept_text` | Text |
| `tqs_cookie_decline_text` | Text |
| `tqs_cookie_expiry_days` | Number (default: `180`) |

---

## 5. Key Helper Functions

Defined in `inc/theme-options.php`:

| Function | Purpose |
|---|---|
| `tqs_get_hero_slides()` | Returns active homepage slider slides |
| `tqs_get_why_us_cards()` | Returns Why Us card data |
| `tqs_get_stats()` | Returns stats bar values |
| `tqs_show_home_section( $section )` | Checks section visibility (`stats`, `services`, `why_us`, `cta`) |
| `tqs_show_breadcrumbs()` | Breadcrumb visibility |
| `tqs_theme_mod_url( $key, $default )` | Resolves relative paths (e.g. `/contact`) to full URLs |
| `tqs_get_header_logo_url()` | Header logo resolution chain |
| `tqs_get_footer_logo_url()` | Footer logo resolution chain |
| `tqs_get_identity_logo_url()` | SEO/schema logo resolution chain |
| `tqs_render_site_logo( $context )` | Outputs header or footer `<img>` markup |
| `tqs_output_brand_css()` | Injects overridden brand color CSS variables |
| `tqs_output_analytics()` | Injects GA4 and/or GTM scripts |
| `tqs_get_default_og_image_url()` | OG image with identity logo fallback |

---

## 6. Logo System (Detailed)

### 6.1 Resolution Priority

**Header logo** (`tqs_get_header_logo_url`):

1. `tqs_header_logo` (Customizer upload)
2. WordPress `custom_logo` (Site Identity)
3. `assets/images/top-logo.png`

**Footer logo** (`tqs_get_footer_logo_url`):

1. `tqs_footer_logo` (Customizer upload)
2. Header logo chain above

**Identity / SEO logo** (`tqs_get_identity_logo_url`):

1. `tqs_identity_logo` (Customizer upload)
2. WordPress `custom_logo`
3. `assets/images/top-Identity-logo.png`
4. Header logo chain

### 6.2 Rendering Rules

- Logos render as a plain `<img>` inside the brand link — no nested anchors.
- Full attachment size is used to preserve proportions.
- CSS uses `max-height` and `object-fit: contain` — no forced square crop.
- Text fallback `TQS` displays only when no image source exists.

### 6.3 SEO Integration

On the homepage, JSON-LD `SecurityService` schema includes:

```json
"logo": "<identity logo URL>",
"image": "<identity logo URL>"
```

Open Graph default image falls back to the identity logo when no dedicated OG image is set.

### 6.4 Customizer Live Preview

Selective refresh partials are registered for `.tqs-brand-logo` and `.tqs-footer-brand-logo`, watching `tqs_header_logo`, `tqs_footer_logo`, and `custom_logo`.

---

## 7. JavaScript Integration

`assets/js/main.js` receives settings via `wp_localize_script` as `tqsData`:

| Key | Source |
|---|---|
| `cookieExpiry` | `tqs_cookie_expiry_days` |
| `heroAutoplay` | `tqs_hero_autoplay` |
| `heroAutoplayMs` | `tqs_hero_autoplay_interval` |
| `formErrorMsg` | `tqs_form_error_msg` |
| `whatsapp` | `tqs_whatsapp_number` (sanitized) |
| `ajaxUrl` / `nonce` | Contact form AJAX |

---

## 8. Admin User Guide (Quick Reference)

### Changing the site logo

1. Go to **Appearance → Customize → 🛡️ TQS Theme Settings → Site Identity & Branding**
2. Upload **Header logo** (and optionally **Footer logo** / **Identity logo**)
3. Click **Publish**

Alternatively, use **Appearance → Customize → Site Identity → Logo** — it syncs with the header logo.

### Changing homepage content

Edit the relevant section under **🛡️ TQS Theme Settings** (Hero Slider, Stats Bar, Services Section, Why Us, CTA Banner). Each section has a visibility toggle.

### Changing brand colors

Use **Brand Colors & Fonts**. Only colors you change are overridden; defaults remain in `style.css`.

### Per-page hero images

Still managed on the individual page editor via the **🖼️ Page Hero Image** meta box (not in Theme Settings).

---

## 9. What Was Not Implemented (Future Phases)

The following items were discussed in planning but deferred:

- Dedicated admin page under Appearance (separate from Customizer)
- Typography font-family pickers in Customizer
- Inner-page hero height / overlay opacity controls
- reCAPTCHA on contact form
- Self-hosted Google Fonts toggle
- Elementor-specific global options
- "Reset to brand defaults" button for colors

---

## 10. Testing Checklist

After deploying the updated theme, verify:

- [ ] **🛡️ TQS Theme Settings** panel appears in the Customizer
- [ ] Header logo uploads and displays at natural proportions
- [ ] Footer logo uploads (or inherits header logo)
- [ ] Identity logo appears in page source JSON-LD on homepage
- [ ] Hero slider slides are editable (text, image, enable/disable)
- [ ] Stats bar, Services, Why Us, and CTA sections respect visibility toggles
- [ ] Brand color change updates header/footer without breaking layout
- [ ] Cookie banner button labels and expiry work
- [ ] Contact form success/error messages reflect Customizer values
- [ ] GA4 / GTM IDs inject scripts when set
- [ ] Breadcrumbs can be disabled globally
- [ ] 404 page copy reflects Customizer values
- [ ] Per-page meta boxes still work (hero image, hide header/footer, gallery)

---

## 11. Deployment Notes

- Theme source lives in `tqs-theme/` at the repository root.
- A mirrored copy may also exist at `wp-content/themes/tqs-theme/` for deployment.
- To install via WordPress upload, the zip must contain `tqs-theme/style.css` at exactly one folder level inside the archive (zip the `tqs-theme` folder itself, not its inner files alone).
- Favicon / site icon is still managed via WordPress **Site Identity → Site Icon**, not the TQS panel.

---

## 12. Summary

The TQS theme now provides a comprehensive, native WordPress Customizer panel covering branding, colors, homepage sections, company information, contact forms, footer, SEO, analytics, GDPR, and error pages. Logo handling was rebuilt to support admin uploads, WordPress Site Identity sync, bundled theme assets, and correct display proportions. Per-page controls remain on the editor, preserving the hybrid content-management model defined in the project brief.
