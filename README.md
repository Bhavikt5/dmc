# DMC Nuts — website revamp

The redesign of dmcnuts.com, built first as HTML prototypes and then packaged for WordPress + Elementor (free) + WooCommerce.

## What's in this folder

| Path | What it is |
|---|---|
| `html/` | The approved HTML prototypes: `index.html` (Home), `about.html`, `shop.html` (shop archive), `product.html` (single product). |
| `wordpress/dmc-revamp.zip` | **The WordPress plugin to install.** Design system, GSAP + Three.js motion, header/footer, and the WooCommerce shop + single product templates. |
| `wordpress/elementor-templates/dmc-home.json` | Elementor template for the Home page. |
| `wordpress/elementor-templates/dmc-about.json` | Elementor template for the About Us page. |
| `wordpress/dmc-products-import.csv` | WooCommerce import file for the 9 almond grades. |
| `content/` | Content scraped from the current site + the reference-site (krishival.com) teardown. |
| `tools/` | Scripts that rebuild the pages, Elementor JSON and CSV from the prototypes. |

## Why a plugin (no Elementor Pro)

Elementor **free** cannot build the site header/footer, the shop archive or the single product page — those need Elementor Pro's Theme Builder. The `dmc-revamp` plugin fills that gap:

- prints the new header and footer on every page,
- replaces WooCommerce's shop/category and single-product templates with the new design (live product data, reviews, related products, schema),
- loads the fonts, CSS, GSAP/ScrollTrigger, Lenis smooth scroll and Three.js (Three.js only on pages with a 3D scene),
- adds shortcodes used by the Home template: `[dmc_grade_circles]`, `[dmc_product_grid limit="8"]`, `[dmc_grade_chart]`,
- adds **Bag colour** and **Badge** fields to each product (Product data → General).

Home and About stay fully in Elementor, so you can reorder, duplicate or delete sections in the editor.

## Install (do this on a staging copy first)

1. **Back up** the site (files + database).
2. **Theme:** install and activate **Hello Elementor** (Appearance → Themes → Add New). The plugin turns off Hello's own header/footer automatically.
3. **Plugin:** Plugins → Add New → Upload Plugin → `wordpress/dmc-revamp.zip` → Activate.
4. **Products:**
   - Delete (or draft) the 4 placeholder products that still have clothing-demo text and $250 prices.
   - Products → Import → choose `wordpress/dmc-products-import.csv` → run. It creates 9 products in the "Almonds" category, with Grain / Count / Bag Size / Variety Type attributes, bag colours and badges. Images are taken from your existing media library.
   - Prices are intentionally empty, so each product shows **Price on request** with Request Quote + WhatsApp buttons. If you add a price later, that product automatically gets a normal WooCommerce Add to Cart.
5. **Currency:** WooCommerce → Settings → General → Currency → Indian rupee (it's currently USD).
6. **Home page:**
   - Templates → Saved Templates → Import Templates → `dmc-home.json`.
   - Edit the Home page with Elementor → folder icon → My Templates → **Insert** "DMC — Home".
   - In the page settings (gear icon) set **Page Layout: Elementor Full Width** and **Hide Title: on**. Update.
7. **About page:** same as step 6 with `dmc-about.json` on the page whose slug is `about-us`.
8. **Settings → Permalinks:** "Post name". The templates link to `/shop/`, `/about-us/` and `/product/orange-bag-almonds/`.
9. Clear any cache plugin (WP Rocket: *Clear and preload cache*). If WP Rocket's "Delay JavaScript execution" is on, exclude `gsap`, `ScrollTrigger`, `lenis`, `three` and `dmc-revamp/assets/js` so the animations start immediately.

## Editing content

- **Text in Home / About:** open the page in Elementor; each section is an *HTML* widget (copy is plain HTML, keep the `class` and `data-*` attributes) or a *Shortcode* widget (pulls products automatically).
- **Products:** edit in WooCommerce as usual. The card/product page reads the **Grain**, **Count**, **Bag Size** and **Variety Type** attributes plus the Bag colour / Badge fields.
- **Phone, WhatsApp, email, address:** constants at the top of `dmc-revamp/dmc-revamp.php` (and `WA_NUMBER` in `assets/js/dmc.js`).

## Motion

- **GSAP + ScrollTrigger:** word-by-word headline reveals, staggered cards, image curtain reveals, parallax, count-ups, magnetic buttons, card tilt, marquees that reverse with scroll direction, pinned horizontal "how we work" scroller on About.
- **Three.js:** procedurally modelled almonds, cashews and pistachios floating in the Home hero (mouse parallax + scroll drift); golden dust particles on the About hero.
- Everything respects `prefers-reduced-motion`, and the 3D scenes pause when off-screen.

## Content notes (please review)

- Specs and copy come from the current site. Nutrition values on product pages are typical USDA values for raw almonds, labelled as such.
- I did **not** use the "Certified Organic – 100% Guarantee" claim from the old homepage, because nothing on the site backs it up. Add it back only if you hold the certification.
- The old About page's "founder" image is a watermarked process diagram from a machinery supplier, so it isn't used. Send a real photo of Deepak Arjan Mange and it can go on the Leadership card.
- Only 2 testimonials exist; the product page shows real WooCommerce reviews (none are invented).
- The bag photos are 4 MB PNGs in your media library. Converting them to WebP (e.g. with an image-optimisation plugin) will speed up the shop noticeably.

## Rebuilding after prototype changes

```bash
powershell -ExecutionPolicy Bypass -File tools/build-pages.ps1
```

```bash
node tools/build-elementor.js
```

```bash
node tools/build-products-csv.js
```
