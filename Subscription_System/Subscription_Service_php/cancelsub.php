<?php
//for connection to the database put your sql server password in the ('').
$conn = new mysqli('localhost', 'root', '', 'subscription_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = $_POST['id'];

  // Update the subscription status to canceled
  $stmt = $conn->prepare("UPDATE subscriptions SET status = 'canceled' WHERE subscription_id = ?");
  $stmt->bind_param("i", $id);

  // Record audit log
$audit = $conn->prepare("INSERT INTO subscription_audit (subscription_id, action, note)
                         VALUES (?, 'Canceled', 'Subscription was canceled by user')");
$audit->bind_param("i", $id);
$audit->execute();
$audit->close();




  if ($stmt->execute()) {
    echo "<p style='text-align:center; color:green;'> Subscription canceled successfully!</p>";
  } else {
    echo "<p style='text-align:center; color:red;'> Error canceling subscription: " . $conn->error . "</p>";
  }

  $stmt->close();
  $conn->close();
}


header("refresh:2; url=subscriptions.php");
exit;
?>
