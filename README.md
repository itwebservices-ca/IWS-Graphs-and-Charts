A production-ready, ultra-lightweight, and secure shortcode-based charting engine for WordPress. Built entirely on top of HTML5 Canvas using **Chart.js v4.4.1**, this plugin delivers responsive, beautiful data visualizations without sacrificing your site's page-load performance.

Developed and maintained by **IT Web Solutions, Brampton** ([itwebsolutions.ca](https://itwebsolutions.ca)).

Developed for ([myfreetools.app](https://myfreetools.app/)).

---

## ⚡ Key Architectural Advantages

* **Zero Global Asset Bloat:** Bulky visualization frameworks typically load scripts site-wide. **IWS Graphs and Charts** safely registers scripts but defers loading them into your page payload until the `[iws_gc]` shortcode is actively parsed in content loops.
* **Modern Aesthetic Defaults:** Out-of-the-box support for an agency-grade corporate palette with smooth entrance animations and bezier curve pathing profiles.
* **Full Responsive Scaling:** Wrapped inside CSS flex containers to automatically adjust dimensions fluidly between desktop screens, tablets, and mobile layouts.
* **Enhanced Data Isolation:** Generates random, cryptographically isolated unique canvas string IDs to handle multiple independent chart layouts stacked on the same page.
* **Granular Input Sanitization:** Leverages strict core validation models (`sanitize_text_field`, `sanitize_key`, and `absint`) preventing Cross-Site Scripting (XSS) injection paths.

---

## 🛠️ Supported Visualizations & Copy-Paste Examples

Ensure data inputs are comma-separated string parameters **without trailing or internal spaces**. Suffix strings are systematically handled via the `unit` attribute.

### 1. Multi-Line Comparative Trend Graph
Perfect for mapping performance trajectories. Separate comparison lines using a pipe (`|`) block.
```text
[iws_gc type="line" title="Product Sales Comparison" labels="Q1,Q2,Q3,Q4" data="120,185,340,290 | 90,210,280,310 | 150,130,210,400" line_labels="Product A,Product B,Product C" unit="Units"]
2. Horizontal Leaderboard Chart
Ideal for ranking matrices or labels requiring extensive horizontal spacing profiles.

Plaintext
[iws_gc type="horizontalBar" title="Top Performing Sales Regions" labels="Toronto,Brampton,Vancouver,Montreal" data="840,910,620,530" unit="CAD"]
3. Vertical Bar Chart
Standard configuration layout built to scale and balance category options across structural axis intervals.

Plaintext
[iws_gc type="bar" title="Monthly Registration Growth" labels="January,February,March,April" data="120,185,340,290" unit="Users"]
4. Interactive Doughnut Layout
Sleek breakdown layouts mapping a single dataset to individual parts. The customized legend automatically reveals corresponding segment values and unit markers directly next to color filters.

Plaintext
[iws_gc type="doughnut" title="Client Acquisition Channels" labels="Organic Search,Direct,Socia
