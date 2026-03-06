# Custom Block Builder CMS

A modern, high-performance Content Management System built with a custom visual block builder. This project eschews generic admin panels (like Filament) in favor of a 100% bespoke, WordPress-like administrative experience tailored for maximum flexibility and performance.

## 🚀 Tech Stack

### Backend
- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** Relational database for structured content and JSON fields (`content`, `settings`) for dynamic block configurations.
- **Routing & State:** Ziggy & Inertia.js (Server-Side Rendered approach without heavy standalone APIs).

### Frontend (Admin & Public)
- **Framework:** Vue.js 3 (Composition API `<script setup>`)
- **State Management:** Pinia (e.g., `useBlockBuilderStore`) managing complex UI states without props-drilling.
- **Styling:** Tailwind CSS v4 (Utility-first) + DaisyUI. 
- **Theming:** Dynamic theme switching (`cyberpunk`, `light`, `dark`, etc.) seamlessly applied across both the admin tools and the public-facing site.
- **Animations & Interactivity:** 
  - [GSAP](https://gsap.com/) for complex, timeline-based block animations and z-index 3D spatial visualizations.
  - `vuedraggable` for intuitive drag-and-drop block management.
  - `@phosphor-icons/vue` and FontAwesome for UI iconography.

## 🏗️ Core Architecture & Philosophy

1. **Custom Visual Editor:** 
   The heart of the application is the Block Builder. Users construct pages and posts by dragging and dropping blocks. Block layouts, padding, margins, and 3D z-index configurations are saved as structured JSON objects.
   
2. **Performance First (Inertia.js):** 
   We limit standard AJAX/API requests. Instead, data is injected directly into components via Laravel controllers and Inertia, ensuring extremely fast load times and a SPA-like feel while maintaining the benefits of a backend-driven application.

3. **DRY & Component-Based:** 
   Vue components are created with reusability in mind. Repeated UI chunks are extracted into standalone `<template>` files to maintain a clean and manageable codebase.

4. **Living Documentation:**
   Core architectural decisions and systemic contexts are documented in the `docs/` directory. **Always consult and update `docs/*.md` files when making significant architectural changes.**

## 💻 Local Development

### Requirements
- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Setup

1. **Clone the repository and install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Install frontend dependencies:**
   ```bash
   npm install
   ```

3. **Environment Setup:**
   Copy the example `.env` file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Migration:**
   Ensure your database is configured in `.env`, then run:
   ```bash
   php artisan migrate
   ```

5. **Start Dev Servers:**
   Run the Laravel queue/serve and Vite concurrently:
   ```bash
   npm run dev
   ```
   *(Alternatively, run `php artisan serve` and `npm run dev` in separate terminal windows).*

---

*This application prioritizes premium user experience with micro-animations and polished aesthetics. When contributing, ensure all UI updates comply with the cohesive DaisyUI theme structure and utility-driven Tailwind principles.*
