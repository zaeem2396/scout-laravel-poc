<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Scout Laravel POC

Laravel 13 observability playground for demonstrating Scout APM instrumentation via Obeserva.

### Obeserva

This project installs [laravel-obeserva](https://github.com/zaeem2396/laravel-obeserva) as `scout/laravel` via a Composer VCS repository, with the Scout APM PHP agent.

1. Set your Scout key in `.env` (`SCOUT_KEY` is wired to Obeserva via `config/obeserva.php`).

2. Install dependencies and verify:

```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php artisan obeserva:status
```

The driver should report `scout` when `OBESERVA_DRIVER=scout` and `SCOUT_MONITORING_ENABLED=true`.

When `OBESERVA_DRIVER=scout`, disable Obeserva's HTTP and SQL tracing so `scout-apm-php` owns those spans (required for Scout **N+1 Insights**):

```env
OBESERVA_HTTP_MIDDLEWARE=false
OBESERVA_DB_QUERY_TRACING=false
OBESERVA_FLUSH_ON_TERMINATE=false
```

Generate N+1 demo traffic (Scout needs repeated queries over ~150ms total SQL time):

```bash
chmod +x scripts/generate-n-plus-one-traffic.sh
./scripts/generate-n-plus-one-traffic.sh http://localhost:8088 30 100
```

Then check **Web Endpoints** → `DemoController@nPlusOne` and the **N+1 Insights** tab (may take a few minutes).

| Package | Location |
|---------|----------|
| Obeserva (`scout/laravel`) | `vendor/scout/laravel` (cloned from GitHub VCS) |
| Scout agent | `vendor/scoutapp/scout-apm-php` |
| Config | `config/obeserva.php` |

### Quick start

```bash
cp .env.example .env
docker compose up -d
```

| Service | URL |
|---------|-----|
| Application | http://localhost:8088 |
| Horizon | http://localhost:8088/horizon |
| Mailpit | http://localhost:8025 |

See `PROJECT_SPEC.md` and `ROADMAP.md` for the full implementation plan.

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
