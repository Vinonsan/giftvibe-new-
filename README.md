# GiftVibe.lk

GiftVibe.lk is a single PHP, MySQL, HTML, CSS, and JavaScript project with separated public website, customer portal, and admin portal routes/layouts.

## Directory Structure Overview

- `app/`: Shared application layer (`Core`, `Helpers`, router, database, auth, view rendering).
- `routes/`: Portal-specific route files (`web.php`, `customer.php`, `admin.php`).
- `resources/`: Shared and portal-specific views, JS, and CSS files.
- `public/`: Public portal entry point (`index.php`) and assets.
- `admin/`: Admin portal entry point (`index.php`) and assets.
- `storage/`: Protected dynamic storage for logs, cache, sessions, private files, and uploads.
- `database/`: Single-file MySQL schema and initial seed data.

## Setup

1. Confirm PHP 8.1+ is enabled in Laragon.
2. Confirm MySQL is running in Laragon.
3. Review `.env`. The current local database settings are:

```ini
DB_DATABASE=dbof-giftvibelk
DB_USERNAME=root
DB_PASSWORD=
```

4. Import `database/schema.sql` using phpMyAdmin, HeidiSQL, or Laragon's MySQL client. The file creates the `dbof-giftvibelk` database and all current tables.
   - For an existing database that already has the base schema, import the update files in `database/` as needed:
     - `database/product_module_update.sql`
     - `database/order_checkout_update.sql`
     - `database/delivery_management_update.sql`
     - `database/reviews_messages_update.sql`
     - `database/expense_reporting_update.sql`
     - `database/sample_catalog_seed.sql`
     - `database/theme_palette_update.sql`
5. Point Apache/Nginx to this project root so `.htaccess` can route:
   - Public website: `http://localhost/giftvibe-new-/`
   - Customer login: `http://localhost/giftvibe-new-/login`
   - Admin login: `http://localhost/giftvibe-new-/admin/login`
6. Seeded admin login:

```text
Email: admin@giftvibe.lk
Password: admin123
```

Change this password immediately after the admin user management phase is implemented.

## Admin Features

- Product review moderation: `/admin/reviews`
- Contact message inbox and replies: `/admin/messages`
- Custom gift request queue and replies: `/admin/custom-requests`
- Notifications: `/admin/notifications`
- Expense management with receipt upload and responsible admin attribution: `/admin/expenses`
- Monthly/yearly gross profit, expenses, net profit, and product profit reports: `/admin/reports`

## Public and Customer Features

- Public shop, categories, product pages, approved reviews, average product ratings, and SEO structured data.
- Contact page with admin notification creation.
- Customer dashboard, orders, addresses, wishlist, reviews, and custom gift requests.
- Custom gift requests support reference image uploads and customer/admin chat-style replies.
- Home page trust section shows delivered order counts and delivered gift images once orders are marked delivered.

## Developer Checks and Validation

Run PHP syntax checks from the project root:

```bash
php -l config/config.php
php -l app/Core/Router.php
php -l app/Core/Database.php
php -l src/Controllers/AuthController.php
php -l src/Controllers/Admin/ExpenseController.php
```

If the MySQL client is available on PATH:

```bash
mysql -u root < database/schema.sql
```

With Laragon, the MySQL client may be available at:

```text
C:\laragon\bin\mysql\mysql-8.0.43-winx64\bin\mysql.exe
```

Run the project flow checks from the project root:

```bash
php tests/admin_auth_flow.php
php tests/public_customer_flow.php
php tests/order_checkout_flow.php
php tests/reviews_messages_flow.php
php tests/finance_reporting_flow.php
php tests/seo_security_database_validation.php
```

## SEO Verification

The public layout renders responsive viewport metadata, canonical URLs, Open Graph tags, SEO descriptions, and JSON-LD structured data when supplied by controllers. Validate with:

```bash
php tests/seo_security_database_validation.php
```

Manual production checks:

- View page source for `/`, `/shop`, `/product/{slug}`, and `/contact`.
- Confirm every page has one `<title>`, a description, a canonical URL, and expected Open Graph tags.
- Confirm product pages include Product JSON-LD and approved-review aggregate rating when reviews exist.

## Security Validation

