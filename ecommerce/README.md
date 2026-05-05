# 🛒 E-Commerce Platform

A complete core PHP-based e-commerce platform with AJAX shopping cart, admin catalog management, and role-based customer/admin dashboards.

---

## ✨ Features

### Customer Features
- ✅ **Product Browsing** - Browse and search products
- ✅ **Shopping Cart** - AJAX add/update/remove items
- ✅ **Wishlist** - Save favorite products
- ✅ **Product Reviews** - Rate and review products
- ✅ **Checkout** - Multi-step checkout process
- ✅ **Order Management** - Track order history
- ✅ **Coupon System** - Apply discount codes
- ✅ **User Account** - Manage profile and orders

### Admin Features
- ✅ **Dashboard Analytics** - Revenue, orders, products metrics
- ✅ **Product Management** - Add/Edit/Delete products
- ✅ **Category Management** - Organize products by categories
- ✅ **Order Management** - Process and track orders
- ✅ **Coupon Management** - Create and manage discount codes
- ✅ **User Management** - Manage customer accounts
- ✅ **Reports** - Sales and inventory reports

### Technical Features
- 🔐 Secure authentication (bcrypt passwords)
- 🔐 CSRF protection on all forms
- 🔐 Role-based access control (Admin/Customer)
- 🔐 Session management
- 💾 Product images support
- 💳 Payment method selection (ready for Razorpay)

### Technical Stack
- **Backend**: Core PHP (PDO)
- **Database**: MySQL 5.7+
- **Frontend**: HTML/CSS + Vanilla JavaScript
- **API**: JSON-based AJAX endpoints
- **Authentication**: Session-based
- **Payment**: Razorpay integration (framework ready)

---

## 🚀 Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Git (optional)

### Step 1: Setup Database
```bash
# Create database
mysql -u root -p
> CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> EXIT;

# Import schema
mysql -u root -p ecommerce_db < schema.sql

# Seed initial data
mysql -u root -p ecommerce_db < seed.sql
```

### Step 2: Configure Database Connection
Edit `admin/includes/bootstrap.php` and `customer/includes/bootstrap.php`:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'ecommerce_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 3: Create Uploads Directory
```bash
mkdir -p uploads/products
chmod 755 uploads/products
```

### Step 4: Start Web Server
```bash
cd ecommerce
php -S localhost:8000
```

### Step 5: Access Application
- **Customer**: `http://localhost:8000/customer/pages/home.php`
- **Admin**: `http://localhost:8000/admin/index.php`

---

## 📋 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | rajuchaswik@gmail.com | Raju@2006 |

### Register New Customer Account
1. Navigate to: `http://localhost:8000/customer/pages/register.php`
2. Fill in your details
3. Login with your credentials

---

## 📚 Module Documentation

### Customer Portal

#### Home Page (`customer/pages/home.php`)
- Product catalog display
- Featured products
- Search and filter options

#### Product Listing (`customer/pages/listing.php`)
- All available products
- Category filtering
- Price sorting
- Product cards with images

#### Product Detail (`customer/pages/product.php`)
- Full product information
- Image gallery
- Customer reviews
- Add to cart button
- Add to wishlist option

#### Shopping Cart (`customer/pages/cart.php`)
**AJAX Operations:**
- Add items to cart
- Update quantities
- Remove items
- Apply coupons
- View cart total

**Key File**: `customer/assets/app.js`
```javascript
fetch('/api/cart.php', {
  method: 'POST',
  body: new FormData({
    action: 'add',
    product_id: productId,
    quantity: 1
  })
});
```

#### Checkout (`customer/pages/checkout.php`)
- Multi-step checkout form
- Shipping information
- Payment method selection (Cash on Delivery / Razorpay)
- Order summary
- Order placement

#### Order Management (`customer/pages/account.php`)
- View order history
- Track order status
- Download invoices
- Return orders

#### Wishlist (`customer/pages/wishlist.php`)
- Saved favorite products
- Move to cart
- Remove from wishlist

### Admin Panel

#### Dashboard (`admin/index.php`)
- Key metrics (revenue, orders, products)
- Low stock alerts
- Recent orders
- Top products

#### Product Management (`admin/modules/products/`)
- **List Products** - `index.php`
- **Add Product** - `add.php`
- **Edit Product** - `edit.php`
- **Delete Product** - `delete.php`

**Product Fields:**
- Name, SKU, Description
- Price, Sale Price
- Stock Quantity
- Category
- Images
- Status (Active/Inactive)

#### Category Management (`admin/modules/categories/`)
- Create categories
- Edit category details
- View product count per category
- Delete categories

#### Order Management (`admin/modules/orders/`)
- View all orders
- Order details
- Update order status
- Generate invoices
- Track payment status

#### Coupon Management (`admin/modules/coupons/`)
- Create discount codes
- Set discount type (Fixed/Percentage)
- Define usage limits
- Set expiration dates
- Track coupon usage

#### User Management (`admin/modules/users/`)
- List all customers
- Manage user roles
- Activate/Deactivate accounts
- View customer details

---

## 🔄 AJAX Implementation

### Cart API (`api/cart.php`)

**Add to Cart**
```php
POST /api/cart.php
{
  "action": "add",
  "product_id": 5,
  "quantity": 1,
  "_csrf": "token"
}

Response:
{
  "success": true,
  "message": "Added to cart",
  "count": 3
}
```

**Update Cart**
```php
POST /api/cart.php
{
  "action": "update",
  "product_id": 5,
  "quantity": 2,
  "_csrf": "token"
}
```

