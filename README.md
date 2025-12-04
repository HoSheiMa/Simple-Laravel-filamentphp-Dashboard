# Acme – Team Skills Tracker (Filament v4)

A mini Laravel 11 + Filament v4 module for tracking team skills. It shows end‑to‑end use of Filament resources, wizard forms, infolists, widgets, database notifications, and a small external API integration for skill categories.

---

## 1. Requirements

-   PHP 8.2+
-   Composer
-   Node.js & npm
-   MySQL/PostgreSQL (or any DB supported by Laravel)

---

## 2. Setup

```bash
git clone https://github.com/HoSheiMa/Simple-Laravel-filamentphp-Dashboard.git acme
cd acme

cp .env.example .env
composer install
php artisan key:generate
```

Configure your database in `.env`:

```env
DB_DATABASE=acme
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate
```

Create storage link (for file uploads):

```bash
php artisan storage:link
```

---

## 3. Filament Admin Panel

Install assets and create an admin user:

```bash
npm install
npm run build

php artisan make:filament-user
# follow prompts for email/password
```

Start the app:

```bash
php artisan serve
```

Visit: `http://localhost:8000/admin` and log in with your Filament user.

---

## 4. External Categories API

Categories are synced from this public API:

`https://692f9b9b778bbf9e006def35.mockapi.io/api/Rinvex/categories/categories`

The data is stored in the `categories` table (`name`, `api_id`) and used as the source for skill categories.

Sync categories:

```bash
php artisan categories:sync
```

You can also trigger sync from the Filament **Categories** screen via a custom “Sync from API” action.

---

## 5. Skills Module Overview

**Skill model fields:**

-   `name` (unique)
-   `category_id` (FK → categories)
-   `proficiency_level` (1–5, conditional)
-   `is_active` (bool)
-   `description` (markdown)
-   `attachments` (JSON, multiple uploads)
-   `tags` (JSON repeater)
-   `notes` (text)
-   `archived` (bool)

**Filament features:**

-   **Wizard form** (create/edit):
    -   Step 1: basic info (name, category, active).
    -   Step 2: proficiency + markdown description (proficiency required only for technical‑type categories).
    -   Step 3: tags repeater + file uploads.
    -   Step 4: notes with Alpine.js character counter.
-   **List table**:
    -   Search + sorting.
    -   Filters: category, min proficiency, active/inactive.
    -   Grouping by category and proficiency.
    -   Row actions: view, edit, toggle active, archive (sets `is_active = false`, `archived = true` and logs activity).
    -   Summaries for total skills and average proficiency.
-   **View page (Infolist)**:
    -   Shows metadata, markdown description, tags, attachments, and notes in sections.

---

## 6. Notifications

Two types of notifications are used:

1. **Toast notifications** after create/update via Filament’s notification system.
2. **Database notifications** sent to the logged‑in user and visible in the Filament notification bell.

Make sure:

-   The panel is configured with `databaseNotifications()` in the panel provider.
-   `App\Models\User` uses the `Notifiable` trait.

---

## 7. Tests

The project includes focused tests for the categories API integration (and you can extend further):

Run tests:

```bash
php artisan test
```

---

## 8. What You Get

-   Clean, component‑based Filament v4 implementation.
-   Real external API integration for categories.
-   Wizard UX for create/edit skills.
-   In‑app statistics widgets for skills.
-   Database‑backed notifications integrated into the Filament UI.
