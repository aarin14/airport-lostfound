# Airport Lost & Found Desk System

A simple web application for managing lost & found items at the airport desk.

## Features
- Desk operator login system
- Report lost or found items (with image upload)
- Search and filter items
- Simple data management
- Data stored in `data/items.json`

## Setup
1. **Requirements:** PHP 7+, file write permissions for `data/` and `uploads/` folders.
2. **Directory Structure:**
   - `index.php` — Main dashboard
   - `report-lost.php` — Lost item form
   - `report-found.php` — Found item form
   - `search.php` — Search interface
   - `backend/` — PHP backend scripts
   - `data/items.json` — Data storage
   - `uploads/` — Uploaded images
   - `assets/` — CSS, JS, images
3. **Permissions:**
   - Ensure `data/` and `uploads/` are writable by the web server.

## Usage
- Desk operators log in to manage all lost and found items
- Report new lost or found items
- Search existing items to help reunite with owners
- Simple interface designed for single desk operation

## Login Credentials
- Username: `desk_operator`
- Password: `password123` (default - change in production)

## Customization
- Update branding in `index.php` and add your airport logo to `assets/img/`.
- Extend backend logic as needed (e.g., MySQL, email notifications).

## Security
- This is a demo app. For production, add input validation, authentication, and secure file handling. 