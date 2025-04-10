# Compensation Management System

This application is built using PHP, Laravel, Vue.js, and Inertia. It allows users to manage customers, handle debt compensations, generate documents, and export data.

## Features
- Add and edit customers
- Manage compensations (debt settlements)
- Generate and export documents
- Data export functionality

## Technologies Used
- **Backend:** Laravel (PHP)
- **Frontend:** Vue.js with Inertia.js
- **Database:** MySQL (or other supported DBs)
- **Authentication:** Laravel Breeze

## Installation

### Prerequisites
Make sure you have the following installed:
- PHP (>=8.0 recommended)
- Composer
- Node.js & npm
- MySQL or another database system

### Steps

1. Clone the repository:
   ```sh
   git clone https://github.com/greentech77/Kompenzacije.git
   cd your-repo
   ```

2. Install dependencies:
   ```sh
   composer install
   npm install
   ```

3. Configure environment:
   ```sh
   cp .env.example .env
   php artisan key:generate
   ```
   Update the `.env` file with your database credentials.

4. Run migrations:
   ```sh
   php artisan migrate --seed
   ```

5. Start the development server:
   ```sh
   php artisan serve
   ```

6. Run frontend development server:
   ```sh
   npm run dev
   ```

## Usage
- Navigate to `http://localhost:8000`
- Register or log in (if authentication is enabled)
- Manage customers and compensations

## Deployment
- Build frontend assets:
  ```sh
  npm run build
  ```
- Set up a web server (Nginx/Apache) and configure Laravel accordingly
- Run database migrations on production

## Contribution
Feel free to submit issues and pull requests to improve the application.

## License
This project is licensed under the MIT License.

