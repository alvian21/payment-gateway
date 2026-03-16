# Payment Gateway Dashboard

A modern payment gateway management system built with Laravel, Vue.js, and Inertia.js.

## Tech Stack

- **Backend**: Laravel 11+
- **Frontend**: Vue.js 3, Inertia.js
- **Styling**: Tailwind CSS, Shadcn Vue
- **Icons**: Lucide Vue Next
- **Database**: PostgreSQL

## Features

- **Admin Dashboard**: Real-time statistics of payment orders.
- **Payment Management**: Listing, filtering, and flagging payment orders.
- **User Settings**: Profile management, security settings, and appearance customization.
- **Robust API**: Secure endpoints for order creation and payment processing.

## Getting Started

### Prerequisites

- PHP 8.2+
- Node.js 20+
- Composer
- NPM

### Installation

1. Clone the repository:

    ```bash
    git clone <repository-url>
    cd payment-gateway
    ```

2. Install dependencies:

    ```bash
    composer install
    npm install
    ```

3. Setup environment:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Run migrations and seeders:

    ```bash
    php artisan migrate --seed
    ```

5. Run the application:
    ```bash
    npm run dev
    # In another terminal
    php artisan serve
    ```

## Admin Dashboard

The admin dashboard can be accessed at `/dashboard`.

- **Summary Stats**: View total, paid, pending, and expired transactions at a glance.
- **Payment List**: Manage all payment orders, search by customer name or reference, and filter by status.
- **Flagging**: Mark specific payments for review.

## Default Credentials

You can use the following credentials to access the Admin Dashboard after running the seeders:

| Role           | Email            | Password   |
| :------------- | :--------------- | :--------- |
| **Admin Demo** | `admin@demo.com` | `password` |

## Payment API Documentation

The API is secured with a `Sec-Token` header.

### Security Header

| Header      | Value Format | Example    |
| :---------- | :----------- | :--------- |
| `Sec-Token` | `YYYYMMDD`   | `20260316` |

### API Endpoints (All GET)

#### 1. Create Payment Order

`GET /api/order`

**Parameters:**

- `amount` (required|numeric): Base amount.
- `reff` (required|string): Unique reference ID.
- `expired` (required|date): Expiration timestamp (ISO 8601).
- `name` (required|string): Customer name.
- `hp` (required|numeric): Customer phone number.

#### 2. Process Payment

`GET /api/payment`

**Parameters:**

- `reff` (required|string): Reference ID of the order to pay.

#### 3. Check Order Status

`GET /api/status`

**Parameters:**

- `reff` (required|string): Reference ID.

---

For more detailed architectural information, see [SYSTEM_DESIGN.md](./SYSTEM_DESIGN.md).
