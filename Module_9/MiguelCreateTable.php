<?php
/* ============================================================================
   Program Name: MiguelCreateTable.php
   Author:       Miguel Fernandez
   Course:       CSD440 - PHP / Apache
   Assignment:   Module 8 - Create and Populate a Database Table
   Date:         09/20/2026

   Description:  Uses MySQLi to connect to the baseball_01 database and create
                 the table "miguel_fleet_roster". The table stores the pilot
                 roster for a science-fiction fleet universe and contains nine
                 fields spanning five different data types (INT, VARCHAR,
                 DECIMAL, TINYINT, DATE).

   Inputs:       None (no user input required).
   Outputs:      An HTML page confirming the table was created, or reporting
                 the MySQL error that prevented creation.
   ============================================================================ */

// ---------------------------------------------------------------------------
// Database connection constants
// ---------------------------------------------------------------------------
define('DB_HOST', 'localhost');   // server hosting MySQL
define('DB_USER', 'student1');    // assigned database login ID
define('DB_PASS', 'pass');        // assigned database password
define('DB_NAME', 'baseball_01'); // database assigned for this assignment
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
    <title>Create Table - Miguel Fernandez</title>
    <style>
        body   { font-family: Arial, Helvetica, sans-serif; margin: 40px; }
        h1     { border-bottom: 2px solid #333; padding-bottom: 6px; }
        .ok    { color: #14601f; font-weight: bold; }
        .fail  { color: #a00000; font-weight: bold; }
        code   { background: #f2f2f2; padding: 2px 4px; }
    </style>
</head>
<body>

<h1>Create Table</h1>

<?php
// ---------------------------------------------------------------------------
// Step 1: Open a connection to the MySQL server and select the database.
// ---------------------------------------------------------------------------
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verify the connection before attempting any queries.
if ($conn->connect_error) {
    echo "<p class='fail'>Connection failed: " .
         htmlspecialchars($conn->connect_error) . "</p>";
    echo "</body></html>";
    exit();   // Nothing further can be done without a connection.
}

echo "<p class='ok'>Connected to the " . DB_NAME . " database.</p>";

// ---------------------------------------------------------------------------
// Step 2: Build the CREATE TABLE statement.
//         IF NOT EXISTS prevents an error if the table is already present.
//
//         Field list (9 fields / 5 data types):
//           pilot_id       INT          - auto-incrementing primary key
//           call_sign      VARCHAR(40)  - pilot's call sign, must be unique
//           given_name     VARCHAR(60)  - full name of the pilot
//           faction        VARCHAR(30)  - fleet or organization served
//           rank_title     VARCHAR(30)  - military rank held
//           ship_class     VARCHAR(30)  - class of craft assigned
//           missions_flown INT          - total completed missions
//           combat_rating  DECIMAL(3,1) - performance score, 0.0 to 10.0
//           active_duty    TINYINT(1)   - 1 = active, 0 = reserve/retired
//           enlisted_date  DATE         - date the pilot joined the fleet
// ---------------------------------------------------------------------------
$sql = "CREATE TABLE IF NOT EXISTS " . TBL_NAME . " (
            pilot_id       INT            NOT NULL AUTO_INCREMENT,
            call_sign      VARCHAR(40)    NOT NULL,
            given_name     VARCHAR(60)    NOT NULL,
            faction        VARCHAR(30)    NOT NULL,
            rank_title     VARCHAR(30)    NOT NULL,
            ship_class     VARCHAR(30)    NOT NULL,
            missions_flown INT            NOT NULL DEFAULT 0,
            combat_rating  DECIMAL(3,1)   NOT NULL DEFAULT 0.0,
            active_duty    TINYINT(1)     NOT NULL DEFAULT 1,
            enlisted_date  DATE           NOT NULL,
            PRIMARY KEY (pilot_id),
            UNIQUE KEY uq_call_sign (call_sign)
        )";

// ---------------------------------------------------------------------------
// Step 3: Execute the statement and report the result to the user.
// ---------------------------------------------------------------------------
if ($conn->query($sql) === true) {
    echo "<p class='ok'>Table <code>" . TBL_NAME .
         "</code> created successfully (or already existed).</p>";
    echo "<p>Run <code>MiguelPopulateTable.php</code> next to load the " .
         "sample rows.</p>";
} else {
    echo "<p class='fail'>Error creating table: " .
         htmlspecialchars($conn->error) . "</p>";
}

// ---------------------------------------------------------------------------
// Step 4: Close the connection to release the database resources.
// ---------------------------------------------------------------------------
$conn->close();
?>

</body>
</html>