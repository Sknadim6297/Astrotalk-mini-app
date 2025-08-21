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
git clone https://github.com/Sknadim6297/strotalk-mini-app.git
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

