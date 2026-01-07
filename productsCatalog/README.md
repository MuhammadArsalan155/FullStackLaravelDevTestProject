# Laravel Product Management System

A modern product catalog management system built with Laravel 12. This project includes an admin panel powered by Filament and a dynamic frontend using Livewire and AlpineJS.

## What's Inside

This is a full-stack Laravel application that shows off some really useful patterns for modern web development. Here's what you get:

- **Web Crawler** - A PHP script that pulls product data from external websites
- **RESTful API** - Backend API with queue-based job processing
- **Admin Dashboard** - Clean, professional interface built with Filament
- **Interactive Frontend** - Reactive product catalog that updates without page refreshes
- **Solid Database Design** - Proper relationships and migrations

## Features

### Admin Panel
- Complete product management (create, read, update, delete)
- Handle multiple images per product
- Filter products by category, date, or whether they have images
- Search and sort through your catalog
- Delete multiple products at once
- Auto-refresh to see changes immediately
- Works great on any device

### Public Catalog
- Shows 25 products per page
- Search products in real-time as you type
- Filter by category with a simple dropdown
- Sort by date, title, or price (ascending or descending)
- Expand/collapse product descriptions
- Fallback images when product images are missing
- Loading states so users know what's happening
- Fully responsive layout
- Shareable URLs with filters included

### Under the Hood
- REST API for importing products
- Background job processing with Laravel queues
- Database transactions to keep your data consistent
- Comprehensive error handling and logging
- Input validation
- Clean Eloquent relationships

### The Crawler
- Simple PHP scraper
- Exports data as JSON
- Grabs title, price, image, description, and category

## Technology Stack

**Backend:**
- PHP 8.2 or higher
- Laravel 12
- MySQL 8.0 or higher
- Eloquent ORM

**Admin Panel:**
- Filament 3.x
- Livewire 3.x

**Frontend:**
- Livewire 3.x for dynamic interactions
- AlpineJS 3.x for lightweight JavaScript
- TailwindCSS 3.x for styling

**Development Tools:**
- Composer for PHP packages
- NPM for JavaScript packages
- Vite for building frontend assets
- Laravel Queue for background jobs

## What You'll Need

- PHP 8.2 or newer
- Composer 2.5+
- Node.js 18.x or higher
- NPM 9.x or higher
- MySQL 8.0+ (or MariaDB 10.3+)
- Apache or Nginx web server

**Required PHP Extensions:**
OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, and either GD or Imagick

## Getting Started

### 1. Clone and Install

```bash
git clone https://github.com/yourusername/productsCatalog.git
cd productsCatalog
composer install
npm install
```

### 2. Set Up Your Environment

```bash
cp .env.example .env
php artisan key:generate
```

Now open `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=products_catalog
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Create the Database

Using MySQL command line:
```bash
mysql -u root -p
CREATE DATABASE products_catalog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Or just create it through phpMyAdmin if that's easier for you.

### 4. Run Migrations

```bash
php artisan migrate
php artisan queue:table
php artisan migrate
```

### 5. Create Your Admin Account

```bash
php artisan make:filament-user
```

You'll be asked for:
- Name (e.g., Admin)
- Email (e.g., admin@example.com)
- Password (choose something secure)

### 6. Build the Frontend

For production:
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

### 7. Start Everything Up

**If you're using PHP's built-in server:**

Open three terminal windows:

```bash
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - Queue Worker
php artisan queue:work

# Terminal 3 - Vite (only for development)
npm run dev
```

**If you're using XAMPP or WAMP:**

1. Move the project to your `htdocs` or `www` folder
2. Access it at `http://localhost/productsCatalog/public`
3. Run the queue worker separately:
```bash
php artisan queue:work
```

### 8. Access Your Application

- **Product Catalog**: http://localhost:8000/view/products
- **Admin Panel**: http://localhost:8000/admin
- **Import API**: http://localhost:8000/api/import

## How to Use It

### Running the Crawler

```bash
cd crawler
php run.php
```

This scrapes products from the demo site and saves them to `crawler/products.json`.

### Importing Products

**With Postman:**
1. Create a POST request to `http://localhost:8000/api/import`
2. Hit send
3. Watch your queue worker terminal to see the import happen

**With cURL:**
```bash
curl -X POST http://localhost:8000/api/import
```

You'll get a response like:
```json
{
    "success": true,
    "message": "Import started successfully",
    "records": 50
}
```

### Managing Products in the Admin Panel

1. Go to http://localhost:8000/admin and log in
2. Click "Products" in the sidebar

From here you can:
- Browse all products with pagination
- Search by title or category
- Filter by category, date range, or image status
- Sort by clicking column headers
- Create new products
- Edit or delete existing ones
- Select multiple products and delete them all at once

### Browsing the Catalog

Just visit http://localhost:8000/ to see the public product catalog.

You can:
- Search for products (updates as you type)
- Filter by category
- Sort by date, title, or price
- Switch between ascending and descending order
- Click "Read More" to expand long descriptions
- Navigate through pages (25 products per page)

Everything works smoothly on mobile too.

## API Reference

### Import Products Endpoint

**URL:** `POST /api/import`

Kicks off an asynchronous import of products from the JSON file.

**Success Response:**
```json
{
    "success": true,
    "message": "Import started successfully",
    "records": 50
}
```

**Error Responses:**

If the file doesn't exist:
```json
{
    "success": false,
    "message": "JSON file not found",
    "path": "/path/to/products.json"
}
```

If the JSON is invalid:
```json
{
    "success": false,
    "message": "Invalid JSON structure - expected an array"
}
```

If something goes wrong:
```json
{
    "success": false,
    "message": "Import failed: error details"
}
```

## Testing Checklist

Here's what you should test manually:

**Crawler:**
- [ ] Run it and check the JSON file is created properly
- [ ] Verify all fields are present (title, price, image, description, category)

**Import Process:**
- [ ] Call the API endpoint
- [ ] Confirm the queue job runs
- [ ] Check that products appear in the database
- [ ] Make sure images are linked correctly

**Admin Panel:**
- [ ] Log in successfully
- [ ] View and paginate through products
- [ ] Search functionality
- [ ] All filters work (category, date, image status)
- [ ] Sorting works on each column
- [ ] Create a new product
- [ ] Edit an existing product
- [ ] Delete a product (with confirmation)
- [ ] Bulk delete multiple products

**Frontend:**
- [ ] Products display correctly (25 per page)
- [ ] Real-time search works
- [ ] Category filtering
- [ ] All sort options work properly
- [ ] Pagination navigation
- [ ] Read more/less buttons
- [ ] Image fallbacks for missing images
- [ ] Responsive design on different screen sizes

## Deploying to Production

When you're ready to go live:

1. **Update your environment settings:**
```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=database
```

2. **Optimize everything:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

3. **Security checklist:**
- Generate a new `APP_KEY`
- Use strong database passwords
- Enable HTTPS on your server
- Set proper file permissions (755 for directories, 644 for files)
- Make sure `.env` isn't publicly accessible

## Resources

If you want to learn more about the tools used in this project:

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [AlpineJS Documentation](https://alpinejs.dev)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

Built with Laravel and a lot of coffee ☕