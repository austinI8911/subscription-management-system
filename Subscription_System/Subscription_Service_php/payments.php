<?php
//for connection to the database put your sql server password in the ('').
$conn = new mysqli('localhost', 'root', '', 'subscription_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT p.payment_id, u.name, s.plan_id, p.amount, p.payment_date, p.method
        FROM payments p
        JOIN subscriptions s ON p.subscription_id = s.subscription_id
        JOIN users u ON s.user_id = u.user_id
        ORDER BY p.payment_date DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Payments</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: lightgray; }
    h2 { text-align: center; }
    table { width: 80%; margin: 20px auto; border-collapse: collapse; background-color: white; }
    th, td { border: 1px solid gray; padding: 10px; text-align: center; }
    th { background-color: blue; color: white; }
  </style>
</head>
<body>
  <h2>Payment Transactions</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Plan</th>
      <th>Amount</th>
      <th>Payment Date</th>
      <th>Method</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['payment_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['plan_id']}</td>
                <td>\${$row['amount']}</td>
                <td>{$row['payment_date']}</td>
                <td>{$row['method']}</td>
              </tr>";
      }
    } else {
      echo "<tr><td colspan='6'>No payments found.</td></tr>";
    }
    ?>
  </table>
  <div style="text-align:center;">
    <a href="index1.php">Back to Home</a> | 
    <a href="add_payment.php">Add Payment</a>
  </div>
</body>
</html>
