# Reference teardown: krishival.com (structure and design blueprint)

Scope: structural/design reference only for the DMC Nuts redesign. No text, images or branding to be copied.
Inspected 2026-10-21 via DOM/computed-style inspection at 1280px desktop and 375px mobile (screenshots were partly unavailable, so measurements come from `getBoundingClientRect` / `getComputedStyle`).
Pages: `/` (home), `/collections/all`, `/products/krishival-nuts-premium-brazil-nuts-250g` (1 image, 1 size), `/products/king-of-cashews-w180` (2 sizes), `/products/popular-cashews-california-almonds-pistachios` (4 images, thumbnails).

Platform: Shopify (theme with "layout-3" header + many custom `k-*` sections hand-coded in custom liquid). Reviews app: Judge.me (popup social-proof cards); checkout: Shiprocket headless "Buy Now".

---

## 1. Global

### Header
- No announcement bar (topbar section exists but empty).
- Height approx. 95-100px, white, **sticky** (`has-sticky`).
- Desktop: logo **left** (approx. 110x85 box, logo image 150px wide), nav **centered/left-center** inline, icons **right** (search, cart with count bubble in orange).
- Nav items: Shop · Gift Boxes · Combo · Products (mega-ish dropdown listing every nut category, approx. 20 items) · Everyday Munch · Blog (dropdown) · Track Your Order. Active/hover item = orange (`--link-hover` #FF9E10).
- Mobile: hamburger left, logo centered, cart right; off-canvas drawer from the left (300px) with menu items approx. 70px apart and social icons at bottom.

### Footer
- Light (transparent/white bg), dark text. 4 link columns: Company / Customer Services / Information / "Our Partners" (quick-commerce + marketplaces list). Logo image, 3 social links.
- Bottom bar (approx. 83px): copyright only.
- Mobile: columns become accordions (heading row with chevron, 1px dividers).

### Typography
- Body: **DM Sans** (custom @font-face 400/500/700), 15px, line-height 1.6, colour #282828.
- Theme headings: **Arimo** 700 (section titles 32-48px, centered).
- Custom sections (hero, PDP): **Outfit** 600-800 for display headings (PDP H1 42px/800, line-height 1.1; price 38px/800). Hero H2 approx. 45px/800.
- Eyebrow labels: 12px, 700, uppercase, letter-spacing 1.5px, primary green, followed by a 30px x 2px rule (`::after`).
- Buttons: 13-14px, 700-800, uppercase, letter-spacing 0.5px.
- All sans-serif: the "premium" feel comes from heavy weights + generous size contrast, not serifs.

### Colour usage
- Base: white backgrounds, near-black text #1A1A1A / #282828, grey body copy #666.
- Primary (PDP): deep forest green **#2E5C35** (eyebrow, active size pill, Buy Now).
- Accent: warm brown **#8B5A2B** (active thumbnail border).
- CTA: orange gradient `linear-gradient(135deg,#FF9800,#F57C00)`; hover deepens to `#F57C00 → #E65100`.
- Secondary CTA colour: amber/yellow #FFC107 (quick-delivery button).
- Soft pastel card tints: cream #FFF8E1, mint #E8F5E9, peach #FFF5E6 / #FFF3E0.
- Tag/badge colours: green text on mint, orange text on peach; discount badge red #E53935 circle.
- Section backgrounds: subtle gradient `#FAFCF8 → #FFFFFF`.

### Spacing / radius / shadow
- Container approx. 1200px, gutter 1.5rem. Section padding approx. 80px vertical on custom sections.
- Radius scale: 4px (tags), 6px (card buttons), 12px (product cards, size pills, thumbs), 16px (PDP image card, blog cards), 20-24px (feature cards / comparison table), 32px (hero bento cards), 50px (pill CTAs, qty capsule), 50% (category circles, badges).
- Shadows: very soft and large: `0 10px 40px rgba(0,0,0,.04)`, `0 4px 10px rgba(0,0,0,.03)`, `0 20px 40px rgba(0,0,0,.05)`, comparison card `0 15px 50px rgba(0,0,0,.08)`. CTAs carry coloured glow (`0 4px 10px rgba(255,152,0,.2)`).
- "3D" icon tiles: white 48px square, radius 14px, `box-shadow: 0 4px 0 #E0E0E0, 0 8px 10px rgba(0,0,0,.1)` (keycap look), press-down on hover.

### Buttons
- Primary (cards): full-width, orange gradient, radius 6px, 12px padding, uppercase "ADD". Disabled states grey #CCC ("SOLD OUT", "COMING SOON").
- Primary (PDP): pill (radius 50px), 48px tall, gradient orange + `0 6px 20px rgba(0,0,0,.2)`, cart icon + label.
- Secondary (PDP): solid green pill "BUY NOW" (fallback style: white with 2px green border).
- Tertiary: full-width amber 48px radius-12 "10 MIN DELIVERY" with infinite shimmer sweep (`::after` gradient animation).
- Hover: lift `translateY(-2px/-3px)` + stronger shadow; all transitions 200-300ms.

### Image treatment
- Product packshots on white with `object-fit: contain; mix-blend-mode: multiply` (packs float on the card background, no box).
- Large padding around PDP image (40px inside a 16px-radius white card).
- Category thumbnails: 100px circles with white 3px ring + light grey inner; highlighted ones get orange ring.

### Hover / motion
- Product card: lifts 4px, shadow deepens, border darkens; second image cross-fades in (opacity 0→1, 0.4s) and both scale 1.03.
- Marquee tickers (`tickerScroll`) in hero frame and a coloured "pill" tape strip.
- Rotating circular "PLAY VIDEO" text badge (`rotateText`), floating hero image (`floatHero`).
- Tab content slide-up (`animate-tab-slide-up`), comparison checkmarks pop (`kPop`).
- PDP image card lifts 5px on hover; active thumbnail lifts 3px.
- Judge.me popup "recent review" cards bottom corner (social proof).

---

## 2. Homepage (top to bottom, desktop 1280px)

1. **Sticky header** (approx. 95px) as above.
2. **Hero bento grid** (approx. 780px section, grid 600px tall, approx. 1190px wide):
   - Left big card (approx. 780x600, cream bg, radius 32px, soft shadow) framed top and bottom by **orange marquee strips** (34px) of tiny USP text. Inside: text column (eyebrow-less heavy H2 approx. 45px, one-line subtext, circular rotating "play video" badge with orange play button) + floating product image on the right.
   - Right column: two stacked promo cards (approx. 390x290 each, pastel mint / pastel orange, radius 32px) each with H3 (26px/800), one line, and a pill CTA (green / orange).
3. **USP ticker tape** (85px): peach band, horizontally scrolling multicoloured pill chips (rust, mustard, green, brown, red) with white text: protein, no sugar, gluten free, fibre, non-GMO, vegan, heart-healthy.
4. **Category carousel "Featured Collection"** (approx. 250px): row of 100px circular thumbnails with label below; round white prev/next arrows at top right; approx. 9 visible, swipeable.
5. **"Why choose us" + video** (approx. 690px): large 48px heading + subtext, background video/image with radial-gradient "blob" decorations; 3 stacked feature row-cards (gradient white→tint, radius 20px) each with a coloured 18px-radius icon box + title (19px/700 in its own colour: green / orange / blue) + one line.
6. **Recipe reels / shoppable video carousel** ("Try our recipes", approx. 580px): vertical 9:16 video cards (radius 10px) in a swiper (third-party reel app).
7. **Comparison table "Why [brand] is better"** (approx. 740px): centered heading + subline; one big white card (radius 24px, big soft shadow) with 3 columns: Feature | Other brands (grey header, red ×) | Our brand (orange header, green ✓). 5 rows: sourcing, processing, packaging, grading, value.
8. **Product tabs "Popular Products"** (approx. 1000px): centered H2, tab row (All + 5 categories), grid of **5 columns x 2 rows** of product cards (approx. 223x434).
9. **Blog carousel "Read Our Blogs"** (approx. 630px): 4 visible cards, white, radius 16px, shadow `0 10px 30px rgba(0,0,0,.08)`, image top, 28px/24px padding, 18px/700 title, orange "READ ARTICLE" text link.
10. **Footer** (approx. 380px) + bottom bar (83px).
Overlays: cookie popup, sales-notification popup, Judge.me review popups.

---

## 3. Shop / collection page (`/collections/all`)

- Header → **same circular category carousel** (approx. 236px) → collection section.
- **Left sidebar filters** (approx. 220px, white card with soft shadow): accordion groups with chevrons: Availability (checkbox + counts), Price (range), Product Type, Choose Weight. Not sticky.
- Top of the main column: a **"Deals Of The Day" strip** (heading left, "All Deals" link right) showing 2 deal cards, then "All Products" H1.
- Toolbar: "Filter and sort" (mobile trigger) + "Sort by: Featured" dropdown.
- **Grid: 4 columns** on desktop (cards approx. 225px wide, approx. 415px row pitch).
- **Product card anatomy** (top to bottom), white, 1px #E0E0E0 border, radius 12px, shadow `0 4px 10px rgba(0,0,0,.03)`:
  1. Square image area (1:1) with **hover image swap** (cross-fade + scale 1.03).
  2. **Discount badge**: 40px red circle top-right, "43% OFF" white 11px/700.
  3. USP tags row: tiny 9px/700 uppercase chips ("100% NATURAL" green-on-mint, "PREMIUM" orange-on-peach), radius 4px.
  4. Title 15px/600 #222 (2-line clamp).
  5. Price row: price 16px/800 #111 + strike-through MRP 13px #999.
  6. Optional **weight `<select>`** (36px tall, 1px border, radius 6px, 12px text) when multiple variants.
  7. Full-width **ADD** button (orange gradient, 49px, radius 6px, uppercase). Greys out for Sold Out / Coming Soon.
- No ratings on cards. No wishlist/quick-view visible.
- **Pagination**: numbered (1 2 3 4), hover fills orange.

---

## 4. Single product page (most important)

Section order (desktop): Header → Product main (approx. 1150-1550px) → USP ticker tape (85px) → "Our Bestsellers" carousel (approx. 840px) → Comparison table (approx. 735px) → Footer.

### 4.1 Product main section (`k-product-section`)
Background: very subtle vertical gradient #FFFFFF → #FAFCF8. Container 1200px, two columns via flex: **gallery flex 1.2 (approx. 630px) / info approx. 480px**, 50px gap.

0. **Breadcrumbs** full width above columns: Home / Shop / Product (15px, plain).

**Left column: gallery (position: sticky, top 60px)**
- Main image card: 578x578 square (aspect-ratio 1/1), white, radius 16px, 40px inner padding, shadow `0 10px 40px rgba(0,0,0,.04)`, hairline border; image `object-fit: contain` + `mix-blend-mode: multiply`. Card lifts 5px on hover.
- **Click opens full-screen zoom modal** (near-white 98% overlay, image max 90vh/90vw) rather than hover-lens zoom.
- **Thumbnails horizontal, centered under the main image**: 65x65, radius 12px, white with 5px padding, soft shadow, 12px gap; active = 2px accent-brown border + lifted 3px. Hidden when only 1 image.

**Right column: info (top to bottom)**
1. **Eyebrow brand line**: "BRAND PREMIUM" 12px/700 uppercase, letter-spacing 1.5px, green, with 30px short rule after.
2. **H1 title**: Outfit 42px/800, line-height 1.1, near-black. (Long SEO titles wrap to 4-5 lines; a shorter product name looks far better.)
3. **Rating row**: 5 gold stars (#FFB300) + "(128 Verified Reviews)" underlined grey 13px link (anchors to reviews).
4. **Price row**: price Outfit 38px/800 + compare-at 22px strike-through #AAA (baseline aligned, 15px gap). No "You save" chip on the inspected items.
5. **Scarcity bar**: "Hurry! Only N packs left in stock!" 13px/700 orange-red, then an 8px progress track (pale amber) with orange→yellow gradient fill.
6. **USP / offer grid**: 4-column grid of icon tiles (48px "3D keycap" icons + 11px uppercase label); first cell is a text-only offer line ("Up to X% discount at checkout").
7. **Variant selector**: label "SELECT SIZE:" 13px/700 uppercase, then **pill buttons** (padding 10px 20px, radius 12px, 2px #EEE border, 14px/700); active = green border + green text + mint bg + soft green glow.
8. **Actions row**: 
   - **Quantity capsule** (107x48, #F5F5F5, radius 50px, round −/+ buttons, bold number) on the left;
   - right side stack: **ADD TO CART** (orange gradient pill, cart icon) + **BUY NOW** (solid green pill, payment-method logos inline) side by side (176px each, 48px tall);
   - below them a full-width **amber "10 MIN DELIVERY" button** (radius 12px, shimmer animation) that opens a **left slide-in drawer** (320px) listing quick-commerce partner links with arrows.
9. **Accordion(s)**: native `<details>`; head 20px vertical padding, 700 weight, "+" icon right, 1px #EEE bottom border; body 15px #666, line-height 1.6. Only "Description" present on inspected products (opened by default).
10. **AI Q&A widget** (third-party, approx. 233px): chat-style card with header strip, bubble, input pill.

Not present on the reference (we should add for DMC): pincode/delivery-date check, nutrition table, benefits / how-to-use / storage accordions, FAQ, inline reviews list, "frequently bought together".

### 4.2 Below the fold
- **USP ticker tape**: same coloured pill marquee as homepage.
- **"Our Bestsellers"** carousel: centered 48px heading, Swiper with 4 visible product cards (same card anatomy as collection), loop, dot pagination.
- **Comparison table** (same as homepage) acting as the "why choose us" block.
- Reviews: only Judge.me floating popup cards; no full reviews section on page.

### 4.3 Mobile (<=900px)
- Order changes: breadcrumbs → **mobile header block (eyebrow, title 26px, rating 13px, price)** placed ABOVE the gallery → image card (full width 335px) → thumbnails → rating/stock bar → USP grid → size pills → accordion → Q&A.
- Gallery loses sticky; info column full width.
- **In-flow quantity/ATC row is hidden**; replaced by a **fixed bottom sticky bar** (white, shadow `0 -5px 25px rgba(0,0,0,.15)`, approx. 68px, safe-area padding): ADD TO CART (orange pill with icon) + BUY NOW (dark green pill with payment logos) 50/50, plus a second row "10 Min Delivery".
- Header: hamburger left, centered logo, cart right.
- Desktop has no sticky ATC bar on scroll; the gallery is sticky instead.

---

## 5. Design moves worth emulating (premium feel)

1. **Packshots floating on white**: `object-fit: contain` + `mix-blend-mode: multiply` inside big-radius white cards with generous padding and whisper shadows.
2. **Heavy geometric sans display type** (Outfit/DM Sans 800) at large sizes against small, tracked uppercase eyebrows with a short rule: strong typographic hierarchy.
3. **Two-colour system**: one deep natural brand colour (forest green) for trust/selection states + one warm gradient (orange) reserved for buy actions; pastel tints for supporting cards.
4. **Consistent radius scale** (12 / 16 / 24-32 / pill) and soft, large-blur, low-opacity shadows; coloured glow only on CTAs.
5. **Bento hero** (one large card + two stacked promo cards) instead of a plain slider.
6. **Motion in small doses**: marquee tickers, card lift on hover, image swap on hover, shimmer on a secondary CTA, rotating badge.
7. **Pill variant selectors** with tinted active state, **capsule quantity stepper**, pill CTAs side by side.
8. **Sticky gallery on desktop / sticky ATC bar on mobile**, with the title+price moved above the image on mobile.
9. **Proof stacking near the buy box**: rating count, scarcity bar, USP icon tiles, offer line.
10. **Comparison table "us vs others"** as a reusable trust section on home and PDP.
11. Circular category rail reused on home and collection pages for navigation.

Improve on the reference: shorter PDP titles, real reviews section, nutrition/benefits/storage accordions, pincode check, add a "you save X%" chip, add ratings to cards.
