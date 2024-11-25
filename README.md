# News API Documentation

## Setup

### Prerequisites

- Docker
- Docker Compose

---

### Docker Setup

1. **Clone the Repository**:
   Clone the repository to your local machine:

   ```
   git clone https://github.com/yourusername/news-api.git
   cd news-api
   ```

2. **Build and Start Docker Containers**:
   Run the following command to build and start the Docker containers for the app, webserver (Nginx), and MySQL:

   ```
   docker-compose up --build
   ```

This command will create and run the following services defined in the `docker-compose.yml` file:

- **app (PHP service)**:
    - Builds using the Dockerfile at `docker/php/Dockerfile`.
    - Mounts the local `./src` directory to `/var/www/html` inside the container.
    - Runs on the `laravel` network.

- **webserver (Nginx service)**:
    - Builds using the Dockerfile at `docker/nginx/Dockerfile`.
    - Mounts the local `./src` directory to `/var/www/html` inside the container.
    - Exposes port `8080` on your local machine for web access.
    - Depends on the `app` service (PHP).
    - Runs on the `laravel` network.

- **mysql (MySQL service)**:
    - Uses the official MySQL 8.0 image.
    - Exposes port `3306` for MySQL access.
    - Sets up environment variables like `MYSQL_ROOT_PASSWORD`, `MYSQL_DATABASE`, `MYSQL_USER`, and `MYSQL_PASSWORD`.
    - Uses a named volume `mysql_data` to persist data across container restarts.

---

### Set Up Environment Variables

Make sure to update the `.env` file with your database credentials and any other required settings.

To do this, copy the `.env.example` file to `.env`:

```
cp .env.example .env
```

### Set Up Environment Variables

Make sure to update the `.env` file with your database credentials and any other required settings, including API keys for news sources.

To do this, copy the `.env.example` file to `.env`:

```
cp .env.example .env
```

Then, open the `.env` file and configure the following settings based on your environment:

- `DB_HOST`: Hostname of your MySQL database (typically `mysql` if using the Docker container).
- `DB_PORT`: Port number for MySQL (default is `3306`).
- `DB_DATABASE`: The name of your database (default is `news`).
- `DB_USERNAME`: The database user (default is `user`).
- `DB_PASSWORD`: The password for the database user (default is `password`).

Additionally, you will need to provide API keys for accessing external news APIs. Replace the placeholder keys with your actual API keys:

- `NEWSAPI_KEY`: Your API key for News API (e.g., `846c7b64a3184b08a42792dd9530a5ae`).
- `GUARDIAN_API_KEY`: Your API key for The Guardian (e.g., `eac96a45-3660-44e9-a09e-3984d95ffd30`).
- `NYTIMES_API_KEY`: Your API key for The New York Times (e.g., `mCeEwypaGY6oaTe36IJIORhHp912PnIS`).

After configuring the `.env` file, save the changes and proceed with setting up your environment.

Ensure that these values are correct for your local or production environment.

---

### Access the Application

Once the containers are up and running, the application should be accessible at:

- `http://localhost:8080` for the web interface.
- Swagger UI (for API documentation) will be available at `http://localhost:8080/api/documentation`.

---

### Swagger Documentation

Swagger has been integrated to automatically generate API documentation for easy reference. You can explore the available API endpoints, their descriptions, and request/response examples directly in the Swagger UI.

#### Key API Endpoints:
- **Register User**: `POST /api/register` - Registers a new user with name, email, and password.
- **Login User**: `POST /api/login` - Log in to get a bearer token for authenticated requests.
- **Personalized Feed**: `GET /api/personalized-feed` - Retrieves a personalized feed of articles based on user preferences (supports pagination).
- **Search Articles**: `GET /api/articles/search?q={query}` - Search for articles based on title, content, category, sources, and date range (supports pagination).

---

### Example Request:

#### Login:
To log in, send a `POST` request to `/api/login` with the following body:

```
{
"email": "johndoe@example.com",
"password": "yourpassword"
}
```

The response will include an `access_token` for use in subsequent API requests.

---

### Pagination

For endpoints that return lists (e.g., `/api/articles`, `/api/personalized-feed`), pagination is available. You can control the number of items per page using the `per_page` query parameter.

#### Example:

```
GET /api/articles?per_page=10&q=tech
```

This request will return 10 articles related to "tech" per page.

---

This version of the README now provides a more specific breakdown of the services defined in your `docker-compose.yml` file, and how they interact. It also includes the necessary steps to get your environment set up using Docker Compose.
