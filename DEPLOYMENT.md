# GiftVibe LK deployment

This project is designed for a copy, configure, import workflow. It does not require Composer, Node.js, or a frontend build command.

## Server requirements

- PHP 8.2 or newer with PDO MySQL, mbstring, fileinfo and GD enabled
- MySQL 8+ or MariaDB 10.4+
- Apache with `mod_rewrite` and `mod_headers`
- HTTPS enabled for `giftvibelk.lk`

## Deploy

1. Copy the complete project to the hosting account.
2. Point the domain document root to the project's `public` directory.
3. Create an empty production database.
4. Import the root `schema.sql` file once. It contains all tables and starter homepage/footer content.
5. Edit the database values in `config.php`, or configure `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, and `APP_URL` through the hosting panel.
6. Ensure `storage/sessions`, `public/assets/uploads`, and their child folders are writable by PHP.
7. Open `/`, `/admin`, `/sitemap.php`, `/robots.txt`, and one product page to verify the deployment.

## Production values

- Application URL: `https://giftvibelk.lk`
- Dynamic sitemap: `https://giftvibelk.lk/sitemap.php`
- AI catalog: `https://giftvibelk.lk/catalog.php`
- AI guidance: `https://giftvibelk.lk/llms.txt`

Submit `https://giftvibelk.lk/sitemap.php` in Google Search Console after DNS and HTTPS are active.

## Security

- Keep the domain document root on `public`; do not expose the project root.
- Replace the sample database credentials in `config.php` before launch.
- Use a strong database password and restrict the database user to this database.
- Remove sample products/testimonials from Admin if they should not be public.
