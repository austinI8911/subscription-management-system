<?php
//for connection to the database put your sql server password in the ('').
$conn = new mysqli('localhost', 'root', '', 'subscription_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$id = $_GET['id'] ?? null;

if ($id) {
  // Fetch subscription info with current plan
  $stmt = $conn->prepare("SELECT s.subscription_id, u.name, u.email, s.plan_id, p.plan_name, s.start_date, s.end_date, s.status
                          FROM subscriptions s
                          JOIN users u ON s.user_id = u.user_id
                          JOIN plans p ON s.plan_id = p.plan_id
                          WHERE s.subscription_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $subscription = $result->fetch_assoc();
  $stmt->close();

  // Fetch all plan options for dropdown
  $plans = $conn->query("SELECT plan_id, plan_name FROM plans");
} else {
  die("Invalid subscription ID.");
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $plan_id = $_POST['plan_id'];
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];
  $status = $_POST['status'];
  $id = $_POST['id'];

  $update = $conn->prepare("UPDATE subscriptions 
                            SET plan_id = ?, start_date = ?, end_date = ?, status = ? 
                            WHERE subscription_id = ?");
  $update->bind_param("isssi", $plan_id, $start_date, $end_date, $status, $id);

  if ($update->execute()) {
    $message = "Subscription updated successfully!";
    // Record audit log
$audit = $conn->prepare("INSERT INTO subscription_audit (subscription_id, action, note)
                         VALUES (?, 'Updated', 'Subscription details modified')");
$audit->bind_param("i", $id);
$audit->execute();
$audit->close();

  } else {
    $message = "Error updating subscription: " . $conn->error;
  }

  $update->close();

  // Redirect back to subscriptions page after 2 seconds
  header("refresh:2; url=subscriptions.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Update Subscription</title>
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
  <h2>Update Subscription</h2>
  <?php if ($subscription): ?>
  <form method="POST">
    <input type="hidden" name="id" value="<?= $subscription['subscription_id'] ?>">

    <label>Name:</label>
    <input type="text" value="<?= htmlspecialchars($subscription['name']) ?>" readonly>

    <label>Email:</label>
    <input type="email" value="<?= htmlspecialchars($subscription['email']) ?>" readonly>

    <label>Plan:</label>
    <select name="plan_id" required>
      <?php while ($plan = $plans->fetch_assoc()): ?>
        <option value="<?= $plan['plan_id'] ?>" <?= ($plan['plan_id'] == $subscription['plan_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($plan['plan_name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Start Date:</label>
    <input type="date" name="start_date" required value="<?= $subscription['start_date'] ?>">

    <label>End Date:</label>
    <input type="date" name="end_date" required value="<?= $subscription['end_date'] ?>">

    <label>Status:</label>
    <select name="status" required>
      <option value="active" <?= $subscription['status'] == 'active' ? 'selected' : '' ?>>Active</option>
      <option value="expired" <?= $subscription['status'] == 'expired' ? 'selected' : '' ?>>Expired</option>
      <option value="canceled" <?= $subscription['status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
    </select>

    <button type="submit">Update Subscription</button>
  </form>
  <?php endif; ?>

  <div class="msg"><?= $message ?></div>
  <div style="text-align:center;">
    <a href="subscriptions.php">⬅ Back to Subscriptions</a>
  </div>
</body>
</html>
