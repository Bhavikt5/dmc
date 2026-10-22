// Builds Elementor (free) page templates from the HTML prototypes.
// Each prototype section becomes a full-width Elementor section holding one HTML widget,
// except the product-driven sections, which become Shortcode widgets fed by WooCommerce.
//
// Usage: node tools/build-elementor.js
const fs = require("fs");
const path = require("path");
const crypto = require("crypto");

const root = path.resolve(__dirname, "..");
const outDir = path.join(root, "wordpress", "elementor-templates");
fs.mkdirSync(outDir, { recursive: true });

// Prototype file links -> WordPress URLs (site installed at the domain root).
const PLUGIN_ASSETS = "/wp-content/plugins/dmc-revamp/assets/";
const LINKS = [
  ['href="index.html#quote"', 'href="/#quote"'],
  ['href="index.html"', 'href="/"'],
  ['href="shop.html"', 'href="/shop/"'],
  ['href="product.html"', 'href="/product/orange-bag-almonds/"'],
  ['href="about.html"', 'href="/about-us/"'],
];

// Sections replaced by live WooCommerce shortcodes (matched on the prototype's section comment).
const SHORTCODES = {
  "SHOP BY GRADE": "[dmc_grade_circles]",
  BESTSELLERS: '[dmc_product_grid limit="8"]',
  "GRADE GUIDE": "[dmc_grade_chart]",
};

const id = () => crypto.randomBytes(4).toString("hex").slice(0, 7);

function toWp(html) {
  let out = html.split('src="assets/').join(`src="${PLUGIN_ASSETS}`);
  for (const [from, to] of LINKS) out = out.split(from).join(to);
  return out.trim();
}

function mainOf(html) {
  const a = html.indexOf("<main>");
  const b = html.indexOf("</main>");
  return a >= 0 && b > a ? html.slice(a + 6, b) : html;
}

// Split on the "<!-- ============ NAME ============ -->" comments that open each section.
function sections(html) {
  const re = /<!--\s*=+\s*(.+?)\s*=+\s*-->/g;
  const marks = [...html.matchAll(re)];
  return marks.map((m, i) => ({
    name: m[1].trim(),
    html: html.slice(m.index + m[0].length, i + 1 < marks.length ? marks[i + 1].index : html.length),
  }));
}

function widget(sec) {
  const sc = SHORTCODES[sec.name];
  if (sc) return { id: id(), elType: "widget", widgetType: "shortcode", settings: { shortcode: sc }, elements: [] };
  return { id: id(), elType: "widget", widgetType: "html", settings: { html: toWp(sec.html) }, elements: [] };
}

function elSection(sec) {
  return {
    id: id(),
    elType: "section",
    isInner: false,
    settings: {
      layout: "full_width",
      gap: "no",
      css_classes: "dmc-el",
      _title: sec.name,
      padding: { unit: "px", top: "0", right: "0", bottom: "0", left: "0", isLinked: true },
      margin: { unit: "px", top: "0", right: 0, bottom: "0", left: 0, isLinked: true },
    },
    elements: [
      {
        id: id(),
        elType: "column",
        isInner: false,
        settings: { _column_size: 100, _inline_size: null, padding: { unit: "px", top: "0", right: "0", bottom: "0", left: "0", isLinked: true } },
        elements: [widget(sec)],
      },
    ],
  };
}

function build(title, file, html) {
  const secs = sections(mainOf(html));
  const tpl = {
    version: "0.4",
    title,
    type: "page",
    page_settings: { template: "elementor_full_width", hide_title: "yes" },
    content: secs.map(elSection),
  };
  fs.writeFileSync(path.join(outDir, file), JSON.stringify(tpl, null, 1));
  console.log(`${file}: ${secs.length} sections — ${secs.map((s) => (SHORTCODES[s.name] ? s.name + " (shortcode)" : s.name)).join(", ")}`);
}

const read = (p) => fs.readFileSync(path.join(root, "html", p), "utf8");
build("DMC — Home", "dmc-home.json", read("index.html"));
build("DMC — About Us", "dmc-about.json", read("partials/about.main.html"));
