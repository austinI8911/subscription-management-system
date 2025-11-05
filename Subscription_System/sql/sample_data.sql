use subscription_system;

INSERT INTO users (name, email) VALUES
('Stephen Curry', 'steph.curry@example.com'),
('Klay Thompson', 'klay.thompson@example.com'),
('James Smith', 'james.smith@example.com'),
('Emily Davis', 'emily.davis@example.com');

INSERT INTO subscriptions (user_id, plan_id, start_date, end_date, status) VALUES
(1, 3, '2025-01-01', '2025-02-01', 'active'),   -- Stephen on Premium
(2, 2, '2025-01-10', '2025-02-10', 'active'),   -- Klay on Standard
(3, 1, '2025-01-03', '2025-02-03', 'canceled'), -- James on Basic
(4, 3, '2025-01-08', '2025-02-08', 'active');   -- Emily on Premium

INSERT INTO payments (subscription_id, amount, method, payment_date) VALUES
(1, 20.00, 'Card', '2025-01-01'),
(2, 15.00, 'PayPal', '2025-01-10'),
(3, 10.00, 'Cash', '2025-01-03'),
(4, 20.00, 'Card', '2025-01-08');

INSERT INTO subscription_audit (subscription_id, action, note) VALUES
(1, 'created', 'Stephen Curry subscribed to Premium'),
(2, 'created', 'Klay Thompson subscribed to Standard'),
(3, 'canceled', 'James Smith canceled Basic plan'),
(4, 'created', 'Emily Davis subscribed to Premium');
