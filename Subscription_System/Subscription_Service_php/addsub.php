<?php
//for connection to the database put your sql server password in the ('').
$conn = new mysqli('localhost', 'root', '', 'subscription_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $plan_id = $_POST['plan_id'];
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  // Check that fields are not empty
  if ($name == "" || $email == "" || $plan_id == "" || $start_date == "" || $end_date == "") {
    $message = "Please fill in all fields.";
  } else {
    // Check if user already exists
    $checkUser = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
      // If user exists, get ID
      $checkUser->bind_result($user_id);
      $checkUser->fetch();
    } else {
      // If new user, insert
      $insertUser = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
      $insertUser->bind_param("ss", $name, $email);
      $insertUser->execute();
      $user_id = $insertUser->insert_id;
      $insertUser->close();
    }
    $checkUser->close();

    // Add subscription
$insertSub = $conn->prepare("INSERT INTO subscriptions (user_id, plan_id, start_date, end_date, status)
                             VALUES (?, ?, ?, ?, 'active')");
$insertSub->bind_param("iiss", $user_id, $plan_id, $start_date, $end_date);

if ($insertSub->execute()) {
  $subscription_id = $insertSub->insert_id;

  // Get plan price
  $priceQuery = $conn->prepare("SELECT price FROM plans WHERE plan_id = ?");
  $priceQuery->bind_param("i", $plan_id);
  $priceQuery->execute();
  $priceResult = $priceQuery->get_result();
  $priceRow = $priceResult->fetch_assoc();
  $price = $priceRow['price'] ?? 0;
  $priceQuery->close();

  // Insert initial payment record
  $paymentStmt = $conn->prepare("INSERT INTO payments (subscription_id, amount, payment_date, method)
                                 VALUES (?, ?, CURDATE(), 'Card')");
  $paymentStmt->bind_param("id", $subscription_id, $price);
  $paymentStmt->execute();
  $paymentStmt->close();

  $message = "Subscription added successfully! Payment of $$price recorded.";
  // Record in audit log
$audit = $conn->prepare("INSERT INTO subscription_audit (subscription_id, action, note)
                         VALUES (?, 'Created', CONCAT('Subscription created with plan ID ', ?))");
$audit->bind_param("ii", $subscription_id, $plan_id);
$audit->execute();
$audit->close();

} else {
  $message = "Error adding subscription: " . $conn->error;
}
$insertSub->close();

  }
}

// Fetch plan options
$plans = $conn->query("SELECT plan_id, plan_name FROM plans");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Subscription</title>
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
  <h2>Add New Subscription</h2>
  <form method="POST">
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Plan:</label>
    <select name="plan_id" required>
      <option value="">-- Select a Plan --</option>
      <?php while ($plan = $plans->fetch_assoc()): ?>
        <option value="<?= $plan['plan_id'] ?>"><?= htmlspecialchars($plan['plan_name']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Start Date:</label>
    <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>">

    <label>End Date:</label>
    <input type="date" name="end_date" required value="<?= date('Y-m-d', strtotime('+1 month')) ?>">

    <button type="submit">Add Subscription</button>
  </form>

  <div class="msg"><?= $message ?></div>
  <div style="text-align:center;">
    <a href="index1.php">Back to Home</a> | 
    <a href="subscriptions.php">View Subscriptions</a>
  </div>
</body>
</html>
