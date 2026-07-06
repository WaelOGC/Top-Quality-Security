=== TQS Theme ===
Theme Name: TQS Theme
Version: 2.0.1
Requires PHP: 8.0
Requires at least: WordPress 6.0

Custom standalone WordPress theme for Top Quality Security (topqualitysecurity.com).

== File Map ==
style.css                  Theme declaration + all CSS (custom properties, layout, components, responsive)
functions.php              Theme setup, enqueues, Customizer (6 panels), CPT registration, content seeding, meta boxes, SEO output, AJAX handler
header.php                 Sticky header, top bar, nav w/ dropdown, CTA, hamburger
footer.php                 4-column footer, GDPR cookie banner, WhatsApp FAB, back-to-top
front-page.php             Homepage: Hero slider (3), Stats, Services slider, Why Us, CTA
page.php                   Generic page template; special layouts for "Wie Zijn Wij" and legal pages
archive-tqs_service.php    Onze Diensten overview (2-col card grid)
single-tqs_service.php     Individual service page (content + sticky sidebar)
page-contact.php           Contact page (info cards, AJAX form, hours, WhatsApp, map)
page-fotogalerij.php       Gallery page (admin-managed images + lightbox, illustrated placeholders)
404.php                    Custom 404 page
assets/js/main.js          All frontend JS
assets/css/                Reserved for Elementor overrides
assets/images/             Placeholder directory for theme default images

== Setup ==
1. Zip this folder and upload via Appearance > Themes > Add New > Upload Theme, then Activate.
2. On activation, 7 services + 6 core pages are auto-seeded (after_switch_theme hook), and the front page is set automatically.
3. Configure Appearance > Customize > "TQS Theme Settings" (phone, email, hero, stats, company info, footer, cookie banner).
4. Appearance > Menus: assign Primary / Footer Quick Links / Footer Services / Footer Legal (fallback menus render automatically if unassigned).
5. Upload hero images per page via the "Page Hero Image" meta box, gallery photos via the Fotogalerij page meta box.
6. To re-run seeding during development: visit the site with ?tqs_reseed=1 while logged in as admin.

== Recommended Plugins ==
Elementor, Yoast SEO, WP Super Cache, UpdraftPlus, WP Mail SMTP, Wordfence Security.
