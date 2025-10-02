# Beauty Salon Management System - API Documentation

## Overview
This comprehensive API documentation provides detailed information about all available endpoints, authentication methods, request/response formats, and integration examples for the Beauty Salon Management System.

## Table of Contents
1. [Getting Started](#getting-started)
2. [Authentication](#authentication)
3. [Rate Limiting](#rate-limiting)
4. [Error Handling](#error-handling)
5. [Data Formats](#data-formats)
6. [API Endpoints](#api-endpoints)
7. [Webhooks](#webhooks)
8. [SDKs and Libraries](#sdks-and-libraries)
9. [Examples](#examples)
10. [Testing](#testing)

---

## Getting Started

### Base URL
```
Production: https://yourdomain.com/api/v1
Development: http://localhost:8000/api/v1
```

### API Versioning
The API uses URL-based versioning. Current version is `v1`.

### Content Type
All requests and responses use `application/json` content type.

### Character Encoding
All text content is UTF-8 encoded.

---

## Authentication

### API Token Authentication
The API uses Bearer token authentication. Include your API token in the Authorization header:

```http
Authorization: Bearer {your-api-token}
```

### Obtaining an API Token
1. Log in to the admin dashboard
2. Navigate to "API Management"
3. Generate a new API token
4. Store the token securely

### Token Permissions
API tokens inherit permissions from the user account that created them.

### Token Expiration
- API tokens expire after 1 year
- Refresh tokens expire after 30 days
- Use the refresh endpoint to obtain new tokens

---

## Rate Limiting

### Rate Limits
- **General API**: 1000 requests per hour
- **Authentication**: 5 requests per minute
- **File Upload**: 100 requests per hour
- **Bulk Operations**: 50 requests per hour

### Rate Limit Headers
```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

### Exceeding Rate Limits
When rate limits are exceeded, the API returns HTTP 429 with the following response:

```json
{
    "error": "Rate limit exceeded",
    "message": "Too many requests. Please try again later.",
    "retry_after": 3600
}
```

---

## Error Handling

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Rate Limit Exceeded
- `500` - Internal Server Error

### Error Response Format
```json
{
    "error": "validation_failed",
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Common Error Codes
- `INVALID_TOKEN` - Invalid or expired API token
- `INSUFFICIENT_PERMISSIONS` - User lacks required permissions
- `VALIDATION_ERROR` - Request data validation failed
- `RESOURCE_NOT_FOUND` - Requested resource does not exist
- `DUPLICATE_ENTRY` - Resource already exists

---

## Data Formats

### Date and Time
All dates and times are in ISO 8601 format with UTC timezone:
```
2024-12-20T14:30:00Z
```

### Pagination
List endpoints support pagination with the following parameters:
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

### Pagination Response
```json
{
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150
    },
    "links": {
        "first": "https://api.example.com/v1/endpoint?page=1",
        "last": "https://api.example.com/v1/endpoint?page=10",
        "prev": null,
        "next": "https://api.example.com/v1/endpoint?page=2"
    }
}
```

### Filtering and Sorting
- `filter[field]` - Filter by specific field
- `sort` - Sort field (prefix with `-` for descending)
- `search` - Global search across multiple fields

---

## API Endpoints

### Authentication Endpoints

#### Login
```http
POST /auth/login
```

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "admin"
    }
}
```

#### Logout
```http
POST /auth/logout
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Successfully logged out"
}
```

#### Refresh Token
```http
POST /auth/refresh
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Response:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

### User Endpoints

#### Get Current User
```http
GET /user
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Response:**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "admin",
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-12-20T14:30:00Z"
}
```

#### Update User Profile
```http
PUT /user
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Request:**
```json
{
    "name": "John Smith",
    "email": "john.smith@example.com"
}
```

**Response:**
```json
{
    "id": 1,
    "name": "John Smith",
    "email": "john.smith@example.com",
    "role": "admin",
    "updated_at": "2024-12-20T14:30:00Z"
}
```

### Client Endpoints

#### List Clients
```http
GET /clients
```

**Query Parameters:**
- `page` - Page number
- `per_page` - Items per page
- `search` - Search term
- `filter[status]` - Filter by status
- `sort` - Sort field

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Jane Smith",
            "email": "jane@example.com",
            "phone": "+1234567890",
            "status": "active",
            "created_at": "2024-01-01T00:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

#### Get Client
```http
GET /clients/{id}
```

**Response:**
```json
{
    "id": 1,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "phone": "+1234567890",
    "address": "123 Main St",
    "city": "New York",
    "state": "NY",
    "zip_code": "10001",
    "date_of_birth": "1990-01-01",
    "status": "active",
    "preferences": {
        "notifications": true,
        "marketing": false
    },
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-12-20T14:30:00Z"
}
```

#### Create Client
```http
POST /clients
```

**Request:**
```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "phone": "+1234567890",
    "address": "123 Main St",
    "city": "New York",
    "state": "NY",
    "zip_code": "10001",
    "date_of_birth": "1990-01-01"
}
```

**Response:**
```json
{
    "id": 1,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "phone": "+1234567890",
    "status": "active",
    "created_at": "2024-12-20T14:30:00Z"
}
```

#### Update Client
```http
PUT /clients/{id}
```

**Request:**
```json
{
    "name": "Jane Johnson",
    "phone": "+1234567891"
}
```

**Response:**
```json
{
    "id": 1,
    "name": "Jane Johnson",
    "email": "jane@example.com",
    "phone": "+1234567891",
    "updated_at": "2024-12-20T14:30:00Z"
}
```

#### Delete Client
```http
DELETE /clients/{id}
```

**Response:**
```json
{
    "message": "Client deleted successfully"
}
```

### Service Endpoints

#### List Services
```http
GET /services
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Hair Cut",
            "description": "Professional hair cutting service",
            "category": "Hair Services",
            "duration": 60,
            "price": 50.00,
            "is_active": true,
            "requires_staff": true,
            "max_clients": 1
        }
    ]
}
```

#### Get Service
```http
GET /services/{id}
```

**Response:**
```json
{
    "id": 1,
    "name": "Hair Cut",
    "description": "Professional hair cutting service",
    "category": "Hair Services",
    "duration": 60,
    "price": 50.00,
    "is_active": true,
    "requires_staff": true,
    "max_clients": 1,
    "booking_advance_days": 30,
    "cancellation_hours": 24,
    "created_at": "2024-01-01T00:00:00Z"
}
```

### Appointment Endpoints

#### List Appointments
```http
GET /appointments
```

**Query Parameters:**
- `date` - Filter by date (YYYY-MM-DD)
- `status` - Filter by status
- `client_id` - Filter by client
- `staff_id` - Filter by staff
- `service_id` - Filter by service

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "client_id": 1,
            "staff_id": 1,
            "service_id": 1,
            "location_id": 1,
            "appointment_date": "2024-12-21T10:00:00Z",
            "duration": 60,
            "price": 50.00,
            "status": "scheduled",
            "notes": "First time client",
            "created_at": "2024-12-20T14:30:00Z"
        }
    ]
}
```

#### Create Appointment
```http
POST /appointments
```

**Request:**
```json
{
    "client_id": 1,
    "staff_id": 1,
    "service_id": 1,
    "location_id": 1,
    "appointment_date": "2024-12-21T10:00:00Z",
    "notes": "First time client"
}
```

**Response:**
```json
{
    "id": 1,
    "client_id": 1,
    "staff_id": 1,
    "service_id": 1,
    "location_id": 1,
    "appointment_date": "2024-12-21T10:00:00Z",
    "duration": 60,
    "price": 50.00,
    "status": "scheduled",
    "notes": "First time client",
    "created_at": "2024-12-20T14:30:00Z"
}
```

#### Update Appointment
```http
PUT /appointments/{id}
```

**Request:**
```json
{
    "status": "completed",
    "notes": "Service completed successfully"
}
```

**Response:**
```json
{
    "id": 1,
    "status": "completed",
    "notes": "Service completed successfully",
    "updated_at": "2024-12-20T14:30:00Z"
}
```

### Staff Endpoints

#### List Staff
```http
GET /staff
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Sarah Johnson",
            "email": "sarah@example.com",
            "phone": "+1234567890",
            "position": "Senior Stylist",
            "hire_date": "2023-01-01",
            "is_active": true,
            "specialties": ["Hair Cutting", "Coloring"],
            "location_id": 1
        }
    ]
}
```

### Inventory Endpoints

#### List Inventory Items
```http
GET /inventory
```

**Query Parameters:**
- `category_id` - Filter by category
- `location_id` - Filter by location
- `low_stock` - Show only low stock items
- `expiring_soon` - Show items expiring soon

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Shampoo",
            "description": "Professional shampoo",
            "category": "Hair Care",
            "sku": "SH-001",
            "quantity": 50,
            "min_quantity": 10,
            "unit_price": 15.00,
            "supplier": "Beauty Supply Co",
            "expiry_date": "2025-12-31",
            "is_active": true
        }
    ]
}
```

#### Update Inventory
```http
PUT /inventory/{id}
```

**Request:**
```json
{
    "quantity": 45,
    "notes": "Used 5 units for services"
}
```

### Report Endpoints

#### Get Dashboard Data
```http
GET /reports/dashboard
```

**Query Parameters:**
- `date_range` - Date range (today, week, month, year)
- `location_id` - Filter by location

**Response:**
```json
{
    "total_appointments": 150,
    "total_revenue": 7500.00,
    "new_clients": 25,
    "staff_performance": [
        {
            "staff_id": 1,
            "name": "Sarah Johnson",
            "appointments": 45,
            "revenue": 2250.00
        }
    ],
    "popular_services": [
        {
            "service_id": 1,
            "name": "Hair Cut",
            "bookings": 60,
            "revenue": 3000.00
        }
    ]
}
```

#### Get Financial Report
```http
GET /reports/financial
```

**Query Parameters:**
- `start_date` - Start date (YYYY-MM-DD)
- `end_date` - End date (YYYY-MM-DD)
- `group_by` - Group by (day, week, month)

**Response:**
```json
{
    "total_revenue": 15000.00,
    "total_expenses": 5000.00,
    "net_profit": 10000.00,
    "daily_breakdown": [
        {
            "date": "2024-12-20",
            "revenue": 750.00,
            "expenses": 250.00,
            "profit": 500.00
        }
    ]
}
```

---

## Webhooks

### Webhook Configuration
Webhooks allow you to receive real-time notifications when events occur in the system.

#### Supported Events
- `appointment.created` - New appointment created
- `appointment.updated` - Appointment updated
- `appointment.cancelled` - Appointment cancelled
- `client.created` - New client registered
- `payment.completed` - Payment processed
- `inventory.low_stock` - Inventory item low stock

#### Webhook Payload
```json
{
    "event": "appointment.created",
    "timestamp": "2024-12-20T14:30:00Z",
    "data": {
        "id": 1,
        "client_id": 1,
        "staff_id": 1,
        "service_id": 1,
        "appointment_date": "2024-12-21T10:00:00Z",
        "status": "scheduled"
    }
}
```

#### Webhook Security
Webhooks include a signature header for verification:
```http
X-Webhook-Signature: sha256=abc123...
```

---

## SDKs and Libraries

### JavaScript/Node.js
```bash
npm install beauty-salon-api
```

```javascript
const BeautySalonAPI = require('beauty-salon-api');

const api = new BeautySalonAPI({
    baseURL: 'https://yourdomain.com/api/v1',
    token: 'your-api-token'
});

// Get clients
const clients = await api.clients.list();

// Create appointment
const appointment = await api.appointments.create({
    client_id: 1,
    staff_id: 1,
    service_id: 1,
    appointment_date: '2024-12-21T10:00:00Z'
});
```

### PHP
```bash
composer require beauty-salon/api-client
```

```php
<?php
use BeautySalon\ApiClient;

$api = new ApiClient([
    'base_url' => 'https://yourdomain.com/api/v1',
    'token' => 'your-api-token'
]);

// Get clients
$clients = $api->clients()->list();

// Create appointment
$appointment = $api->appointments()->create([
    'client_id' => 1,
    'staff_id' => 1,
    'service_id' => 1,
    'appointment_date' => '2024-12-21T10:00:00Z'
]);
```

### Python
```bash
pip install beauty-salon-api
```

```python
from beauty_salon_api import BeautySalonAPI

api = BeautySalonAPI(
    base_url='https://yourdomain.com/api/v1',
    token='your-api-token'
)

# Get clients
clients = api.clients.list()

# Create appointment
appointment = api.appointments.create({
    'client_id': 1,
    'staff_id': 1,
    'service_id': 1,
    'appointment_date': '2024-12-21T10:00:00Z'
})
```

---

## Examples

### Complete Appointment Booking Flow
```javascript
// 1. Get available services
const services = await api.services.list();

// 2. Get available staff for a service
const staff = await api.staff.list({
    'filter[service_id]': 1
});

// 3. Check availability
const availability = await api.appointments.availability({
    staff_id: 1,
    service_id: 1,
    date: '2024-12-21'
});

// 4. Create appointment
const appointment = await api.appointments.create({
    client_id: 1,
    staff_id: 1,
    service_id: 1,
    appointment_date: '2024-12-21T10:00:00Z'
});

// 5. Process payment
const payment = await api.payments.create({
    appointment_id: appointment.id,
    amount: 50.00,
    payment_method: 'credit_card'
});
```

### Bulk Operations
```javascript
// Bulk create clients
const clients = await api.clients.bulkCreate([
    {
        name: 'Client 1',
        email: 'client1@example.com',
        phone: '+1234567890'
    },
    {
        name: 'Client 2',
        email: 'client2@example.com',
        phone: '+1234567891'
    }
]);

// Bulk update appointments
const appointments = await api.appointments.bulkUpdate([
    { id: 1, status: 'completed' },
    { id: 2, status: 'completed' }
]);
```

### Error Handling
```javascript
try {
    const appointment = await api.appointments.create(data);
} catch (error) {
    if (error.status === 422) {
        // Validation error
        console.log('Validation errors:', error.errors);
    } else if (error.status === 401) {
        // Authentication error
        console.log('Invalid token');
    } else {
        // Other error
        console.log('Error:', error.message);
    }
}
```

---

## Testing

### API Testing with cURL
```bash
# Login
curl -X POST https://yourdomain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'

# Get clients
curl -X GET https://yourdomain.com/api/v1/clients \
  -H "Authorization: Bearer your-token"

# Create appointment
curl -X POST https://yourdomain.com/api/v1/appointments \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{"client_id":1,"staff_id":1,"service_id":1,"appointment_date":"2024-12-21T10:00:00Z"}'
```

### Postman Collection
Import the Postman collection for easy API testing:
```
https://yourdomain.com/api/v1/postman-collection.json
```

### API Documentation
Interactive API documentation available at:
```
https://yourdomain.com/api/docs
```

---

## Conclusion

This API documentation provides comprehensive information for integrating with the Beauty Salon Management System. For additional support or questions, please contact the technical support team.

### Support Resources
- API Documentation: https://yourdomain.com/api/docs
- Postman Collection: https://yourdomain.com/api/v1/postman-collection.json
- SDK Downloads: https://yourdomain.com/api/sdks
- Support Email: api-support@beautysalon.com

---

**Last Updated**: December 2024  
**Version**: 1.0  
**API Version**: v1.0
