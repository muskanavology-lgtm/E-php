# FreshCart — PHP (OOP) E-Commerce Site (Blinkit-style)

A grocery e-commerce website built in **core PHP using OOP** + **MySQL**.
Customers can browse categories, search products, add to cart, and checkout.
Admin can log in and add/edit/delete products & categories, and manage orders.

---

## 🗂️ Project Structure

```
ecommerce-site/
├── config/Database.php        -> PDO database connection (Singleton class)
├── classes/
│   ├── Product.php             -> Product model (list, search, category filter, CRUD)
│   ├── Category.php            -> Category model (CRUD)
│   ├── Cart.php                -> Session-based shopping cart
│   ├── Order.php                -> Places orders, saves order items, reduces stock
│   └── Admin.php               -> Admin login/session handling
├── includes/
│   ├── header.php               -> Top bar, search box, category strip (customer pages)
│   ├── footer.php
│   └── functions.php            -> Helper functions (price format, image path, uploads)
├── admin/                       -> Admin panel (protected, needs login)
│   ├── login.php / logout.php
│   ├── dashboard.php
│   ├── products.php / add_product.php / edit_product.php / delete_product.php
│   ├── categories.php / delete_category.php
│   ├── orders.php / view_order.php
│   └── includes/admin_header.php / admin_footer.php
├── assets/
│   ├── css/style.css            -> Customer site styling (Blinkit-style)
│   ├── css/admin.css            -> Admin panel styling
│   ├── js/script.js             -> AJAX add-to-cart, cart quantity buttons
│   └── img/placeholder.svg
├── uploads/products/             -> Product images uploaded from admin panel go here
├── uploads/categories/           -> Category images uploaded from admin panel go here
├── database/schema.sql           -> Run this once to create the database & tables
├── setup.php                     -> Run this once in browser to create default admin
├── index.php, category.php, product.php, search.php, cart.php, checkout.php
├── add_to_cart.php, update_cart.php   -> AJAX endpoints used by script.js
```

---

## 🚀 How to Run (XAMPP / WAMP / Laragon)

1. **Copy the folder** `ecommerce-site` into your server's web root:
   - XAMPP: `C:\xampp\htdocs\ecommerce-site`
   - WAMP: `C:\wamp64\www\ecommerce-site`
   - Laragon: `C:\laragon\www\ecommerce-site`

2. **Start Apache and MySQL** from your XAMPP/WAMP control panel.

3. **Create the database:**
   - Open `phpMyAdmin` (usually `http://localhost/phpmyadmin`).
   - Click **Import**, choose the file `database/schema.sql`, and click **Go**.
   - This creates the database `blinkit_clone` with all tables and a few sample categories.

4. **Check your DB login in `config/Database.php`:**
   - By default it uses `host = localhost`, `user = root`, `password = ""` (XAMPP default).
   - If your MySQL has a different username/password, edit those 4 lines in `config/Database.php`.

5. **Run the one-time setup script:**
   - Open `http://localhost/ecommerce-site/setup.php` in your browser.
   - This creates the default admin account:
     - **Username:** `admin`
     - **Password:** `admin123`
   - You can delete `setup.php` after this (optional).

6. **Open the website:**
   - Customer site: `http://localhost/ecommerce-site/index.php`
   - Admin panel: `http://localhost/ecommerce-site/admin/login.php`

7. **Add some products:**
   - Log in to the admin panel → go to **Products** → **Add New Product**.
   - Choose a category, enter price/stock, upload an image (optional), and save.
   - The product will now show up on the home page, its category page, and in search.

---

## 🛒 How the Cart Works

- The cart uses **PHP Sessions** — no login is required for customers to shop.
- "Add to Cart" buttons use **AJAX (fetch)** so the page does not reload.
- On `cart.php`, the `+` / `-` / **Remove** buttons update the cart instantly via AJAX.
- On `checkout.php`, the customer enters name/phone/address and places the order.
  - This saves the order in the `orders` + `order_items` tables, reduces product stock,
    and empties the cart.

---

## 🔑 Admin Panel Features

- **Dashboard** — quick stats (products, categories, orders, revenue).
- **Products** — add / edit / delete, with image upload.
- **Categories** — add / delete, shown as the top category bar on the site.
- **Orders** — view customer orders and update status
  (Placed → Confirmed → Out for Delivery → Delivered / Cancelled).

⚠️ **Please change the default admin password** after your first login
(there's no "change password" screen yet — you can update it directly in
phpMyAdmin using `UPDATE admins SET password = '<new_bcrypt_hash>' WHERE username='admin';`,
or ask me to add a "change password" page for you).

---

## 🧠 OOP Concepts Used

- **Classes & Objects:** `Product`, `Category`, `Cart`, `Order`, `Admin`, `Database`
- **Encapsulation:** DB connection details are private inside `Database`
- **Singleton Pattern:** `Database::getInstance()` — only one DB connection is created
- **Reusability:** Same `Product`/`Category` classes are used by both the customer site
  and the admin panel

---

## 📌 Notes

- This project was written carefully by hand but **could not be executed/tested inside
  this sandbox** (no PHP available here). Please test it on your local XAMPP/WAMP setup.
  If you hit any error, copy the exact error message and I will fix it immediately.
- Prices are shown in ₹ (Indian Rupees) — change `formatPrice()` in `includes/functions.php`
  if you want a different currency symbol.
