
create database subscription_system;
use subscription_system;


-- Users table
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--  Plans table 
CREATE TABLE plans (
  plan_id INT AUTO_INCREMENT PRIMARY KEY,
  plan_name VARCHAR(100) NOT NULL,
  price DECIMAL(8,2) NOT NULL,
  billing_cycle ENUM('monthly','yearly') NOT NULL DEFAULT 'monthly',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Plan names
INSERT INTO plans (plan_name, price, billing_cycle) VALUES
('Basic', 5.00, 'monthly'),
('Standard', 10.00, 'monthly'),
('Premium', 20.00, 'monthly');


--  Subscriptions table
CREATE TABLE subscriptions (
  subscription_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  plan_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('active','expired','canceled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (plan_id) REFERENCES plans(plan_id) ON DELETE RESTRICT,
  INDEX idx_user_status (user_id, status),
  INDEX idx_end_date (end_date)
);

--  Payments table
CREATE TABLE payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  subscription_id INT NOT NULL,
  amount DECIMAL(8,2) NOT NULL,
  payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  method VARCHAR(50),
  transaction_ref VARCHAR(100),
  FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE CASCADE,
  INDEX idx_payment_subscription (subscription_id)
);

-- Audit table for subscription changes 
CREATE TABLE subscription_audit (
  audit_id INT AUTO_INCREMENT PRIMARY KEY,
  subscription_id INT NOT NULL,
  action VARCHAR(50) NOT NULL, -- e.g., 'created', 'updated', 'canceled'
  action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  note TEXT,
  FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE CASCADE
);
