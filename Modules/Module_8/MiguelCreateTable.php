<?php
/*
 * File:        MiguelCreateTable.php
 * Author:      Miguel
 * Date:        2026-09-29
 * Course:      Module 8 - Creating a MySQL Table with PHP (MySQLi)
 * Description: Connects to the baseball_01 database with MySQLi and creates
 *              the "miguel_video_games" table. The table stores information about video games and has eight fields
 *              using several data types (INT, VARCHAR, DATE, DECIMAL,
 *              TINYINT and BOOLEAN). If the table already exists, or the
 *              connection fails, an error message is displayed instead.
 * AI Use:      Claude Code (Anthropic) was used to help check for bugs
 *              and to write and improve comments.
 */

// ---------------------------------------------------------------------------
// Database connection settings
// ---------------------------------------------------------------------------
require_once "MiguelDbConfig.php";

// Throw mysqli_sql_exception on errors so they can be caught below.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$success = false;   // true when the table was created
$message = "";      // Result or error message shown to the user

try {
    // Open the connection to the database.
    $conn = new mysqli($host, $user, $password, $database);

    // SQL statement that defines the table and its eight fields.
    $sql = "CREATE TABLE $table (
                game_id      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
                title        VARCHAR(100)  NOT NULL,
                genre        VARCHAR(40)   NOT NULL,
                platform     VARCHAR(40)   NOT NULL,
                release_date DATE          NOT NULL,
                price        DECIMAL(5,2)  NOT NULL,
                rating       TINYINT       NOT NULL,
                multiplayer  BOOLEAN       NOT NULL DEFAULT FALSE,
                PRIMARY KEY (game_id),
                UNIQUE KEY uq_title_platform (title, platform)
            )";

    $conn->query($sql);
    $success = true;
    $message = "Table \"$table\" was created successfully in database \"$database\".";

    $conn->close();
} catch (mysqli_sql_exception $e) {
    // Error 1050 means the table already exists; give a clearer message.
    if ($e->getCode() === 1050) {
        $message = "Table \"$table\" already exists. Run MiguelDropTable.php first to recreate it.";
    } else {
        $message = "Error creating table: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miguel - Create Table</title>
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
    <h1>Create Table</h1>
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
