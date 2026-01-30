# Full-Stack News Aggregator Challenge

A powerful Laravel  application that aggregates news from multiple sources including NewsAPI, The Guardian, New York Times and BBC News. This API provides endpoints to search, filter, and retrieve news articles from various sources in a unified format.

## Prerequisites

- **Docker Desktop** ([download](https://www.docker.com/products/docker-desktop/))

---

## Docker Setup (Laravel Sail)

The project runs fully in Docker: Laravel app, MySQL, Redis, Vite dev server, and queue worker all start with one command.

### 1. Clone and install PHP dependencies

```bash
git clone https://github.com/mzcodebase/new-aggregator.git
cd new-aggregator
composer install
```

### 2. Environment file

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set these for Sail:

| Variable        | Value (for Sail)                          |
|----------------|-------------------------------------------|
| `APP_URL`      | `http://localhost` or `http://localhost:8080` |
| `APP_PORT`     | `80` (or `8080` if port 80 is in use)    |
| `DB_CONNECTION`| `mysql`                                  |
| `DB_HOST`      | `mysql`                                  |
| `DB_PORT`      | `3306`                                   |
| `DB_DATABASE`  | `laravel` or `news_aggregator`            |
| `DB_USERNAME`  | `sail`                                   |
| `DB_PASSWORD`  | `password`                               |
| `REDIS_HOST`   | `redis`                                  |

### 3. Start all services

```bash
./vendor/bin/sail up -d
```

This starts:

- **laravel.test** – Laravel app (PHP)
- **vite** – Frontend dev server (Vue/Vite); no need to run `npm run dev` manually
- **queue** – Queue worker for background jobs (e.g. fetching articles)
- **mysql** – MySQL 8.4
- **redis** – Redis

The first run may take a few minutes (build + pull). Subsequent runs are quick.

### 4. First-time setup

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run build
```

(Optional: run `./vendor/bin/sail npm install` if the vite service hasn’t installed deps yet.)

### 5. Access the application

- **Web UI:** http://localhost (or http://localhost:8080 if `APP_PORT=8080`)
- **API base:** http://localhost/api/v1 (or http://localhost:8080/api/v1)

The frontend is served by the **vite** container (hot reload). The **queue** container runs jobs in the background.

### 6. Custom port (e.g. when port 80 is in use)

In `.env`:

```env
APP_URL=http://localhost:8080
APP_PORT=8080
```

Then:

```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### 7. Sail command reference

| Task              | Command                              |
|-------------------|--------------------------------------|
| Start containers  | `./vendor/bin/sail up -d`            |
| Stop containers   | `./vendor/bin/sail down`             |
| Run Artisan       | `./vendor/bin/sail artisan <cmd>`    |
| Run Composer      | `./vendor/bin/sail composer <cmd>`   |
| Run NPM           | `./vendor/bin/sail npm <cmd>`        |
| View logs         | `./vendor/bin/sail logs -f`          |
| Shell into app    | `./vendor/bin/sail shell`            |

---

## Required API accounts (news sources)

Create accounts and obtain API keys from the following sources:

| Provider       | Website                                               | Documentation |
|----------------|--------------------------------------------------------|---------------|
| **NewsAPI.org**| [newsapi.org](https://newsapi.org/)                   | [Docs](https://newsapi.org/docs) |
| **The Guardian** | [open-platform.theguardian.com](https://open-platform.theguardian.com/) | [Docs](https://open-platform.theguardian.com/documentation/) |
| **New York Times** | [developer.nytimes.com](https://developer.nytimes.com/) | [Docs](https://developer.nytimes.com/docs) |

### Environment variables (news APIs)

Add to `.env`:

```env
NEWSAPI_ENABLED=true
NEWSAPI_KEY=your_news_api_key_here
NEWS_API_URL=https://newsapi.org/v2

GUARDIAN_ENABLED=true
GUARDIAN_KEY=your_guardian_api_key_here
GUARDIAN_BASE_URL=https://content.guardianapis.com

NYT_ENABLED=true
NYT_KEY=your_NYT_api_key_here
NYT_SECRET=your_NYT_api_secret_here
NYT_API_URL=https://api.nytimes.com/svc

BBC_ENABLED=true
BBC_API_URL=https://bbc-news-api.vercel.app
BBC_LANGUAGE=english
```

---

## Running the application

After `./vendor/bin/sail up -d`:

- **Web:** http://localhost (or http://localhost:8080)  
- **API:** http://localhost/api/v1 (or http://localhost:8080/api/v1)  

The **vite** and **queue** services run automatically; no need to run `npm run dev` or `queue:work` manually.

---

## Data synchronization

Fetch and sync articles from news sources:

```bash
# All sources
./vendor/bin/sail artisan fetch:articles

# Specific source
./vendor/bin/sail artisan fetch:articles --source=newsapi
./vendor/bin/sail artisan fetch:articles --source=guardian
./vendor/bin/sail artisan fetch:articles --source=nyt
```

---

## Support

If you encounter issues:

1. Check the [Issues](https://github.com/mzcodebase/new-aggregator/issues) page
2. Create a new issue with details
3. Include error logs and environment (OS, Docker version, etc.)
