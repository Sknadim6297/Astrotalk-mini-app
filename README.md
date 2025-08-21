# 🔮 Astrotalk Mini App

A comprehensive astrology consultation platform built with Laravel, featuring real-time chat, wallet system, booking management, and availability tracking.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![WebSocket](https://img.shields.io/badge/WebSocket-Reverb-green.svg)

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation & Setup](#installation--setup)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Features Overview](#features-overview)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)

## ✨ Features

### 🔐 Authentication & Authorization
- **Multi-role system**: Users, Astrologers, and Admins
- **JWT/Sanctum authentication** with secure token management
- **Role-based access control** for different endpoints
- **Session management** with automatic cleanup

### 💰 Wallet System
- **Digital wallet** for users with secure balance management
- **Top-up functionality** with transaction history
- **Automatic deductions** during chat sessions
- **Comprehensive transaction logging**
- **Wallet analytics** and spending insights

### 📅 Booking Management
- **Session booking** with astrologers
- **Real-time availability checking**
- **Session duration tracking**
- **Automatic session management**
- **Booking history** and status tracking

### 💬 Real-time Chat System
- **WebSocket-powered** real-time messaging
- **Laravel Reverb** integration for live communication
- **Session-based** chat rooms
- **Message history** and persistence
- **Typing indicators** and connection status

### 🕐 Availability Management
- **Real-time online/offline status**
- **Smart availability detection**
- **Daily schedule management**
- **Manual availability override**
- **Last seen timestamps**

### 👨‍💼 Admin Features
- **User management** and role assignment
- **Astrologer approval system**
- **Platform analytics** and reporting
- **Revenue tracking**
- **System monitoring**

### 🔧 Advanced Features
- **Rate limiting** for API protection
- **Comprehensive error handling**
- **Input validation** and sanitization
- **CORS configuration**
- **API versioning** ready
- **Caching optimization**

## 🛠 Tech Stack

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Real-time**: Laravel Reverb (WebSocket)
- **Authentication**: Laravel Sanctum
- **Frontend**: Blade Templates + Bootstrap 5
- **Testing**: PHPUnit + Pest
- **Cache**: Redis (optional)
- **Queue**: Database/Redis


## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/astrotalk-mini-app.git
cd astrotalk-mini-app
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

```bash
# Create database (MySQL)
mysql -u root -p
CREATE DATABASE astrotalk_mini;
EXIT;

# Update .env file with database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=astrotalk_mini
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 5. Laravel Reverb Setup (WebSocket)

```bash
# Install Reverb
php artisan reverb:install

# Update .env for Reverb
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 6. Queue Configuration

```bash
# Update .env for queues
QUEUE_CONNECTION=database

# Create queue table
php artisan queue:table
php artisan migrate
```

### 7. Storage Setup

```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME="Astrotalk Mini App"
APP_ENV=local
APP_KEY=base64:your_generated_key
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=astrotalk_mini
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting & WebSocket
BROADCAST_DRIVER=reverb
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# Reverb Configuration
REVERB_APP_ID=1234567
REVERB_APP_KEY=abcdefghijklmnop
REVERB_APP_SECRET=secretkey123
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Mail Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🏃‍♂️ Running the Application

### Development Mode

```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start Reverb WebSocket server
php artisan reverb:start

# Terminal 3: Start queue worker
php artisan queue:work

# Terminal 4: Compile frontend assets (if needed)
npm run dev
```

### Access Points

- **Main Application**: http://localhost:8000
- **API Base URL**: http://localhost:8000/api
- **WebSocket Server**: ws://localhost:8080
- **Demo Pages**:
  - Availability Demo: http://localhost:8000/availability-demo
  - Debug Chat: http://localhost:8000/debug-chat
  - Availability Management: http://localhost:8000/availability-management

## 📖 API Documentation

### Authentication Endpoints

```http
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/me
```

### User Management

```http
GET    /api/user                    # Get current user info
PUT    /api/user/profile           # Update user profile
```

### Astrologer Endpoints

```http
GET    /api/astrologers                           # List all astrologers
GET    /api/astrologers/{id}                      # Get specific astrologer
GET    /api/astrologers/{id}/availability-status  # Check availability
```

### Booking System

```http
POST   /api/bookings                    # Create new booking
GET    /api/bookings/my-bookings        # User's bookings
GET    /api/bookings/astrologer-bookings # Astrologer's bookings
POST   /api/bookings/{id}/start         # Start session
POST   /api/bookings/{id}/end           # End session
POST   /api/bookings/{id}/cancel        # Cancel booking
```

### Wallet System

```http
GET    /api/wallet                  # Get wallet balance
POST   /api/wallet/add             # Add money to wallet
GET    /api/wallet/transactions    # Transaction history
GET    /api/wallet/stats           # Wallet statistics
```

### Chat System

```http
GET    /api/chat/{bookingId}/messages  # Get chat messages
POST   /api/chat/{bookingId}/send      # Send message
```

### Availability Management (Astrologers)

```http
POST   /api/astrologer/availability/toggle-online        # Toggle online status
POST   /api/astrologer/availability/toggle-available-now # Toggle immediate availability
POST   /api/astrologer/availability/set-today           # Set today's schedule
GET    /api/astrologer/availability/status              # Get own availability
```

## 🎯 Features Overview

### 1. User Registration & Authentication

**For Users:**
```bash
# Register as a user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
  }'