**Remove from Cart**
```php
POST /api/cart.php
{
  "action": "remove",
  "product_id": 5,
  "_csrf": "token"
}
```

### Coupon API (`api/coupon.php`)

**Validate Coupon**
```php
POST /api/coupon.php
{
  "code": "SAVE20",
  "_csrf": "token"
}

Response:
{
  "success": true,
  "message": "Coupon applied",
  "coupon": {
    "id": 1,
    "code": "SAVE20",
    "type": "percentage",
    "value": 20
  }
}
```

### Product Search API (`api/search.php`)

**Search Products**
```php
POST /api/search.php
{
  "query": "laptop",
  "_csrf": "token"
}

Response:
{
  "success": true,
  "products": [
    {
      "id": 5,
      "name": "Dell Laptop XPS",
      "price": "85000",
      "images_json": "[\"image1.jpg\"]"
    }
  ]
}
```

---

## 📂 Directory Structure

```
ecommerce/
├── index.php                          # Redirect to home
├── schema.sql                         # Database schema
├── seed.sql                           # Sample data
├── customer/
│   ├── pages/
│   │   ├── home.php                   # Customer home
│   │   ├── listing.php                # Product listing
│   │   ├── product.php                # Product detail
│   │   ├── cart.php                   # Shopping cart
│   │   ├── checkout.php               # Checkout form
│   │   ├── account.php                # Order history
│   │   ├── wishlist.php               # Saved items
│   │   ├── login.php                  # Customer login
│   │   ├── register.php               # Registration
│   │   └── logout.php                 # Logout
│   ├── assets/
│   │   ├── css/                       # Stylesheets
│   │   ├── js/app.js                  # AJAX operations
│   │   └── images/                    # Static images
│   └── includes/
│       ├── bootstrap.php              # Config & auth
│       ├── functions.php              # Helpers
│       └── header.php                 # Navigation
├── admin/
│   ├── index.php                      # Admin dashboard
│   ├── modules/
│   │   ├── products/
│   │   │   ├── index.php              # Product list
│   │   │   ├── add.php                # Add product
│   │   │   ├── edit.php               # Edit product
│   │   │   └── delete.php             # Delete product
│   │   ├── categories/
│   │   ├── orders/
│   │   ├── coupons/
│   │   ├── users/
│   │   └── reports/
│   └── includes/
│       ├── bootstrap.php              # Admin config
│       └── functions.php              # Admin helpers
├── api/
│   ├── cart.php                       # Cart AJAX
│   ├── coupon.php                     # Coupon validation
│   ├── search.php                     # Product search
│   ├── review.php                     # Product reviews
│   └── wishlist.php                   # Wishlist AJAX
└── README.md                          # This file
```

---

## 🧪 Testing Guide

### Test Customer Flow
1. **Browse Products**
   - Navigate to `/customer/pages/listing.php`
   - View product catalog

2. **Add to Cart (AJAX)**
   ```javascript
   const btn = document.querySelector('.add-cart');
   btn.click(); // Triggers AJAX
   ```

3. **View Cart**
   - Navigate to `/customer/pages/cart.php`
   - See items added via AJAX

4. **Apply Coupon**
   - Use code: `SAVE20` (20% off)
   - Or: `FLAT500` (₹500 off)

5. **Checkout**
   - Enter shipping details
   - Select payment method
   - Place order

6. **Admin Dashboard**
   - Login: `/admin/index.php`
   - View revenue metrics
   - Manage products

---

## 💳 Payment Gateway (Razorpay)

### Current Status
- ✅ Payment method selection in checkout
- ✅ Database schema ready
- 🔄 Integration in progress

### Integration Steps
1. Get Razorpay API keys from dashboard
2. Add keys to `admin/includes/bootstrap.php`
3. Update `customer/pages/checkout.php` with Razorpay button
4. Create payment processing endpoint

---

## 🔐 Security Features

- ✅ Bcrypt password hashing (cost=10)
- ✅ CSRF token protection on all forms
- ✅ Session ID regeneration on login
- ✅ SQL injection prevention (prepared statements)
- ✅ Role-based access control
- ✅ Input sanitization with `clean_text()`
- ✅ Email uniqueness enforcement
- ✅ Session destruction on logout

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Cart not updating | Check CSRF token, verify JavaScript is enabled |
| Login fails | Ensure user exists, check password hash |
| 403 Forbidden on admin | Login as admin user, check role in database |
| Images not displaying | Verify `uploads/products` directory exists and is writable |
| Database connection error | Check `includes/bootstrap.php` credentials |

---

## 📝 Database Tables

- `users` - Customer accounts
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Customer orders
- `order_items` - Items in orders
- `cart_items` - Shopping cart (session-based)
- `coupons` - Discount codes
- `coupon_usage` - Coupon redemption tracking
- `reviews` - Product reviews
- `wishlist` - Saved favorites

---

## 🚀 Deployment

### cPanel Hosting
1. Upload files to `public_html/ecommerce`
2. Import database using phpMyAdmin
3. Update database credentials
4. Set file permissions (644 for files, 755 for directories)
5. Ensure `uploads/products` is writable

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name shop.example.com;
    root /path/to/ecommerce;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

---

## 📞 Support

For detailed information, refer to:
- `QUICK_START.md` - Quick testing guide
- `SETUP_GUIDE.md` - Comprehensive setup
- `IMPLEMENTATION_SUMMARY.md` - Features overview

---

**Last Updated**: May 5, 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready (Razorpay integration pending)
