# Gift Vibe LK

An application featuring two distinct portals (Public customer portal and Admin portal) inside a single shared PHP application.

## Directory Structure Overview

- `app/`: Shared application layer (Core, Models, Repositories, Services, Helpers, etc.).
- `routes/`: Portal-specific routing files (`web.php` for Public, `admin.php` for Admin).
- `resources/`: Shared and portal-specific views, JS, and CSS files.
- `public/`: Public portal entry point (`index.php`) and assets.
- `admin/`: Admin portal entry point (`index.php`) and assets.
- `storage/`: Dynamic file storage (logs, cache, uploads, sessions).
- `database/`: Database migrations, seeds, and schema configuration.

## Setup Instructions

1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Build Tailwind CSS:
   ```bash
   npm run build
   ```
3. Set up environment variables:
   - Copy `.env.example` to `.env`
   - Adjust database credentials