```

**For Astrologers:**
```bash
# Register as an astrologer
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Astrologer Name",
    "email": "astrologer@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "astrologer",
    "languages": ["English", "Hindi"],
    "specialization": ["Vedic", "Tarot"],
    "experience": 5,
    "per_minute_rate": 10.00,
    "bio": "Experienced astrologer"
  }'
```

### 2. Wallet Operations

**Add Money to Wallet:**
```bash
curl -X POST http://localhost:8000/api/wallet/add \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount": 100.00}'
```

**Check Balance:**
```bash
curl -X GET http://localhost:8000/api/wallet \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Booking Process

**Create Booking:**
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer USER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "astrologer_id": 1,
    "duration_minutes": 30
  }'
```

**Start Chat Session:**
```bash
curl -X POST http://localhost:8000/api/bookings/1/start \
  -H "Authorization: Bearer TOKEN"
```

### 4. Real-time Chat

**WebSocket Connection:**
```javascript
// Frontend JavaScript
const pusher = new Pusher('your_app_key', {
    wsHost: 'localhost',
    wsPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss']
});

const channel = pusher.subscribe('chat.1'); // booking_id = 1
channel.bind('message.sent', function(data) {
    console.log('New message:', data.message);
});
```

**Send Message:**
```bash
curl -X POST http://localhost:8000/api/chat/1/send \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello, how can I help you?"}'
```

### 5. Availability Management

**Toggle Online Status:**
```bash
curl -X POST http://localhost:8000/api/astrologer/availability/toggle-online \
  -H "Authorization: Bearer ASTROLOGER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"is_online": true}'
```

**Set Today's Schedule:**
```bash
curl -X POST http://localhost:8000/api/astrologer/availability/set-today \
  -H "Authorization: Bearer ASTROLOGER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "slots": [
      {"start_time": "09:00", "end_time": "12:00"},
      {"start_time": "14:00", "end_time": "18:00"}
    ]
  }'
```

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/WalletTest.php
```

### Test Categories

- **Unit Tests**: Model logic, helpers, utilities
- **Feature Tests**: API endpoints, integration tests
- **Browser Tests**: End-to-end functionality

### Sample Test Commands

```bash
# Test wallet functionality
php artisan test --filter=WalletTest

# Test booking system
php artisan test --filter=BookingTest

# Test authentication
php artisan test --filter=AuthTest
```

## 🎛 Admin Features

### Admin Dashboard Access

```bash
# Create admin user
php artisan tinker
>>> \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@astrotalk.com',
    'password' => bcrypt('admin123'),
    'role' => 'admin'
]);
```

### Admin Capabilities

1. **User Management**
   - View all users
   - Manage user roles
   - Block/unblock users

2. **Astrologer Management**
   - Approve/reject astrologer applications
   - Manage astrologer profiles
   - Set commission rates

