# Top Quality Security — WordPress Theme Development

Custom WordPress theme project for **Top Quality Security**, a professional security company based in Den Haag, Netherlands, certified as an officially recognised training company (*Erkend leerbedrijf* ND 7099).

## Domains

| | |
|---|---|
| **Current live site** | https://topqualitysecurity.nl/ (Strato Website Builder — being replaced) |
| **New site (in development)** | https://topqualitysecurity.com/ (currently in maintenance mode) |

## Project Goal

Rebuild the entire website as a custom, standalone WordPress theme (`tqs-theme`, v2.0.0) on the new `.com` domain. The theme is:

- Sleek and modern, matching the official TQS brand identity
- Fully manageable via the WordPress Dashboard (Customizer + per-page meta boxes) — no code changes needed for day-to-day content updates
- SEO-optimised (meta tags, Open Graph, JSON-LD schema, semantic HTML) with automatic fallback if Yoast SEO / RankMath is not installed
- GDPR-compliant (cookie consent banner)
- Fully responsive (desktop, tablet, mobile)
- Compatible with the Elementor page builder for future visual editing

## Brand Identity

| Colour | Hex | Usage |
|---|---|---|
| Deep Purple | `#2D0A4E` | Primary / main backgrounds |
| Vivid Purple | `#8B2FC9` | Secondary / highlights, hover states |
| Gold | `#C9973A` | Accent / CTAs, borders, icons |
| Light Gold | `#E8C06A` | Hover states, gradients |
| Dark Background | `#1A0533` | Footer, overlays |
| Light Background | `#F9F6FF` | Alternating sections |

**Fonts:** Plus Jakarta Sans (headings) · Inter (body)

## Site Structure — 13 Pages

- **Home** — Hero slider, stats bar, services slider, "Why Us", CTA
- **Wie Zijn Wij** (About)
- **Onze Diensten** (Services archive)
- 7× Service sub-pages: Retailbeveiliging, Horecabeveiliging, Evenementenbeveiliging, Hotelbeveiliging, Objectbeveiliging, Supermarktbeveiliging, Casino's Beveiliging
- **Fotogalerij** (Gallery)
- **Contact**
- **Privacybeleid** (Privacy Policy) · **Algemene Voorwaarden** (Terms & Conditions)

All content is in Dutch. Hero backgrounds are image-based, controlled via the WordPress Customizer (site-wide default) and per-page meta boxes (page-specific override) — the client uploads real photography after theme installation.

## Repository Structure

```
├── design/            Claude Design source files (.dc.html) — design reference only, not deployed
├── docs/              Project brief & technical handover documentation
└── wp-content/
    └── themes/
        └── tqs-theme/ The actual WordPress theme — this is what gets uploaded to WordPress
```

Only the contents of `wp-content/themes/tqs-theme/` are deployed to the live WordPress installation. `design/` and `docs/` are reference material for the development team.

## Status

Theme files are under active development. See `docs/` for the full technical handover document and `wp-content/themes/tqs-theme/readme.txt` for installation steps and file-by-file breakdown.