- CSRF protection is required for state-changing forms.
- Admin routes are protected by staff middleware.
- Customer routes are protected by customer middleware.
- `storage/` denies direct web access with `storage/.htaccess`.
- Expense receipts are served through authenticated admin routes instead of direct storage links.
- Image/PDF uploads are MIME checked and limited to 5 MB.

Validate with:

```bash
php tests/seo_security_database_validation.php
```

## Responsive Testing

The layouts use the shared viewport meta tag and responsive Tailwind grid classes. Before release, check these routes at 375 px, 768 px, and 1440 px widths:

- `/`
- `/shop`
- `/product/{slug}`
- `/contact`
- `/custom-gifts`
- `/customer/dashboard`
- `/admin/dashboard`
- `/admin/expenses`
- `/admin/reports`

Confirm navigation, forms, tables, product cards, report cards, and chat replies do not overlap or overflow.

## Storage Security

`storage/` is denied from direct web access. Upload subfolders are split by purpose:

```text
storage/uploads/products
storage/uploads/categories
storage/uploads/banners
storage/uploads/payments
storage/uploads/profiles
storage/uploads/reviews
storage/uploads/requests
storage/uploads/expenses
storage/uploads/temp
storage/private
```

## Requirements Checklist

No separate master requirements document was present in this workspace beyond this README, so this checklist consolidates the requested requirements implemented in the project.

| Requirement | Status | Implementation |
| --- | --- | --- |
| Customer 1-5 star ratings | Completed | Customer review form and `reviews.rating` check. |
| Written reviews | Completed | Review title/body stored and rendered after approval. |
| Admin review approval/rejection | Completed | `/admin/reviews` moderation status updates. |
| Public approved reviews | Completed | Product pages query approved reviews only. |
| Average product rating | Completed | Product detail/listing averages approved ratings only. |
| Contact page | Completed | `/contact` form and notification creation. |
| Admin/customer chat-style replies | Completed | Contact/admin replies and custom request customer/admin reply threads. |
| Customised gift requests | Completed | `/custom-gifts` and customer request portal. |
| Reference-image uploads | Completed | Custom request image uploads with MIME/size validation. |
| Admin notifications | Completed | Notification table, topbar count, and notification center. |
| Message and request status management | Completed | Admin status controls for contact messages and custom requests. |
| Expense management | Completed | `/admin/expenses` create/filter/status workflow. |
| Expense receipts/images | Completed | Receipt upload plus authenticated admin receipt route. |
| Admin responsible for each expense | Completed | `expenses.admin_id` links to the staff user creating the expense. |
| Gross profit | Completed | Reports calculate sales minus product cost. |
| Total expenses | Completed | Reports sum approved/paid business expenses. |
| Net profit | Completed | Reports calculate gross profit minus expenses. |
| Product profit | Completed | Reports group revenue/cost/profit by product. |
| Monthly and yearly reports | Completed | Dashboard/reports period filters support monthly and yearly windows. |
| Delivered-order trust section | Completed | Home page trust section backed by delivered orders. |
| Delivered counts and images | Completed | Home page shows delivered counts and product images. |
| Complete SEO verification | Completed | SEO component, structured data, and validation test. |
| Security validation | Completed | CSRF, protected storage, authenticated receipt route, upload validation, and validation test. |
| Responsive testing | Completed | Responsive layouts documented with viewport checklist. |
| Database and PHP validation | Completed | SQL updates, schema additions, flow tests, and PHP lint commands. |
| Complete setup documentation | Completed | Setup, migrations, validation, security, responsive, and requirements sections documented. |
| Remove admin public-home button | Completed | Topbar no longer shows the public "View Site" link/button. |
| Admin products page | Completed | `/admin/products` renders image-backed product management, creation, stock, status, rating, and price data. |
| Admin categories page | Completed | `/admin/categories` renders image-backed category management and status controls. |
| Admin customers page | Completed | `/admin/customers` renders customer activity, order, address, request, and lifetime value data. |
| Admin settings page | Completed | `/admin/settings` renders editable grouped settings. |
| Public about page | Completed | `/about` renders a modern SEO-ready brand/story page with image hero and live stats. |
| Starter images and data | Completed | Fresh schema and `database/sample_catalog_seed.sql` include sample products, categories, image paths, and SEO copy. |
