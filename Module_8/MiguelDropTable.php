<?php
/*
 * File:        MiguelDropTable.php
 * Author:      Miguel
 * Date:        2026-09-29
 * Course:      Module 8 - Creating a MySQL Table with PHP (MySQLi)
 * Description: Connects to the baseball_01 database with MySQLi and drops
 *              (permanently deletes) the "miguel_video_games" table and all
 *              of its data. If the table does not exist, a message says so.
 *              If the connection or the DROP fails, an error is displayed.
 */

// ---------------------------------------------------------------------------
// Database connection settings
// ---------------------------------------------------------------------------
require_once "MiguelDbConfig.php";

// Throw mysqli_sql_exception on errors so they can be caught below.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$success = false;   // true when the statement ran without an error
$message = "";      // Result or error message shown to the user

try {
    // Open the connection to the database.
    $conn = new mysqli($host, $user, $password, $database);

    // Check whether the table exists so the message can say what happened.
    $check  = $conn->query("SHOW TABLES LIKE '$table'");
    $exists = $check->num_rows > 0;
    $check->free();

    if ($exists) {
        $conn->query("DROP TABLE $table");
        $message = "Table \"$table\" was dropped successfully from database \"$database\".";
    } else {
        $message = "Table \"$table\" does not exist, so there was nothing to drop.";
    }
    $success = true;

    $conn->close();
} catch (mysqli_sql_exception $e) {
    $message = "Error dropping table: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miguel - Drop Table</title>
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; background: #eef2f7; margin: 0; padding: 30px 16px; color: #222; }
        .container { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1); padding: 28px 32px; }
        h1 { margin-top: 0; color: #1f4e79; }
        .ok    { background: #e4f3e7; border-left: 6px solid #1e7e34; padding: 12px 16px; border-radius: 6px; }
        .error { background: #fdecea; border-left: 6px solid #c0392b; padding: 12px 16px; border-radius: 6px; }
        nav a { color: #1f4e79; margin-right: 14px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Drop Table</h1>
    <p class="<?= $success ? "ok" : "error" ?>"><?= htmlspecialchars($message) ?></p>
    <nav>
        <a href="MiguelCreateTable.php">Create</a>
        <a href="MiguelPopulateTable.php">Populate</a>
        <a href="MiguelQueryTable.php">Query</a>
        <a href="MiguelDropTable.php">Drop</a>
    </nav>
</div>
</body>
</html>
