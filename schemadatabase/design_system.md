
---

# 🎨 Tailwind CSS v4.2 Design System

## 1. Core Principles

* **Utility-First:** Build designs directly in markup using functional classes.
* **Unapologetically Modern:** Built for the latest CSS features (P3, Grid, Container Queries).
* **Zero-Runtime/Smallest Bundle:** Automatic removal of unused CSS (typically <10kB).
* **P3-First:** Default color palette uses wide-gamut colors for more vibrant displays.

---

## 2. Color Palette (OKLCH Space)

The system has migrated to the `oklch()` color space for better perceptual uniformity and access to P3 gamuts.

### **Primary Neutrals**

| Name | Value (Example) | Usage |
| --- | --- | --- |
| `white` | `oklch(1 0 0)` | Backgrounds, text on dark |
| `gray-950` | `oklch(0.05 0 0)` | Deepest text, dark backgrounds |

### **Brand Accent: Mint (New)**

| Shade | Value |
| --- | --- |
| `mint-100` | `oklch(0.97 0.15 145)` |
| `mint-500` | `oklch(0.7 0.28 145)` |
| `mint-950` | `oklch(0.3 0.4 145)` |

### **Vibrant Palettes**

Includes full scales (50-950) for:
`Red`, `Orange`, `Amber`, `Yellow`, `Lime`, `Green`, `Emerald`, `Teal`, `Cyan`, `Sky`, `Blue`, `Indigo`, `Violet`, `Purple`, `Fuchsia`, `Pink`, `Rose`.

---

## 3. Typography

High-contrast typography with tight spacing for headers.

* **Primary Font:** `"Inter", sans-serif` (Variable support)
* **Mono Font:** `"IBM Plex Mono", monospace`
* **Scale:**
* `text-tiny`: 0.625rem
* `text-base`: Standard Body
* `text-lg`: Featured Body
* `text-4xl` to `text-8xl`: Hero Headlines


* **Styling Patterns:**
* `tracking-tighter`: Required for all large headings.
* `text-balance`: Used for titles to ensure even line lengths.



---

## 4. Effects & Motion

The v4.2 system encourages "stacking" filters and 3D depth.

* **Glassmorphism:** Heavy use of `backdrop-blur` and `blur-sm`.
* **Transitions:** Standardized at `duration-750` with `ease-out` or `ease-in-out` for smooth UI.
* **3D Space:** Scaling and rotating on the Z-axis to add depth.
* **Gradients:** Created using simple utility classes without complex CSS math.

---

## 5. Layout & Responsive Logic

### **Container Queries (`@container`)**

Instead of just viewport-based breakpoints (`sm`, `md`), components now adapt to their parent container's size.

> **Example:** `<div class="grid grid-cols-1 @sm:grid-cols-2">`

### **Logical Properties**

The system supports LTR (Left-to-Right) and RTL (Right-to-Left) automatically using logical property utilities, making internationalization seamless.

---

## 6. CSS Architecture (`@layer`)

Tailwind v4 organizes the final CSS into four distinct layers to solve specificity issues:

1. **`theme`**: Where variables like `--color-mint-500` live.
2. **`base`**: Global reset (Preflight).
3. **`components`**: Complex, reusable class groups.
4. **`utilities`**: Atomic classes (e.g., `flex`, `pt-4`).

---

## 7. Example Implementation

Here is how you would define this system in your CSS file using the new v4 syntax:

```css
@theme {
  /* Typography */
  --font-sans: "Inter", sans-serif;
  --font-mono: "IBM Plex Mono", monospace;

  /* P3 Colors */
  --color-mint-500: oklch(0.7 0.28 145);
  
  /* Custom Sizes */
  --text-tiny: 0.625rem;
  --text-tiny--line-height: 1.5rem;
}

@layer utilities {
  .hero-heading {
    @apply text-8xl font-bold tracking-tighter text-balance text-gray-950 dark:text-white;
  }
}

```
