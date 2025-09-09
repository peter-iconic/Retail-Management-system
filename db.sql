CREATE TABLE
    Suppliers (
        supplier_id INT AUTO_INCREMENT PRIMARY KEY,
        supplier_name VARCHAR(100),
        contact_name VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT
    );

CREATE TABLE
    Products (
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        description TEXT,
        price DECIMAL(10, 2),
        stock_quantity INT,
        reorder_level INT,
        supplier_id INT,
        FOREIGN KEY (supplier_id) REFERENCES Suppliers (supplier_id)
    );

CREATE TABLE
    Customers (
        customer_id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100)
    );

CREATE TABLE
    Sales (
        sale_id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        total_amount DECIMAL(10, 2),
        FOREIGN KEY (customer_id) REFERENCES Customers (customer_id)
    );

CREATE TABLE
    Sale_Items (
        sale_item_id INT AUTO_INCREMENT PRIMARY KEY,
        sale_id INT,
        product_id INT,
        quantity INT,
        subtotal DECIMAL(10, 2),
        FOREIGN KEY (sale_id) REFERENCES Sales (sale_id),
        FOREIGN KEY (product_id) REFERENCES Products (product_id)
    );
    CREATE TABLE Users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100),
  role ENUM('admin','cashier') NOT NULL DEFAULT 'cashier',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
