<?php
/*
   Program Name: MiguelResponse.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Assignment:   Module 7.2
   Date:         2026-09-08
   Description:  Receives the seven fields posted from MiguelForm.html,
                 validates each one by data type (string, email, integer,
                 float, date, list choice, yes/no), and shows either a list
                 of errors or a table of the submitted values.
   AI Use:       Claude Code (Anthropic) was used to help check for bugs
                 and to write and improve comments.
*/

// Read each field. "?? ''" avoids a warning if a field was not sent, and
// trim() removes extra spaces so "   " counts as empty.
$name     = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$age       = trim($_POST['age'] ?? '');
$rate      = trim($_POST['rate'] ?? '');
$startDate = trim($_POST['startDate'] ?? '');
$dept      = trim($_POST['dept'] ?? '');
$remote    = trim($_POST['remote'] ?? '');

$errors = array();

// String
if ($name == '') {
    $errors[] = "Full Name is required.";
}

// String (email format)
if ($email == '') {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email is not a valid address.";
}

// Integer
if ($age == '') {
    $errors[] = "Age is required.";
} elseif (!ctype_digit($age)) {
    $errors[] = "Age must be a whole number.";
} elseif ($age < 16 || $age > 99) {
    $errors[] = "Age must be between 16 and 99.";
}

// Float
if ($rate == '') {
    $errors[] = "Hourly Rate is required.";
} elseif (!is_numeric($rate)) {
    $errors[] = "Hourly Rate must be a number.";
} elseif ($rate <= 0) {
    $errors[] = "Hourly Rate must be greater than zero.";
}

// Date
if ($startDate == '') {
    $errors[] = "Start Date is required.";
} elseif (!strtotime($startDate)) {
    $errors[] = "Start Date is not a valid date.";
}

// String from a list
if ($dept == '') {
    $errors[] = "Department is required.";
} elseif (!in_array($dept, array("Engineering", "Marketing", "Operations"), true)) {
    $errors[] = "Department is not a valid choice.";
}

// Boolean
if ($remote == '') {
    $errors[] = "Works Remotely is required.";
} elseif (!in_array($remote, array("Yes", "No"), true)) {
    $errors[] = "Works Remotely must be Yes or No.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Miguel Response</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; max-width: 500px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #eee; width: 40%; }
    .error { color: red; }
  </style>
</head>
<body>

<?php if (count($errors) > 0) { ?>

  <h1>Error</h1>
  <p>Please correct the following:</p>
  <ul>
    <?php foreach ($errors as $error) { ?>
      <li class="error"><?php echo htmlspecialchars($error); ?></li>
    <?php } ?>
  </ul>

<?php } else { ?>

  <h1>Registration Received</h1>
  <table>
    <tr><th>Full Name</th><td><?php echo htmlspecialchars($name); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($email); ?></td></tr>
    <tr><th>Age</th><td><?php echo (int)$age; ?></td></tr>
    <tr><th>Hourly Rate</th><td>$<?php echo number_format((float)$rate, 2); ?></td></tr>
    <tr><th>Start Date</th><td><?php echo date("F j, Y", strtotime($startDate)); ?></td></tr>
    <tr><th>Department</th><td><?php echo htmlspecialchars($dept); ?></td></tr>
    <tr><th>Works Remotely</th><td><?php echo htmlspecialchars($remote); ?></td></tr>
  </table>

<?php } ?>

<p><a href="MiguelForm.html">Back to the form</a></p>

</body>
</html>