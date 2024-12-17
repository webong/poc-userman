# User Management API

This project provides an API for user management in Laravel, offering authentication and user data operations.

## Setup Instructions

1. Clone the repository:
   ```bash
   git clone <repo link>
   ```

2. Navigate to the project directory and install dependencies:
   ```bash
   cd <project-directory>
   composer install
   ```

3. Set up the database:
    - Update your `.env` file with the correct database configuration.
    - Run the following command to migrate the database and seed initial data:
      ```bash
      php artisan migrate:fresh --seed
      ```

4. Generate a personal access token for Laravel Passport:
   ```bash
   php artisan passport:client --personal
   ```

5. Start the server:
   ```bash
   php artisan serve
   ```

## Running Tests

To execute the test suite, run:
```bash
php artisan test
```

## Documentation

For detailed API documentation, please refer to the [API Documentation](<http://127.0.0.1:8000/docs/api>).
(http://127.0.0.1:8000/docs/api)
---

### Notes

- Ensure the database is configured and running before starting the server.
- Use the personal access token generated during setup to authenticate protected routes.

