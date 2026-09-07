# Elite Estates - Luxury Real Estate Backend & Database

A production-grade, enterprise architecture backend and relational MySQL database engineered for **Elite Estates**, an ultra-luxury real estate platform.

---

## 🌟 Key Architecture Features

- **Relational MySQL Database**:
  - `properties`: Luxury property listings with full specifications, JSON amenities, coordinates/location, pricing, and statuses.
  - `property_images`: High-resolution gallery images with display ordering.
  - `inquiries`: Client viewing requests and high-value lead capture tied directly to properties.
  - `subscribers`: VIP newsletter subscribers with deduplication and export capabilities.
  - `admins`: Role-based administrator authentication with `password_hash` (BCRYPT).
- **Clean OOP PHP Architecture**:
  - **Singleton PDO Database Connection** (`config/database.php`): Secure prepared statements, UTF-8 (`utf8mb4`), no emulated prepares, strict exception handling.
  - **Domain Models** (`src/Models/`): `Property`, `Inquiry`, `Subscriber`, `Admin`.
  - **Validation & Sanitization** (`src/Helpers/Validator.php`): XSS protection, email/phone format validation, payload parsing.
  - **Standardized JSON API** (`src/Helpers/Response.php`): Structured API output with HTTP status codes and timestamps.
- **RESTful API Layer** (`api/`):
  - `GET /api/properties.php`: Multi-criteria filtering (location, property type, price tier, freeform search).
  - `GET /api/property.php?id={id}`: Single property retrieval with gallery and specs for dynamic modal.
  - `POST /api/inquiry.php`: Async lead submission with validation and status tracking.
  - `POST /api/newsletter.php`: VIP newsletter subscription endpoint.
- **Dynamic Public Frontend** (`index.php`):
  - Enhanced version of your luxury frontend design.
  - Pre-hydrated with Server-Side Rendering (SSR) fallback and reactive Alpine.js real-time filtering without page reloads.
  - Dynamic property detail modal with live specifications, gallery, and one-click "Book Private Viewing" auto-fill.
  - Toast notification system for instant user feedback.
- **Executive Admin Portal** (`admin/`):
  - Protected session authentication (`/admin/login.php`).
  - Executive Overview Dashboard (`/admin/index.php`) with KPI cards (Active Listings, Total Portfolio Valuation, Inquiries, Subscribers).
  - Property Inventory Management (`/admin/properties.php` and `/admin/property-form.php`): Create, edit, toggle status (active, pending, sold, draft), or delete properties.
  - Inquiries & Private Viewing Leads (`/admin/inquiries.php`): View prospective buyer requests, change status (`new`, `in_review`, `contacted`, `closed`), and quick-dial/email clients.
  - Subscriber Management (`/admin/subscribers.php`): View VIP leads with **1-click CSV Export**.

---

## 📁 Directory Structure

```
elite_estates/
├── config/
│   ├── database.php          # PDO Singleton connection & environment config
│   └── config.php            # App-wide global settings
├── database/
│   ├── schema.sql            # Normalized MySQL DDL tables & indexes
│   ├── seed.sql              # Luxury properties, gallery photos, admin user
│   └── migrate.php           # Automated CLI & Web database migration runner
├── src/
│   ├── Helpers/
│   │   ├── Response.php      # Standardized JSON response helper
│   │   └── Validator.php     # Input sanitization and validation helper
│   └── Models/
│       ├── Property.php      # Property queries, filtering, and CRUD
│       ├── Inquiry.php       # Lead capture and status management
│       ├── Subscriber.php    # Newsletter handling
│       └── Admin.php         # Admin authentication and dashboard KPIs
├── api/
│   ├── properties.php        # GET: Filtered properties API
│   ├── property.php          # GET: Single property details API
│   ├── inquiry.php           # POST: Inquiry submission API
│   └── newsletter.php        # POST: Newsletter subscription API
├── admin/
│   ├── login.php             # Staff authentication
│   ├── logout.php            # Session termination
│   ├── header.php            # Common admin layout header & auth guard
│   ├── footer.php            # Common admin layout footer
│   ├── index.php             # Executive dashboard metrics
│   ├── properties.php        # Property inventory manager
│   ├── property-form.php     # Create & edit luxury listings
│   ├── inquiries.php         # Client viewing requests and leads
│   └── subscribers.php       # Newsletter list & CSV export
├── index.php                 # Dynamic public luxury real estate platform
└── README.md                 # Technical documentation
```

---

## 🚀 Getting Started

### 1. Database Configuration

Open `config/database.php` and verify or update your MySQL connection credentials:

```php
private static string $host = '127.0.0.1';
private static int $port = 3306;
private static string $dbName = 'elite_estates';
private static string $username = 'root';
private static string $password = '';
```
*(Default settings match standard local MySQL / XAMPP / WAMP / Laragon setups).*

### 2. Run Database Migration & Seeder

You can run the migration in **one step** via CLI or your browser:

#### Option A: Via Command Line (Recommended)
```bash
cd C:\Users\Afzal\.gemini\antigravity\scratch\elite_estates
php database/migrate.php
```

#### Option B: Via Browser
Once your PHP server is running, navigate to:
```
http://localhost:8000/database/migrate.php
```
The migration script will automatically create the `elite_estates` database, build all relational tables, configure foreign keys and indexes, and seed luxury listings with a default administrator account.

---

### 3. Start the Local Server

Run the PHP built-in web server:

```bash
cd C:\Users\Afzal\.gemini\antigravity\scratch\elite_estates
php -S localhost:8000
```

- **Public Luxury Website**: Open [http://localhost:8000](http://localhost:8000)
- **Executive Admin Portal**: Open [http://localhost:8000/admin/login.php](http://localhost:8000/admin/login.php)

---

## 🔑 Default Admin Credentials

- **Email / Username**: `admin@eliteestates.com` or `admin`
- **Password**: `Admin@12345`

---

## 📡 REST API Documentation

### 1. Filter Properties
- **URL**: `/api/properties.php`
- **Method**: `GET`
- **Query Parameters**:
  - `location`: e.g. `Beverly Hills, CA`, `Miami, FL`, `Malibu, California`, `Aspen, Colorado`
  - `property_type`: e.g. `Modern Villa`, `Penthouse`, `Townhouse`
  - `price_range`: e.g. `$1M - $5M`, `$5M - $10M`, `$10M+`
  - `search`: Freeform text search in title, description, and location
  - `limit`: Number of records (default: 50)
- **Response**: JSON object containing matching property arrays, pricing, and specs.

### 2. Property Details (Modal / Detail View)
- **URL**: `/api/property.php?id={id}`
- **Method**: `GET`
- **Response**: Full property record including gallery images array, amenities list, lot size, and year built.

### 3. Submit Client Inquiry / Viewing Request
- **URL**: `/api/inquiry.php`
- **Method**: `POST`
- **Payload** (JSON or Form Data):
  ```json
  {
    "property_id": 1,
    "full_name": "Lord Harrison Sterling",
    "email": "sterling@investmentcapital.uk",
    "phone": "+44 20 7946 0912",
    "message": "Requesting a confidential private viewing."
  }
  ```

### 4. Newsletter Subscription
- **URL**: `/api/newsletter.php`
- **Method**: `POST`
- **Payload**:
  ```json
  {
    "email": "investor@familyoffice.com"
  }
  ```
