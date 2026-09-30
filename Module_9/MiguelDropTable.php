<?php
/* ============================================================================
   Program Name: MiguelDropTable.php
   Author:       Miguel Fernandez
   Course:       CSD440 - PHP / Apache
   Assignment:   Module 8 - Create and Populate a Database Table
   Date:         09/20/2026

   Description:  Uses MySQLi to remove the "miguel_fleet_roster" table from
                 the baseball_01 database. The script first checks whether the
                 table exists so the user receives a meaningful message either
                 way, then issues DROP TABLE IF EXISTS.

   Inputs:       None.
   Outputs:      An HTML page confirming the table was dropped, reporting that
                 it did not exist, or displaying the MySQL error.

   Warning:      Dropping the table permanently deletes both its structure and
                 all of its rows. Re-run MiguelCreateTable.php and
                 MiguelPopulateTable.php to rebuild it.
   ============================================================================ */

// ---------------------------------------------------------------------------
// Database connection constants
// ---------------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'student1');
define('DB_PASS', 'pass');
define('DB_NAME', 'baseball_01');
define('TBL_NAME', 'miguel_fleet_roster');

// PHP 8.1+ (XAMPP) makes MySQLi throw exceptions by default. Turning that
// off lets the if/else checks below display friendly error messages.
mysqli_report(MYSQLI_REPORT_OFF);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop Table - Miguel Fernandez</title>
    <style>
        body  { font-family: Arial, Helvetica, sans-serif; margin: 40px; }
        h1    { border-bottom: 2px solid #333; padding-bottom: 6px; }
        .ok   { color: #14601f; font-weight: bold; }
        .fail { color: #a00000; font-weight: bold; }
        .warn { color: #8a6d00; }
        code  { background: #f2f2f2; padding: 2px 4px; }
    </style>
</head>
<body>

<h1>Drop Table</h1>

<?php
// ---------------------------------------------------------------------------
// Step 1: Connect to the database.
// ---------------------------------------------------------------------------
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo "<p class='fail'>Connection failed: " .
         htmlspecialchars($conn->connect_error) . "</p>";
    echo "</body></html>";
    exit();
}

echo "<p class='ok'>Connected to the " . DB_NAME . " database.</p>";

// ---------------------------------------------------------------------------
// Step 2: Determine whether the table is currently present so the message
//         shown to the user is accurate.
// ---------------------------------------------------------------------------
$check  = $conn->query("SHOW TABLES LIKE '" . TBL_NAME . "'");
$exists = ($check !== false && $check->num_rows > 0);

if ($check !== false) {
    $check->free();
}

// ---------------------------------------------------------------------------
// Step 3: Drop the table. IF EXISTS keeps the script from erroring out when
//         the table has already been removed.
// ---------------------------------------------------------------------------
$sql = "DROP TABLE IF EXISTS " . TBL_NAME;

if ($conn->query($sql) === true) {
    if ($exists) {
        echo "<p class='ok'>Table <code>" . TBL_NAME .
             "</code> was dropped successfully.</p>";
        echo "<p class='warn'>All rows and the table structure have been " .
             "permanently removed.</p>";
    } else {
        echo "<p class='warn'>Table <code>" . TBL_NAME .
             "</code> did not exist, so there was nothing to drop.</p>";
    }
    echo "<p>Run <code>MiguelCreateTable.php</code> to rebuild the table.</p>";
} else {
    echo "<p class='fail'>Error dropping table: " .
         htmlspecialchars($conn->error) . "</p>";
}

// ---------------------------------------------------------------------------
// Step 4: Close the connection.
// ---------------------------------------------------------------------------
$conn->close();
?>

</body>
</html>