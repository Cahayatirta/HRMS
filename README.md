# HRMS

## Project Overview

This repository contains the codebase for an HRMS (Human Resource Management System). While no detailed description was initially provided, this system appears to be built using PHP and JavaScript, leveraging tools like Tailwind CSS, Filament, and GitHub Actions for CI/CD. The project aims to streamline HR processes, potentially including access control, data management, and other related functionalities.

## Key Features & Benefits (Inferred)

Based on the file structure and technologies used, the HRMS likely offers the following features:

*   **Access Control:** Implemented using `AccessResource` and `AccessApiService`, enabling role-based permission management and controlled access to sensitive data.
*   **API Integration:** `AccessApiService` suggests the system utilizes API endpoints for data retrieval and manipulation.
*   **User Interface:** Built with Filament and Tailwind CSS, providing a modern and responsive user experience.
*   **Automated Testing and Linting:** Configured with GitHub Actions (`lint.yml` and `tests.yml`), ensuring code quality and stability through automated checks.
*   **Geolocalization:**  The presence of `filament-geolocate-me` suggests location tracking capabilities for employees or resources.

## Prerequisites & Dependencies

Before you begin, ensure you have the following installed:

*   **PHP:** Version 8.0 or higher (recommended).
*   **Node.js:** Version 16 or higher (required for frontend assets).
*   **Composer:** PHP dependency manager.
*   **npm** or **yarn:** JavaScript package manager.
*   **MySQL** or another compatible database.

**PHP Extensions:**

*   pdo\_mysql
*   mbstring
*   gd
*   curl

## Installation & Setup Instructions

1.  **Clone the Repository:**

    ```bash
    git clone git@github.com:Cahayatirta/HRMS.git
    cd HRMS
    ```

2.  **Install PHP Dependencies:**

    ```bash
    composer install
    ```

3.  **Install JavaScript Dependencies:**

    ```bash
    npm install
    # or
    yarn install
    ```

4.  **Configure Environment Variables:**

    *   Copy `.env.example` to `.env`:

        ```bash
        cp .env.example .env
        ```

    *   Edit the `.env` file with your database credentials, application URL, and other configurations:

        ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=your_database_name
        DB_USERNAME=your_database_user
        DB_PASSWORD=your_database_password

        APP_URL=http://localhost
        ```

5.  **Generate Application Key:**

    ```bash
    php artisan key:generate
    ```

6.  **Run Database Migrations:**

    ```bash
    php artisan migrate
    ```

7.  **Compile Assets:**

    ```bash
    npm run build
    # or
    yarn build
    ```

8.  **Serve the Application:**

    ```bash
    php artisan serve
    ```

    This will start the development server.  Open your browser and navigate to the URL specified in the output (typically `http://localhost:8000`).

9. **QuickShield Setup (Optional):**

    Run the `QuickShieldSetup` command.  The purpose of this command is not fully clear without additional information, but it may involve setting up security features.

    ```bash
    php artisan quickshield:setup
    ```

## Usage Examples & API Documentation

Due to the lack of detailed documentation, specific usage examples and API documentation are unavailable.  However, the following can be inferred:

*   **Access Control:**  Utilize the Filament resources (e.g., `AccessResource.php`) to manage user roles and permissions.
*   **API Endpoints:** Explore the `Api/AccessApiService.php` file to understand available API endpoints and their functionalities.  Common endpoints might include:
    *   `/api/access/create` (or similar): To create new access entries (handled by `CreateHandler.php`).

**Example API Call (Conceptual):**

```javascript
// Example using Axios
import axios from 'axios';

axios.post('/api/access/create', {
  user_id: 1,
  resource: 'dashboard',
  permission: 'read'
})
.then(response => {
  console.log(response.data);
})
.catch(error => {
  console.error(error);
});
```

**Note:**  Replace `/api/access/create` with the actual API endpoint and adjust the request parameters accordingly.

## Configuration Options

The primary configuration is done through the `.env` file.  Key variables include:

*   **Database Configuration:** `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
*   **Application URL:** `APP_URL`
*   **Debugging:** `APP_DEBUG` (set to `true` for development, `false` for production)

Additional configuration options might be available within the Filament resources or other parts of the codebase.

## Contributing Guidelines

We welcome contributions! To contribute:

1.  Fork the repository.
2.  Create a new branch for your feature or bug fix.
3.  Make your changes and ensure that all tests pass.
4.  Submit a pull request with a clear description of your changes.

Please follow these guidelines:

*   Write clear and concise commit messages.
*   Adhere to the existing code style.
*   Include tests for new features and bug fixes.
*   Document your code thoroughly.

## License Information

License not specified. All rights reserved by Cahayatirta. Further clarification is required to determine the licensing terms.

## Acknowledgments

*   Tailwind CSS
*   Filament
*   Laravel
*   GitHub Actions
