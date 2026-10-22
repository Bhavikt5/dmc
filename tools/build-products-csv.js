// Writes a WooCommerce product CSV (Products > Import) for the nine DMC almond grades.
// Prices are left empty: the site sells on enquiry ("Price on request").
// Images point at files already in the dmcnuts.com media library, which the importer reuses.
//
// Usage: node tools/build-products-csv.js
const fs = require("fs");
const path = require("path");

const UP = "https://dmcnuts.com/wp-content/uploads/";
const LIFESTYLE = UP + "2025/09/ChatGPT-Image-Sep-20-2025-09_32_56-PM.png";
const DESCRIPTION =
  "At DMC, we bring you premium quality almonds sourced directly from trusted farms. Known for their rich taste, superior crunch, and natural freshness, our almonds are supplied in bulk to businesses across India and global markets.\n\n" +
  "Whether it’s snacking, gifting, or industrial use, our colorful bag varieties ensure consistency, trust, and excellence with every delivery.";

// name, sku, grain, count, bag size, variety type(s), image, swatch, badge
const PRODUCTS = [
  ["Orange Bag Almonds", "DMC-ORANGE-15", "100 – 105", "29 – 30", "15 Kg", "Classic", "2025/11/1-3.png", "#f39233", "Bestseller"],
  ["White Bag (American 3D) Almonds", "DMC-WHITE3D-15", "95 – 98", "27 – 28", "15 Kg", "3D", "2025/11/3-4.png", "#ededed", ""],
  ["Yellow Bag Almonds", "DMC-YELLOW-15", "92 – 95", "26 – 27", "15 Kg", "Classic", "2025/11/4.png", "#f2cc0e", ""],
  ["Pink Bag Almonds", "DMC-PINK-15", "85 – 88", "24 – 25", "15 Kg", "Classic", "2025/11/7-1.png", "#e2138c", "Popular"],
  ["Purple Bag Almonds", "DMC-PURPLE-15", "80 – 83", "23 – 24", "15 Kg", "Classic", "2025/11/6-1.png", "#8e3f8e", "Popular"],
  ["Blue Bag Almonds", "DMC-BLUE-15", "75 – 78", "21 – 22", "15 Kg", "Classic", "2025/11/9-4.png", "#4a4595", ""],
  ["Golden Bag Almonds", "DMC-GOLDEN-15", "68 – 74", "19 – 21", "15 Kg", "Classic", "2025/11/10-3.png", "#d9b877", ""],
  ["Independent 3D Bag Almonds", "DMC-IND3D-15", "85 – 88", "24 – 25", "15 Kg", "3D, Independent", "2025/11/8-1.png", "#e0303c", ""],
  ["Independent Bag Almonds", "DMC-IND-30", "95 – 100", "27 – 28", "30 Kg", "Independent", "2025/11/9-2.png", "#d02030", ""],
];

const header = [
  "Type", "SKU", "Name", "Published", "Is featured?", "Visibility in catalog", "Short description", "Description",
  "In stock?", "Regular price", "Categories", "Images", "Position", "Allow customer reviews?",
  "Attribute 1 name", "Attribute 1 value(s)", "Attribute 1 visible", "Attribute 1 global",
  "Attribute 2 name", "Attribute 2 value(s)", "Attribute 2 visible", "Attribute 2 global",
  "Attribute 3 name", "Attribute 3 value(s)", "Attribute 3 visible", "Attribute 3 global",
  "Attribute 4 name", "Attribute 4 value(s)", "Attribute 4 visible", "Attribute 4 global",
  "Meta: _dmc_swatch", "Meta: _dmc_badge",
];

const q = (v) => {
  const s = String(v);
  return /[",\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
};

const rows = PRODUCTS.map(([name, sku, grain, count, size, type, img, swatch, badge], i) => [
  "simple", sku, name, 1, badge === "Bestseller" ? 1 : 0, "visible",
  `Grain ${grain} · Count ${count} · ${size} bag. Sorted, graded and hygienically packed.`,
  DESCRIPTION, 1, "", "Almonds", UP + img + ", " + LIFESTYLE, i + 1, 1,
  "Grain", grain, 1, 0,
  "Count", count, 1, 0,
  "Bag Size", size, 1, 0,
  "Variety Type", type, 1, 0,
  swatch, badge,
]);

const csv = [header, ...rows].map((r) => r.map(q).join(",")).join("\n") + "\n";
const out = path.resolve(__dirname, "..", "wordpress", "dmc-products-import.csv");
fs.writeFileSync(out, csv, "utf8");
console.log("wrote " + out + " (" + rows.length + " products)");
