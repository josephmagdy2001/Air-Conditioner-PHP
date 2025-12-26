 <img width="1788" height="3495" alt="image" src="https://github.com/user-attachments/assets/2010ad6c-d007-42b8-aafd-dbe793f19c34" /> 

 
❄️ Elite Cool - Digital Store & Management System
A high-performance sales and management platform for air conditioning units, featuring a futuristic 3D user experience and a robust backend for inventory and sales tracking.

🛠 Tech Stack
Backend: PHP 8.1 (PDO & Session Management).

Database: MySQL.

UI/UX: Tailwind CSS with Glassmorphism effects.

3D Graphics: Three.js (Interactive backgrounds & shapes).

Frontend Logic: JavaScript (ES6+, Fetch API, AJAX).

👥 System Roles & Permissions
👤 User (Customer)
Interactive UI: Browse products in a 3D-animated environment.

Smart Purchase: Secure checkout system integrated with InstaPay payment flow.

Technical Consultation: A dedicated modal form to send technical inquiries directly via email.

Adaptive Theme: Instant switching between Dark and Light modes for optimal comfort.

👨‍💼 Administrator (Admin)
Inventory Control: Full CRUD (Create, Read, Update, Delete) operations for products.

Stock Tracking: Automatic inventory deduction upon every successful purchase.

Sales Reports: A detailed log of all transactions (Customer Name, Product, Price, and Timestamp).

📂 Project Structure
index_user.php: The main landing page and product storefront.

product_details.php: Dynamic page for technical specifications.

buy.php: The core engine for processing orders and updating stock levels.

send_email.php: Handler for processing consultation forms.

db.php: Centralized secure database connection.

🚀 Quick Installation
Database: Import the database.sql file into your MySQL server.

Configuration: Update the credentials in db.php.

Mail Setup: Configure your receiver email address in send_email.php.

Launch: Deploy to a PHP-enabled server (XAMPP, WAMP, or live hosting).

🌟 Key Design Features
Micro-animations: Floating 3D elements and smooth transition effects.

Responsive Design: Fully optimized for Mobile, Tablet, and Desktop.

Access Control: Purchase logic is restricted to logged-in users only.
