# Laravel Sales Reporting API  
A complete Laravel REST API project including authentication, order management, order items, and a full reporting dashboard with caching.  
This project meets all required assessment criteria including Eloquent performance notes, API documentation, caching, and reporting.

---

# 📂 Project Structure

```bash
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── OrderController.php
│   │   ├── OrderItemController.php
│   │   ├── ReportController.php
│   │
│   ├── Middleware/
│
├── Models/
│   ├── User.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│
├── routes/
│   ├── api.php
```

---

# 🚀 Setup Instructions

## 1. Clone Project  
```bash
git clone <repo-url>
cd project-folder
```

## 2. Install Dependencies  
```bash
composer install
```

## 3. Create .env File  
```bash
cp .env.example .env
```

## 4. Generate App Key  
```bash
php artisan key:generate
```

## 5. Run Migrations & Seeders  
```bash
php artisan migrate --seed
```

## 6. Start Server  
```bash
php artisan serve
```

API base URL:  
```
http://localhost:8000/api/
```

---

# 🔐 Authentication API

## **POST /register**
```json
{
  "name": "John",
  "email": "john@gmail.com",
  "password": "password",
  "password_confirmation": "password"
}
```

## **POST /login**
```json
{
  "email": "john@gmail.com",
  "password": "password"
}
```

## **POST /logout**  
Requires Bearer Token

---

# 📦 Products API

## **GET /products**
Get product list.

---

# 🧾 Orders API

## **POST /orders**
```json
{
  "customer_name": "David",
  "total_amount": 65000,
  "items": [
    { "product_id": 1, "quantity": 2, "price": 12000 },
    { "product_id": 3, "quantity": 1, "price": 41000 }
  ]
}
```

## **GET /orders**
List all orders.

## **GET /orders/{id}**
Show order with items.

---

# 📊 Reporting Dashboard API

### **GET /report**
Supports:
- Total sales  
- Monthly sales  
- Daily sales  
- Top products  
- Date filters  
- Caching  

Example:
```
GET /api/report?from=2024-01-01&to=2024-12-31
```

---

### Sample Response
```json
{
  "total_sales": 1500000,
  "monthly": [],
  "daily": [],
  "top_products": []
}
```

---

# ⚙ Performance Notes (Assessment Requirement #11)

### ✔ 1. N+1 Query Prevention
```php
Order::with('items.product')->get();
```

### ✔ 2. Cached Reports (1 hour)
```php
Cache::remember($cacheKey, 3600, function () { ... });
```

### ✔ 3. Optimized Grouping  
```php
DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
```

### ✔ 4. Query Builder for heavy aggregations  
```php
DB::table('orders')->select(...)->groupBy(...);
```

---

# 📘 Technology Used

- Laravel 10  
- MySQL  
- Laravel Sanctum  
- Redis/File Cache  
- Eloquent ORM  
- REST API Architecture  

---

# 🎯 Completed Requirements

| Requirement | Status |
|------------|--------|
| Authentication | ✔ |
| CRUD (Orders, Items, Products) | ✔ |
| Reporting Dashboard API | ✔ |
| Date Filters | ✔ |
| Caching | ✔ |
| Eloquent Performance Notes | ✔ |
| API Documentation | ✔ |
| Postman Testing | ✔ |

---

# ✍️ Author  
**Hlaing Min Kyaw**