3. **Analytics**
   - Revenue reports
   - Top performing astrologers
   - User engagement metrics
   - Session statistics

4. **System Management**
   - Platform settings
   - Fee structure
   - Content moderation

## 🔒 Security Features

### 1. Authentication Security
- **Token-based authentication** with expiry
- **Role-based access control**
- **Password hashing** with bcrypt
- **Session management**

### 2. API Security
- **Rate limiting** (60 requests per minute)
- **CORS configuration**
- **Input validation** and sanitization
- **SQL injection prevention**
- **XSS protection**

### 3. Data Security
- **Encrypted sensitive data**
- **Secure wallet transactions**
- **Audit trails** for financial operations
- **Data backup** procedures

## 🚀 Deployment

### Production Setup

1. **Server Requirements**
   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install php8.2 php8.2-fpm nginx mysql-server redis-server
   sudo apt install php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring
   ```

2. **Environment Configuration**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   # Use Redis for better performance
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

3. **Optimization Commands**
   ```bash
   # Production optimizations
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   
   # Set proper permissions
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

4. **Supervisor Setup** (for queue workers)
   ```ini
   [program:astrotalk-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/artisan queue:work --sleep=3 --tries=3
   autostart=true
   autorestart=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/path/to/worker.log
   ```

### Docker Deployment

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

### Docker Compose

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: astrotalk-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - astrotalk

  webserver:
    image: nginx:alpine
    container_name: astrotalk-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - astrotalk

  database:
    image: mysql:8.0
    container_name: astrotalk-db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: astrotalk_mini
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_PASSWORD: db_password
      MYSQL_USER: astrotalk_user
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - astrotalk

  redis:
    image: redis:alpine
    container_name: astrotalk-redis
    restart: unless-stopped
    networks:
      - astrotalk

networks:
  astrotalk:
    driver: bridge

volumes:
  dbdata:
    driver: local
```

## 🔍 Monitoring & Logging

### Log Files
- **Application Logs**: `storage/logs/laravel.log`
- **WebSocket Logs**: `storage/logs/reverb.log`
- **Queue Logs**: Monitor via `php artisan queue:monitor`

### Performance Monitoring
```bash
# Monitor queue status
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed

# Monitor WebSocket connections
php artisan reverb:ping
```

## 🐛 Troubleshooting

### Common Issues

1. **WebSocket Connection Failed**
   ```bash
   # Check if Reverb is running
   php artisan reverb:start
   
   # Verify port is not blocked
   netstat -an | grep 8080
   ```

2. **Queue Jobs Not Processing**
   ```bash
   # Restart queue worker
   php artisan queue:restart
   php artisan queue:work
   ```

3. **Database Connection Issues**
   ```bash
   # Test database connection
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

4. **Permission Issues**
   ```bash
   # Fix storage permissions
   sudo chmod -R 775 storage bootstrap/cache
   sudo chown -R www-data:www-data storage bootstrap/cache
   ```

### Debug Mode

```bash
# Enable debug mode
php artisan down
# Update .env: APP_DEBUG=true
php artisan up
php artisan config:clear
```

## 📚 Additional Resources

### Documentation Links
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Reverb](https://laravel.com/docs/broadcasting#reverb)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Pusher JavaScript Client](https://pusher.com/docs/channels/library_auth_reference/pusher-websockets-protocol/)

### API Testing
- **Postman Collection**: Available in `/docs/postman/`
- **Insomnia Collection**: Available in `/docs/insomnia/`

### Sample Data
```bash
# Generate sample data
php artisan db:seed --class=DatabaseSeeder
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Write comprehensive tests
- Document all API endpoints
- Use meaningful commit messages

## 📝 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 👥 Support

For support and questions:
- **Email**: support@astrotalk.com
- **Documentation**: [Wiki](https://github.com/yourusername/astrotalk-mini-app/wiki)
- **Issues**: [GitHub Issues](https://github.com/yourusername/astrotalk-mini-app/issues)

---

**Built with ❤️ using Laravel**

*Happy Coding! 🚀*

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

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
