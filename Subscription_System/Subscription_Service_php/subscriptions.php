<?php
//for connection to the database put your sql server password in the ('').
$conn = new mysqli('localhost', 'root', '', 'subscription_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT s.subscription_id, u.name AS user_name, u.email, 
               p.plan_name, s.start_date, s.end_date, s.status
        FROM subscriptions s
        JOIN users u ON s.user_id = u.user_id
        JOIN plans p ON s.plan_id = p.plan_id
        ORDER BY s.subscription_id ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>All Subscriptions</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: lightgray; }
    h2 { text-align: center; }
    table { width: 90%; margin: 20px auto; border-collapse: collapse; background-color: white; }
    th, td { border: 1px solid gray; padding: 10px; text-align: center; }
    th { background-color: blue; color: white; }
    .btn { display: inline-block; padding: 6px 10px; border: none; cursor: pointer; color: white; }
    .btn-edit { background-color: green; }
    .btn-view { background-color: steelblue; }
    .btn-cancel { background-color: red; }
    .actions form { display: inline; margin: 0 4px; }
  </style>
</head>
<body>
  <h2>All Subscriptions</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Email</th>
      <th>Plan</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Status</th>
      <th>Action</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['subscription_id']) ?></td>
          <td><?= htmlspecialchars($row['user_name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['plan_name']) ?></td>
          <td><?= htmlspecialchars($row['start_date']) ?></td>
          <td><?= htmlspecialchars($row['end_date']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td class="actions">
            <!-- Edit -->
            <form action="updatesub.php" method="GET">
              <input type="hidden" name="id" value="<?= htmlspecialchars($row['subscription_id']) ?>">
              <button type="submit" class="btn btn-edit">Edit</button>
            </form>

            <!-- View Payments -->
            <form action="payments.php" method="GET">
              <input type="hidden" name="id" value="<?= htmlspecialchars($row['subscription_id']) ?>">
              <button type="submit" class="btn btn-view">View Payments</button>
            </form>

            <!-- Cancel -->
            <?php if ($row['status'] !== 'canceled'): ?>
              <form action="cancelsub.php" method="POST" onsubmit="return confirm('Cancel this subscription?');">
                <input type="hidden" name="id" value="<?= htmlspecialchars($row['subscription_id']) ?>">
                <button type="submit" class="btn btn-cancel">Cancel</button>
              </form>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="8">No subscriptions found.</td></tr>
    <?php endif; ?>
  </table>

  <div style="text-align:center;">
    <a href="index1.php"> Back to Home</a>
    <a href="audit_log.php" style="color:blue; text-decoration:none; font-weight:bold;">View Audit Log</a>
</div>
  </div>
</body>
</html>
