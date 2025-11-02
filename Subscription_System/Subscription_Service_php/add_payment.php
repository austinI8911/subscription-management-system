<?php
//for connection to the database put your sql server password in the ('').
$conn = new mysqli('localhost', 'root', '', 'subscription_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$message = "";

// Get subscriptions for dropdown
$subscriptions = $conn->query("SELECT s.subscription_id, u.name, s.plan_id 
                               FROM subscriptions s 
                               JOIN users u ON s.user_id = u.user_id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $subscription_id = $_POST['subscription_id'];
  $amount = $_POST['amount'];
  $payment_date = $_POST['payment_date'];
  $method = $_POST['method'];

  $stmt = $conn->prepare("INSERT INTO payments (subscription_id, amount, payment_date, method)
                           VALUES (?, ?, ?, ?)");
  $stmt->bind_param("idss", $subscription_id, $amount, $payment_date, $method);

  if ($stmt->execute()) {
    $message = "Payment recorded successfully!";
  } else {
    $message = "Error adding payment: " . $conn->error;
  }

  $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Payment</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: lightgray; }
    form { width: 400px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 10px gray; }
    input, select { width: 100%; padding: 8px; margin: 8px 0; }
    button { background: blue; color: white; border: none; padding: 10px; cursor: pointer; width: 100%; }
    button:hover { background: darkblue; }
    h2 { text-align: center; }
    .msg { text-align: center; margin-bottom: 10px; font-weight: bold; }
  </style>
</head>
<body>
  <h2>Add Payment</h2>
  <form method="POST">
    <label>Subscription:</label>
    <select name="subscription_id" required>
      <option value="">-- Select Subscription --</option>
      <?php while ($sub = $subscriptions->fetch_assoc()): ?>
        <option value="<?= $sub['subscription_id'] ?>">
          <?= htmlspecialchars($sub['name']) ?> (<?= htmlspecialchars($sub['plan_id']) ?>)
        </option>
      <?php endwhile; ?>
    </select>

    <label>Amount:</label>
    <input type="number" name="amount" step="0.01" required>

    <label>Payment Date:</label>
    <input type="date" name="payment_date" required value="<?= date('Y-m-d') ?>">

    <label>Payment Method:</label>
    <select name="method" required>
      <option value="Card">Card</option>
      <option value="Cash">Cash</option>
      <option value="PayPal">PayPal</option>
    </select>

    <button type="submit">Add Payment</button>
  </form>

  <div class="msg"><?= $message ?></div>
  <div style="text-align:center;">
    <a href="payments.php">Back to Payments</a>
  </div>
</body>
</html>
