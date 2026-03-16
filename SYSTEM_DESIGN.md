# System Design - Payment Gateway Dashboard

This document outlines the application flow and architecture of the Payment Gateway Dashboard.

## Application Flow

The following diagram illustrates the primary user flow within the application, from authentication to managing payment orders.

```mermaid
graph TD
    Start([Start]) --> Login{Authenticated?}
    Login -- No --> Register[Register / Login Page]
    Register --> Login
    Login -- Yes --> Dashboard[Dashboard Summary]

    subgraph "Main Navigation"
        Dashboard --> Stats[View Statistics]
        Dashboard --> Payments[Payments Management]
        Dashboard --> Settings[User Settings]
    end

    subgraph "Payments Management"
        Payments --> List[List Payment Orders]
        List --> Filter[Filter by Status/Search]
        List --> Sort[Sort by Date/Amount]
        List --> Flag[Toggle Flag/Mark for Review]
    end

    subgraph "User Settings"
        Settings --> Profile[Update Profile]
        Settings --> Security[Change Password]
        Settings --> Appearance[Theme Selection]
    end

    Flag --> List
    Profile --> Settings
```

## Architecture Overview

The application is built using a modern stack combining Laravel for the backend and Vue.js for the frontend, synchronized via Inertia.js.

```mermaid
graph LR
    subgraph "Client (Vue.js)"
        UI[Components/Pages]
        Route[Inertia Routes]
    end

    subgraph "Bridge (Inertia.js)"
        JSON[XHR/JSON Data]
    end

    subgraph "Server (Laravel)"
        Controller[PaymentDashboardController]
        Model[Eloquent Models: PaymentOrder, User]
        DB[(Database)]
    end

    UI <--> JSON
    JSON <--> Controller
    Controller <--> Model
    Model <--> DB
```

## Payment API Flow

The Payment API handles order creation, payment processing, and status inquiries through a secure endpoint.

### API Sequence Flow

```mermaid
sequenceDiagram
    participant Client
    participant Middleware as VerifySecToken
    participant API as PaymentController
    participant DB as Database
    participant Event as Event System

    Note over Client,API: Order Creation (GET /order)
    Client->>Middleware: GET /api/order {amount, reff, name, ...}
    Middleware->>API: Valid Token
    API->>API: Validate Data & Unique Reff
    API->>DB: Save PaymentOrder (status: pending)
    API-->>Client: 200 OK {code, total_amount, ...}

    Note over Client,API: Payment Process (GET /payment)
    Client->>Middleware: GET /api/payment {reff}
    Middleware->>API: Valid Token
    API->>DB: Find Order
    API->>API: Check Expiration & Status
    API->>DB: Update Order (status: paid)
    API->>DB: Create PaymentTransaction
    API->>Event: Fire PaymentTransactionCreated
    API-->>Client: 200 OK {status: paid, ...}

    Note over Client,API: Status Check (GET /status)
    Client->>Middleware: GET /api/status?reff=...
    Middleware->>API: Valid Token
    API->>DB: Fetch Order Data
    API-->>Client: 200 OK {status, amount, ...}
```

## Database Entities

- **User**: System administrator or operator.
- **PaymentOrder**: Stores the details of payment requests (Amount, Status, Customer, Reference).
- **PaymentTransaction**: Detailed logs of actual transactions related to orders.
