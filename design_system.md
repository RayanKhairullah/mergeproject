---

# 🎨 TALLStarter (Pelindo) Design System

## 1. Core Principles

* **Utility-First & Component-Driven:** Leverage Tailwind CSS v4.2 utility classes combined with Flux UI components.
* **Modern & Clean:** Emphasis on readability, accessibility, and clean interface structures primarily utilizing the Zinc secondary palette.
* **Zero-Runtime/Smallest Bundle:** Automatic removal of unused CSS.
* **Functional & Responsive:** Relies heavily on Tailwind's native Container Queries and logical properties for robust cross-device support.

---

## 2. Color Palette

The system utilizes Tailwind's native color scales with a strong emphasis on Zinc for neutrals and Blue for primary accents, defined in `@theme`.

### **Primary Neutrals (Zinc & Gray)**
- Light mode focuses on `white`, `zinc-50`, `zinc-100`.
- Dark mode utilizes `zinc-900`, `zinc-950`.

### **Brand Accent: Blue**
Used extensively for buttons, focus rings, and primary callouts.
- `primary-100`: `var(--color-blue-100)`
- `primary-500` (Accent): `var(--color-blue-600)`
- `primary-950`: `var(--color-blue-950)`

*(Note: Custom variable `--color-accent` switches between `primary-500` on light mode and `white` on dark mode to ensure high-contrast accessibility).*

---

## 3. Typography

High-contrast typography with tight spacing for headers.

* **Primary Font:** `"Roboto", ui-sans-serif, system-ui, sans-serif`
* **Mono Font:** `"IBM Plex Mono", ui-monospace, monospace`

### **Scale & Styling Patterns**
* `--text-tiny`: `0.625rem`
* `hero-heading`: Uses `text-5xl md:text-7xl font-bold tracking-tighter text-balance text-zinc-900 dark:text-white`.
* `tracking-tighter` & `text-balance`: Systematically used for large headings to ensure readable line lengths.

---

## 4. Effects, Layouts & Motion

* **Glassmorphism:** Central to the aesthetic. Components use `.glass-card` which provides:
  * `backdrop-blur-md`
  * Translucent white/zinc-900 backgrounds depending on the theme.
  * `duration-750 ease-in-out` transitions.
* **Form & Input Accessibility:** Flux UI data attributes (e.g., `[data-flux-field]`) are globally targeted to supply consistent gap spacing and focus rings. Inputs ring with the custom `--color-accent` outline.
* **Animation:** Custom spin animations (`animate-spin: spin 3s linear infinite`) for loading states.

---

## 5. CSS Architecture (Tailwind v4)

Tailwind v4 handles variables via `@theme` instead of a separate `tailwind.config.js`.

**Key Integration Points:**
1. `@import 'tailwindcss';` and `@import '../../vendor/livewire/flux/dist/flux.css';` form the base.
2. Views and Flux components are scanned via `@source` directives.
3. Dark mode is mapped cleanly utilizing variant injection `@custom-variant dark`.

Example definition in `app.css`:
```css
@theme {
    --font-sans: 'Roboto', ui-sans-serif, system-ui, sans-serif;
    --color-primary-500: var(--color-blue-600);
}

@layer utilities {
    .glass-card {
        @apply bg-white/70 dark:bg-zinc-900/70 backdrop-blur-md shadow-xl transition-all duration-750 ease-in-out;
    }
}
```
