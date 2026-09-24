# Enterprise Order & Inventory Management API

A Laravel-based enterprise e-commerce backend for managing products, inventory, carts, orders, payments, shipments, supplier synchronization, and background processing.

## Tech Stack

- Laravel 13
- PHP 8.5
- MySQL 8.4
- Redis 8
- Docker & Docker Compose
- Laravel Sanctum
- Laravel Horizon
- PHPUnit
- Razorpay Test Mode
- DummyJSON supplier API
- Mailpit for local email testing

## Main Features

- User authentication using Laravel Sanctum
- Role-based authorization
- Product and category management
- Inventory management
- Cart management
- Checkout
- Inventory reservation
- Order management
- Idempotent checkout
- Razorpay payment integration
- Razorpay webhook verification
- Shipment management
- DummyJSON supplier synchronization
- Background jobs and queues
- Laravel Horizon
- Scheduled jobs
- Email notifications
- Redis caching and queue support

## Project Architecture

The application separates business logic into services instead of placing all logic inside controllers.

Main areas include:

- Catalog
- Ordering
- Inventory
- Payments
- Fulfillment
- Supplier integration
- Notifications
- Jobs
- Events and listeners

Controllers are responsible mainly for handling HTTP requests and delegating business logic to services.

## Docker Setup

The project runs using Docker Compose.

Main services include:

- `app` - Laravel application
- `mysql` - MySQL database
- `redis` - Redis
- `worker` - Laravel Horizon worker
- `scheduler` - Laravel scheduler
- `shipping` - mock shipping service
- `mailpit` - local email testing

## Installation

Clone the repository and enter the project directory:

```bash
git clone <your-github-repository-url>
cd enterprise-commerce-api
```
