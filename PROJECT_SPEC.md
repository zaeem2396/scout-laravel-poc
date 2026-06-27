You are a Senior Laravel Staff Engineer and Solutions Architect.

Your task is to build a production-grade demonstration project whose sole purpose is to showcase advanced Laravel observability using the Obeserva package (Scout APM integration).

This project is NOT a CRUD application.

It is NOT intended for production.

It is an engineering showcase that demonstrates how Obeserva captures Laravel internals and presents rich traces inside Scout APM.

Think of this project as an "Observability Test Suite" or "Observability Playground" similar to what an APM company would build internally for testing their instrumentation.

The code must follow SOLID principles, PSR-12, Laravel best practices, clean architecture, and must be fully Dockerized.

====================================================
TECH STACK
====================================================

- Laravel 13
- PHP 8.4
- MySQL 8
- Redis
- Horizon
- Mailpit
- Docker Compose
- Pest
- Scout APM
- Obeserva (local path/package)
- Laravel Telescope (optional)
- Laravel Reverb (optional)

Everything must run with:

docker compose up -d

No manual installation.

====================================================
DOCKER SERVICES
====================================================

Create containers for:

- nginx
- php-fpm
- mysql
- redis
- horizon
- scheduler
- mailpit

The scheduler must continuously execute Laravel Scheduler.

Horizon must process queues.

====================================================
PROJECT GOAL
====================================================

The application should intentionally generate every possible interesting trace that an observability platform should capture.

Every page should demonstrate a specific capability.

The Scout trace should tell a story.

====================================================
DOMAIN
====================================================

Build a realistic Multi-Vendor Marketplace.

Entities:

- Users
- Vendors
- Products
- Categories
- Orders
- Order Items
- Reviews
- Coupons
- Inventory
- Payments
- Notifications

Do NOT spend time on beautiful UI.

Bootstrap is sufficient.

The focus is backend execution.

====================================================
DATABASE
====================================================

Seed realistic data.

Approximate dataset:

500 users

50 vendors

100 categories

5000 products

50000 reviews

100000 orders

500000 order_items

Enough data to expose slow queries and N+1 problems.

====================================================
DEMO ROUTES
====================================================

Create the following demonstration routes.

----------------------------------------------------
1.

GET /demo/request

Purpose:

Simple request lifecycle.

Show:

- Middleware
- Controller
- Service
- Repository
- Database

----------------------------------------------------
2.

GET /demo/n-plus-one

Intentionally create an N+1 query.

Example:

Products

↓

Vendor

↓

Category

↓

Reviews

↓

Images

Do NOT eager load.

Scout should clearly display the duplicated SQL queries.

----------------------------------------------------
3.

GET /demo/slow-query

Execute one intentionally slow SQL query.

Simulate a bad query using:

- large joins
- sleep()
- inefficient conditions

The trace should clearly show where time is spent.

----------------------------------------------------
4.

GET /demo/slow-method

Create an intentionally slow RecommendationService.

Consume approximately 600-1000ms.

The trace must identify this method as the bottleneck.

----------------------------------------------------
5.

GET /demo/cache

Demonstrate:

Cache miss

↓

Database

↓

Redis cache

↓

Cache hit

Show the difference between first and second request.

----------------------------------------------------
6.

GET /demo/events

Dispatch:

OrderPlaced

Listeners:

- UpdateInventory
- SendInvoice
- RewardCustomer
- NotifyWarehouse

Every listener should have its own trace.

----------------------------------------------------
7.

GET /demo/jobs

Dispatch:

GenerateInvoiceJob

↓

SyncERPJob

↓

GenerateAnalyticsJob

↓

SendEmailJob

Each job should run independently.

====================================================
QUEUE CORRELATION
====================================================

Demonstrate request-to-job correlation.

HTTP Request

↓

Dispatch Job

↓

Queue Worker

↓

Job Completion

Maintain trace continuity.

====================================================
POLICIES
====================================================

Create policies for:

Order

Product

Vendor

Demonstrate:

view

update

delete

Each authorization check should appear in the trace.

====================================================
MIDDLEWARE
====================================================

Create custom middleware.

Examples:

TenantResolver

RequestLogger

FeatureFlagMiddleware

Each middleware should appear as an independent span.

====================================================
NOTIFICATIONS
====================================================

Create notification flow.

Order Paid

↓

Mail Notification

↓

Database Notification

Both should be traced.

====================================================
BROADCASTING
====================================================

Broadcast:

OrderStatusUpdated

Demonstrate broadcasting instrumentation.

====================================================
EXTERNAL APIS
====================================================

Consume multiple APIs.

Examples:

JSONPlaceholder

DummyJSON

GitHub API

Each HTTP request should become its own trace.

====================================================
DATABASE TRANSACTIONS
====================================================

Checkout must use:

DB::transaction()

The trace should show transaction duration.

====================================================
FAILED JOB
====================================================

Create one job that intentionally fails.

Retry.

Eventually succeed.

The trace should capture:

Failure

↓

Retry

↓

Success

====================================================
SCHEDULER
====================================================

Create scheduled commands.

Examples:

GenerateDailyReport

CleanupLogs

SyncAnalytics

Every scheduled command should generate traces.

====================================================
MEMORY TEST
====================================================

Create:

GET /demo/memory

Generate a large in-memory collection.

Demonstrate memory usage.

====================================================
EXCEPTION TEST
====================================================

Create:

GET /demo/exception

Throw a custom exception.

Ensure Scout captures it.

====================================================
STRESS TEST
====================================================

Create:

POST /demo/full-flow

This endpoint should execute nearly every Laravel subsystem.

Execution flow:

Authenticate

↓

Middleware

↓

Validation

↓

Controller

↓

Service

↓

Repository

↓

Policy

↓

Database Transaction

↓

Multiple SQL Queries

↓

Redis

↓

Cache

↓

External API

↓

Events

↓

Listeners

↓

Notifications

↓

Broadcast

↓

Dispatch Queue Jobs

↓

Commit Transaction

↓

HTTP Response

This endpoint should generate a deep trace tree demonstrating Obeserva's full capabilities.

====================================================
DASHBOARD
====================================================

Create:

GET /demo/dashboard

Display:

- Current Request ID
- Trace ID
- Correlation ID
- Memory Usage
- Peak Memory
- Execution Time
- Active Queue Jobs
- Cache Statistics
- Database Query Count
- Redis Operations
- Event Count
- Listener Count

====================================================
TESTING
====================================================

Use Pest.

Create tests for:

- HTTP tracing
- Middleware tracing
- Controller tracing
- Service tracing
- Repository tracing
- Query tracing
- Cache tracing
- Redis tracing
- Event tracing
- Listener tracing
- Job tracing
- Queue propagation
- Policy tracing
- Notification tracing
- Broadcast tracing
- Scheduler tracing
- Exception tracing

====================================================
ARCHITECTURE
====================================================

Follow:

- SOLID
- Repository Pattern
- Service Layer
- Form Requests
- Events
- Listeners
- Jobs
- Policies
- DTOs where appropriate
- Dependency Injection
- Constructor Property Promotion
- PHPStan compatible code
- Clean folder structure

====================================================
IMPORTANT
====================================================

This project exists to demonstrate observability.

Do NOT optimize away slow code.

Intentionally create:

- N+1 queries
- Slow methods
- Slow SQL
- Failed jobs
- Nested events
- Queue chains
- Complex request lifecycles

Every trace should clearly demonstrate why an engineer would want Obeserva installed.

Think like an engineer working for Scout APM who is building an internal showcase application for validating Laravel instrumentation.