# White Sand Resort Website (CodeIgniter 4)

Single-resort website with:

- Public home page and gallery
- Public inquiry form with database persistence and admin email notification
- Admin login with role field support
- Admin gallery management (upload, edit, delete)
- Admin inquiry management (view and status updates)
- Responsive dark luxury theme

## Tech Stack

- CodeIgniter 4
- MySQL
- Vanilla HTML/CSS + minimal JavaScript

## Theme Palette

- Main background: #0A1A2F
- Card background: #121E2E
- Accent: #1F6AE1

## Configuration

Environment settings are in [.env](.env).

Important values to update for your machine:

1. Database host, name, username, password
2. SMTP settings for inquiry notification
3. Base URL if not using http://localhost:8080/

## Database Setup

Create a database named white_sand_resort, then run:

1. php spark migrate
2. php spark db:seed AdminUserSeeder

Seeded admin account:

- Email: admin@whitesandresort.local
- Password: Admin@12345

Change this password immediately in production.

## Run Locally

1. Install dependencies:
	composer install --no-dev
2. Start server:
	 php spark serve
3. Open:
	 http://localhost:8080/

## Key Routes

- Public:
	- /
	- /gallery
	- /inquiry
- Admin:
	- /admin/login
	- /admin/dashboard
	- /admin/gallery
	- /admin/inquiries

## Project Structure

- Controllers:
	- Home
	- Gallery
	- Inquiry
	- Admin
- Models:
	- GalleryModel
	- InquiryModel
	- AdminUserModel
- Migrations:
	- admin_users
	- gallery_images
	- inquiries

## Notes

- Gallery files upload to public/uploads/gallery.
- Inquiry emails use Config\Email and .env overrides.
- Admin-protected routes use a custom filter alias: adminauth.
