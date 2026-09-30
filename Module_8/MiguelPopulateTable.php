<?php
/*
 * File:        MiguelPopulateTable.php
 * Author:      Miguel
 * Date:        2026-09-29
 * Course:      Module 8 - Creating a MySQL Table with PHP (MySQLi)
 * Description: Connects to the baseball_01 database with MySQLi and inserts
 *              ten video game records into the "miguel_video_games" table
 *              using a prepared statement. Rows that are already in the
 *              table (same title and platform) are skipped, so the script
 *              can be run more than once without creating duplicates. The
 *              page reports how many rows were inserted and skipped, or an
 *              error if the table is missing or the connection fails.
 */

// ---------------------------------------------------------------------------
// Database connection settings
// ---------------------------------------------------------------------------
require_once "MiguelDbConfig.php";

// Records to insert:
// title, genre, platform, release_date, price, rating (1-10), multiplayer
$games = [
    ["The Legend of Zelda: Tears of the Kingdom", "Action-Adventure", "Nintendo Switch", "2023-05-12", 69.99, 10, false],
    ["Elden Ring",                                "Action RPG",       "PC",              "2022-02-25", 59.99, 10, true],
    ["Minecraft",                                 "Sandbox",          "PC",              "2011-11-18", 29.99, 9,  true],
    ["Stardew Valley",                            "Simulation",       "PC",              "2016-02-26", 14.99, 9,  true],
    ["Hades",                                     "Roguelike",        "Nintendo Switch", "2020-09-17", 24.99, 9,  false],
    ["Mario Kart 8 Deluxe",                       "Racing",           "Nintendo Switch", "2017-04-28", 59.99, 9,  true],
    ["God of War Ragnarok",                       "Action-Adventure", "PlayStation 5",   "2022-11-09", 69.99, 9,  false],
    ["Halo Infinite",                             "Shooter",          "Xbox Series X",   "2021-12-08", 59.99, 7,  true],
    ["Celeste",                                   "Platformer",       "PC",              "2018-01-25", 19.99, 9,  false],
    ["EA Sports FC 24",                           "Sports",           "PlayStation 5",   "2023-09-29", 69.99, 6,  true],
];

// Throw mysqli_sql_exception on errors so they can be caught below.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$success  = false;  // true when every insert ran without an error
$message  = "";     // Result or error message shown to the user
$inserted = [];     // Titles of rows that were added
$skipped  = [];     // Titles of rows that were already in the table

try {
    // Open the connection to the database.
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");

    // Prepared statement: the ? placeholders are filled in for each game.
    // INSERT IGNORE skips a row that matches the unique (title, platform) key.
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO $table
            (title, genre, platform, release_date, price, rating, multiplayer)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    // Bind variables to the placeholders.
    // Types: s = string, d = double (decimal), i = integer.
    $stmt->bind_param("ssssdii", $title, $genre, $platform, $releaseDate, $price, $rating, $multiplayer);

    foreach ($games as $game) {
        [$title, $genre, $platform, $releaseDate, $price, $rating, $multiplayer] = $game;
        $multiplayer = $multiplayer ? 1 : 0;   // Store the boolean as 1 or 0
        $stmt->execute();

        // affected_rows is 1 when the row was inserted, 0 when it was skipped.
        if ($stmt->affected_rows === 1) {
            $inserted[] = "$title ($platform)";
        } else {
            $skipped[] = "$title ($platform)";
        }
    }

    $stmt->close();
    $conn->close();

    $success = true;
    $message = count($inserted) . " row(s) inserted and " . count($skipped)
             . " row(s) skipped because they were already in \"$table\".";
} catch (mysqli_sql_exception $e) {
    // Error 1146 means the table does not exist yet.
    if ($e->getCode() === 1146) {
        $message = "Table \"$table\" does not exist. Run MiguelCreateTable.php first.";
    } else {
        $message = "Error populating table: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miguel - Populate Table</title>
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
    <h1>Populate Table</h1>
    <p class="<?= $success ? "ok" : "error" ?>"><?= htmlspecialchars($message) ?></p>

    <?php if (!empty($inserted)): ?>
        <h2>Inserted</h2>
        <ul>
        <?php foreach ($inserted as $name): ?>
            <li><?= htmlspecialchars($name) ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($skipped)): ?>
        <h2>Skipped (already in the table)</h2>
        <ul>
        <?php foreach ($skipped as $name): ?>
            <li><?= htmlspecialchars($name) ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <nav>
        <a href="MiguelCreateTable.php">Create</a>
        <a href="MiguelPopulateTable.php">Populate</a>
        <a href="MiguelQueryTable.php">Query</a>
        <a href="MiguelDropTable.php">Drop</a>
    </nav>
</div>
</body>
</html>
