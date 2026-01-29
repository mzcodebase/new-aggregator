# News Aggregator API

A powerful Laravel  application that aggregates news from multiple sources including NewsAPI, The Guardian, New York Times and BBC News. This API provides endpoints to search, filter, and retrieve news articles from various sources in a unified format.

## 📋 Prerequisites

Before you begin, ensure you have the following installed on your system:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **MySQL** >= 8.0
- **Node.js** >= 18.x (for frontend assets)
- **Git**

## Installation

### 1. Clone the Repository

```
git clone https://github.com/mzcodebase/new-aggregator.git
cd new-aggregator
```

### 2. Install Dependencies

```
composer install
```

### 3. Environment Configuration

Copy the environment example file:

```
cp .env.example .env
```

### 4. Generate Application Key

```
php artisan key:generate
```

### 5. Database Setup

Configure your database connection in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_aggregator
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations

```
php artisan migrate
```

### 7. Seed the Database

```
php artisan db:seed
```

You need to create accounts and obtain API keys from the following news sources:

### Required API Accounts

| Provider | Website | Documentation |
|----------|---------|---------------|
| **NewsAPI.org** | [https://newsapi.org/](https://newsapi.org/) | [Docs](https://newsapi.org/docs) |
| **The Guardian** | [https://open-platform.theguardian.com/](https://open-platform.theguardian.com/) | [Docs](https://open-platform.theguardian.com/documentation/) |
| **New York Times** | [https://developer.nytimes.com/](https://developer.nytimes.com/) | [Docs](https://developer.nytimes.com/docs) |

### Environment Variables

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

## 🏃‍♂️ Running the Application

### Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`
### Run the Scheduler
The scheduler dispatches fetching articles job at the configured intervals(every minute):
```
php artisan schedule:work
```
### Run Queue Worker background job - API Article Fetching
```
php artisan queue:work
```
### Base URL
```
http://localhost:8000/api/v1
```

## 🔄 Data Synchronization

The application includes commands to fetch and sync data from news sources:

```
# Fetch articles from all sources
php artisan fetch:articles

# Fetch from specific source
php artisan fetch:articles --source=newsapi
php artisan fetch:articles --source=guardian
php artisan fetch:articles --source=nyt
```

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/mzcodebase/new-aggregator/issues) page
2. Create a new issue with detailed information
3. Provide error logs and environment details

---
