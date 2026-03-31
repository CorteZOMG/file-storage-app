# File Storage App

A Laravel 13 file storage application running in Docker.

## Tech Stack

| Service | Image / Version |
|---------|----------------|
| PHP-FPM | `php:8.4-fpm` |
| Web Server | `nginx:1.27-alpine` |
| Database | `mysql:8.4` |
| Framework | Laravel 13 |

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- Git

## Getting Started

### 1. Clone the repository

```bash
git clone <repository-url>
cd file-storage-app
```

### 2. Configure environment

```bash
cp .env.example .env
```

Edit `.env` if you need to change any default values. Key variables:

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_PORT` | `8080` | Port the app is accessible on |
| `DB_DATABASE` | `file_storage` | MySQL database name |
| `DB_USERNAME` | `file_storage_user` | MySQL user |
| `DB_PASSWORD` | `secret` | MySQL user password |
| `DB_ROOT_PASSWORD` | `root_secret` | MySQL root password |
| `DB_EXTERNAL_PORT` | `3306` | Host port for MySQL access |

### 3. Build and start containers

```bash
docker-compose up -d --build
```

This starts three containers:

- **file-storage-app** — PHP 8.4 FPM processing Laravel
- **file-storage-nginx** — Nginx reverse proxy on port `8080`
- **file-storage-db** — MySQL 8.4 with a health check

### 4. Install dependencies

```bash
docker-compose exec app composer install
```

### 5. Set up Laravel

```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

### 6. Open the app

Visit [http://localhost:8080](http://localhost:8080)

## Useful Commands

```bash
# Stop all containers
docker-compose down

# View logs
docker-compose logs -f

# View logs for a specific service
docker-compose logs -f app

# Rebuild after Dockerfile changes
docker-compose up -d --build

# Run Artisan commands
docker-compose exec app php artisan <command>

# Open a shell inside the app container
docker-compose exec app bash

# Connect to MySQL
docker-compose exec db mysql -u file_storage_user -p file_storage

# Clear all data (removes DB volume)
docker-compose down -v
```