# Laravel Orders & Payments API

A professional RESTful API built with Laravel for managing user authentication, orders, and payments with support for multiple payment gateways.

---

## 🚀 Features

- User Authentication (Register, Login, Logout, Get Profile)
- Order Management (Create, Read, Update, Delete)
- Payment Processing (Credit Card, PayPal)
- Token-based authentication (JWT)
- Fully documented with Postman collection and environment

---

## 🛠️ Tech Stack

- **Backend:** Laravel  
- **Database:** MySQL / PostgreSQL  
- **Authentication:** JWT  
- **API Testing:** Postman

---

## 📦 Installation / Setup

1. **Clone the repository**
```bash
git clone https://github.com/roudeena/laravel-orders-payments-api.git
cd laravel-orders-payments-api
```

2. **Install dependencies**
```bash
composer install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```
Update `.env` with your database credentials:
```env
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

4. **Run migrations**
```bash
php artisan migrate
```

5. **Serve the application**
```bash
php artisan serve
```
The API will be available at:
```
http://127.0.0.1:8000
```

---

## 🛧️ Postman Collection Setup

1. Open Postman.
2. Import the collection:
```
postman/Laravel Orders & Payments API.postman_collection.json
```
3. Import the environment:
```
postman/Laravel API.postman_environment.json
```
4. Select the environment `Laravel API`.
5. Login first using a test user to populate the `token` variable:
```
Email: john@example.com
Password: password123
```
6. All protected endpoints (Orders, Payments, Me, Logout) now use the saved token automatically.

---

## 🔒 Authentication

All protected endpoints require a JWT token.  

**Header:**
```
Authorization: Bearer {{token}}
```

### Endpoints:

| Method | Endpoint      | Description          |
|--------|---------------|--------------------|
| POST   | /api/register | Register new user    |
| POST   | /api/login    | Login user           |
| GET    | /api/me       | Get authenticated user|
| POST   | /api/logout   | Logout user          |

**Example Login Request:**
```json
POST /api/login
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Example Login Response:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

---

## 📦 Orders

| Method | Endpoint         | Description       |
|--------|-----------------|-----------------|
| GET    | /api/orders     | List all orders   |
| POST   | /api/orders     | Create new order  |
| GET    | /api/orders/{id}| Get order by ID   |
| PUT    | /api/orders/{id}| Update order      |
| DELETE | /api/orders/{id}| Delete order      |

**Example Create Order Request:**
```json
POST /api/orders
Authorization: Bearer {{token}}

{
  "items": [
    { "product_name": "Product A", "quantity": 2, "price": 50 },
    { "product_name": "Product B", "quantity": 1, "price": 100 }
  ]
}
```

**Example Create Order Response:**
```json
{
  "id": 1,
  "user_id": 1,
  "status": "pending",
  "items": [
    { "product_name": "Product A", "quantity": 2, "price": 50 },
    { "product_name": "Product B", "quantity": 1, "price": 100 }
  ],
  "created_at": "2026-02-03T10:00:00Z"
}
```

---

## 💳 Payments

| Method | Endpoint              | Description                  |
|--------|----------------------|------------------------------|
| POST   | /api/payments         | Process payment              |
| GET    | /api/payments         | List all payments            |
| GET    | /api/payments/{order} | Get payments for an order    |

**Example Payment Request (Credit Card):**
```json
POST /api/payments
Authorization: Bearer {{token}}

{
  "order_id": 1,
  "payment_method": "credit_card"
}
```

**Example Payment Response:**
```json
{
  "id": 1,
  "order_id": 1,
  "payment_method": "credit_card",
  "status": "paid",
  "amount": 200,
  "paid_at": "2026-02-03T11:00:00Z"
}
```

💳 Payment Gateway Extensibility

The API uses a flexible and maintainable system to handle multiple payment methods without modifying controllers or core logic.

Architecture

PaymentGatewayInterface

Defines a standard pay() method that all gateways must implement:

public function pay(Order $order, array $data): array;


Ensures consistent responses across different payment methods.

Gateway Implementations

CreditCardGateway handles credit card payments:

'payment_id' => 'CC' . time(),
'status' => 'successful',


PayPalGateway handles PayPal payments:

'payment_id' => 'PP' . time(),
'status' => 'successful',


Each gateway encapsulates its provider-specific logic.

PaymentGatewayFactory

Dynamically selects the appropriate gateway:

$gateway = PaymentGatewayFactory::make('paypal');
$response = $gateway->pay($order, $data);


Adding new gateways only requires creating a new class implementing PaymentGatewayInterface and registering it in the factory.

How to Add a New Payment Method

Create a new class implementing PaymentGatewayInterface.

Add a case in PaymentGatewayFactory::make() for the new method.

Done! No changes needed in controllers or models.

Benefits

✅ Extensible: Add new gateways without touching existing code

✅ Consistent: All gateways return the same response structure

✅ Testable: Each gateway can be tested independently

✅ Follows SOLID principles (Open/Closed, Interface Segregation)

---

## 📂 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
├── Models/
├── Services/
routes/
├── api.php
postman/
├── LaravelOrdersPaymentsAPI.postman_collection.json
├── LaravelOrdersPaymentsAPI.postman_environment.json
```

---

## 📜 License

MIT License

---

## 👤 Author

**Roudeena** — Laravel & API Developer

---

## ⭐ Support

If you find this project useful, please ⭐ it on GitHub!

