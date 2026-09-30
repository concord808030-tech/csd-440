<?php
/* ============================================================================
   Program Name: MiguelForms.php
   Author:       Miguel Fernandez
   Course:       CSD440 - PHP / Apache
   Assignment:   Module 9 - Query and Add Records with Forms
   Date:         09/27/2026

   Description:  Displays a form for adding a new pilot. When the form is
                 submitted, the page checks that every field was filled in,
                 then inserts the record into the "miguel_fleet_roster" table
                 using a MySQLi prepared statement.

   Inputs:       Form fields sent with POST: call_sign, given_name, faction,
                 rank_title, ship_class, missions_flown, combat_rating,
                 active_duty, enlisted_date.
   Outputs:      The form, plus a success message or an error message.
   ============================================================================ */

// ---------------------------------------------------------------------------
// Database connection constants
// ---------------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'student1');
define('DB_PASS', 'pass');
define('DB_NAME', 'baseball_01');
define('TBL_NAME', 'miguel_fleet_roster');

// Show errors as messages instead of exceptions (PHP 8.1+).
mysqli_report(MYSQLI_REPORT_OFF);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add a Pilot - Miguel Fernandez</title>
    <style>
        body  { font-family: Arial, Helvetica, sans-serif; margin: 40px; }
        h1    { border-bottom: 2px solid #333; padding-bottom: 6px; }
        label { display: inline-block; width: 140px; }
        p     { margin: 8px 0; }
        .ok   { color: #14601f; font-weight: bold; }
        .fail { color: #a00000; font-weight: bold; }
    </style>
</head>
<body>

<h1>Add a Pilot</h1>
<p><a href="MiguelIndex.php">Back to Index</a></p>

<?php
// ---------------------------------------------------------------------------
// Process the form only when it has been submitted.
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Step 1: Read the form values.
    $callSign  = trim($_POST['call_sign']);
    $givenName = trim($_POST['given_name']);
    $faction   = trim($_POST['faction']);
    $rankTitle = trim($_POST['rank_title']);
    $shipClass = trim($_POST['ship_class']);
    $missions  = (int)$_POST['missions_flown'];
    $rating    = (float)$_POST['combat_rating'];
    $active    = (int)$_POST['active_duty'];
    $enlisted  = $_POST['enlisted_date'];

    // Step 2: Make sure the text fields and date are not empty.
    if ($callSign == "" || $givenName == "" || $faction == "" ||
        $rankTitle == "" || $shipClass == "" || $enlisted == "") {
        echo "<p class='fail'>Please fill in every field.</p>";
    } else {

        // Step 3: Connect to the database.
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            echo "<p class='fail'>Connection failed: " .
                 htmlspecialchars($conn->connect_error) . "</p>";
        } else {

            // Step 4: Prepare and run the INSERT statement.
            $sql = "INSERT INTO " . TBL_NAME . "
                        (call_sign, given_name, faction, rank_title, ship_class,
                         missions_flown, combat_rating, active_duty, enlisted_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                echo "<p class='fail'>Error: " .
                     htmlspecialchars($conn->error) . "</p>";
            } else {
                // s = string, i = integer, d = decimal
                $stmt->bind_param("sssssidis",
                    $callSign, $givenName, $faction, $rankTitle, $shipClass,
                    $missions, $rating, $active, $enlisted);

                if ($stmt->execute()) {
                    echo "<p class='ok'>Pilot " . htmlspecialchars($callSign) .
                         " was added.</p>";
                } else {
                    // A duplicate call sign will show up here as an error.
                    echo "<p class='fail'>Could not add pilot: " .
                         htmlspecialchars($stmt->error) . "</p>";
                }
                $stmt->close();
            }

            // Step 5: Close the connection.
            $conn->close();
        }
    }
}
?>

<!-- Add pilot form -->
<form method="post" action="MiguelForms.php">
    <p><label for="call_sign">Call Sign:</label>
       <input type="text" id="call_sign" name="call_sign" maxlength="40" required></p>

    <p><label for="given_name">Name:</label>
       <input type="text" id="given_name" name="given_name" maxlength="60" required></p>

    <p><label for="faction">Faction:</label>
       <input type="text" id="faction" name="faction" maxlength="30" required></p>

    <p><label for="rank_title">Rank:</label>
       <input type="text" id="rank_title" name="rank_title" maxlength="30" required></p>

    <p><label for="ship_class">Ship Class:</label>
       <input type="text" id="ship_class" name="ship_class" maxlength="30" required></p>

    <p><label for="missions_flown">Missions Flown:</label>
       <input type="number" id="missions_flown" name="missions_flown" min="0" value="0" required></p>

    <p><label for="combat_rating">Combat Rating:</label>
       <input type="number" id="combat_rating" name="combat_rating" min="0" max="10" step="0.1" required></p>

    <p><label for="active_duty">Status:</label>
       <select id="active_duty" name="active_duty">
           <option value="1">Active</option>
           <option value="0">Reserve</option>
       </select></p>

    <p><label for="enlisted_date">Enlisted Date:</label>
       <input type="date" id="enlisted_date" name="enlisted_date" required></p>

    <p><input type="submit" value="Add Pilot"></p>
</form>

</body>
</html>