<?php
//for connection to the database put your sql server password in the ('').
$conn = new mysqli('localhost', 'root', '', 'subscription_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT a.audit_id, a.subscription_id, u.name, p.plan_id, a.action, a.action_date, a.note
        FROM subscription_audit a
        JOIN subscriptions s ON a.subscription_id = s.subscription_id
        JOIN users u ON s.user_id = u.user_id
        JOIN plans p ON s.plan_id = p.plan_id
        ORDER BY a.action_date DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Subscription Audit Log</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: lightgray; }
    table { width: 85%; margin: 20px auto; border-collapse: collapse; background: white; }
    th, td { border: 1px solid gray; padding: 10px; text-align: center; }
    th { background-color: blue; color: white; }
    h2 { text-align: center; }
  </style>
</head>
<body>
  <h2>Subscription Audit Log</h2>
  <table>
    <tr>
      <th>Audit ID</th>
      <th>User</th>
      <th>Plan</th>
      <th>Action</th>
      <th>Date</th>
      <th>Notes</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['audit_id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['plan_id']) ?></td>
          <td><?= htmlspecialchars($row['action']) ?></td>
          <td><?= $row['action_date'] ?></td>
          <td><?= htmlspecialchars($row['note']) ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6">No audit records found.</td></tr>
    <?php endif; ?>
  </table>
  <div style="text-align:center;">
    <a href="index1.php">Back to Home</a>
  </div>
</body>
</html>
