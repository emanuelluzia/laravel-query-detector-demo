# Laravel Query Detector Demo (Docker)

A small API designed to **demonstrate N+1 queries** and how to fix them using **[beyondcode/laravel-query-detector]**.  
The project includes two endpoints:
- `GET /api/posts` – intentionally **N+1** (bad on purpose)
- `GET /api/posts-optimized` – **eager loaded** (fixed)

## Quick start

```bash
# 1) Build & start
docker compose up -d --build

# 2) Install Laravel (first time only, if not present)
# docker compose exec app composer create-project laravel/laravel .

# 3) Generate app key and migrate/seed
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
```

## Endpoints

```bash
# N+1 (will trigger the detector)
curl -i http://localhost:8080/api/posts

# Optimized
curl -i http://localhost:8080/api/posts-optimized
```

## Query Detector

Install and publish (already in this repo, but for reference):
```bash
docker compose exec app composer require beyondcode/laravel-query-detector --dev
docker compose exec app php artisan vendor:publish --provider="BeyondCode\QueryDetector\QueryDetectorServiceProvider" --force
```

Suggested config for APIs (`config/querydetector.php`):
```php
return [
    'enabled'   => env('QUERY_DETECTOR_ENABLED', true),
    'threshold' => (int) env('QUERY_DETECTOR_THRESHOLD', 1),
    'except'    => [],
    'log_channel' => env('QUERY_DETECTOR_LOG_CHANNEL', 'daily'),
    'output' => [
        \BeyondCode\QueryDetector\Outputs\Json::class,
        \BeyondCode\QueryDetector\Outputs\Log::class,
    ],
];
```

### Where to see the results
- **JSON body (for `/api/posts`)** – The detector appends a block with N+1 details.  
- **Logs** – Check `storage/logs/laravel-YYYY-MM-DD.log`.

## Screenshots / Evidence

```
# RESPONSE JSON (N+1 detected)
<img width="1027" height="587" alt="image" src="https://github.com/user-attachments/assets/becbced8-8b45-4f49-8499-9f9b86929d6d" />

```

```
# LOG SAMPLE
<img width="725" height="150" alt="image" src="https://github.com/user-attachments/assets/df45f909-1dcb-4906-a051-df52fcffc457" />

```

## How the N+1 is produced

In `PostController@index`, we deliberately access relations without eager loading:
```php
$posts = Post::latest()->take(30)->get();
$data = $posts->map(function ($p) {
    return [
        'id'       => $p->id,
        'title'    => $p->title,
        'author'   => $p->user->name,        // lazy load → N+1
        'comments' => $p->comments->count(), // lazy load → N+1
    ];
});
```

The **optimized** version uses eager loading:
```php
$posts = Post::with(['user','comments'])->latest()->take(30)->get();
```

## Docker layout

- `app`: PHP-FPM 8.3 + Composer
- `web`: Nginx (root at `/var/www/src/public`)
- `db`: MySQL 8.0 (credentials in `docker-compose.yml` / `.env`)

## Notes

- This repository is intentionally minimal and focused on **learning** N+1 detection and eager loading.
- Feel free to add more relations (e.g. categories, tags) to generate different N+1 scenarios.



