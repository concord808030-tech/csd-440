<?php
/*
 * File:        MiguelQueryTable.php
 * Author:      Miguel
 * Date:        2026-09-29
 * Course:      Module 8 - Creating a MySQL Table with PHP (MySQLi)
 * Description: Connects to the baseball_01 database with MySQLi and queries
 *              the "miguel_video_games" table to test it. It displays every
 *              row in an HTML table (sorted by title), the table's structure
 *              (field names and data types), and the total row count. If the
 *              table is missing, empty, or the connection fails, a message
 *              explains what to do.
 */

// ---------------------------------------------------------------------------
// Database connection settings
// ---------------------------------------------------------------------------
require_once "MiguelDbConfig.php";

/**
 * Escapes a value for safe output inside HTML to prevent XSS.
 *
 * @param mixed $text The value to escape.
 * @return string     The HTML-safe text.
 */
function h($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, "UTF-8");
}

// Throw mysqli_sql_exception on errors so they can be caught below.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$success = false;   // true when the queries ran without an error
$message = "";      // Error message shown to the user
$rows    = [];      // Every record in the table
$columns = [];      // Field names and data types

try {
    // Open the connection to the database.
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");

    // Query 1: the table's structure (field name, data type, key).
    $result = $conn->query("DESCRIBE $table");
    $columns = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    // Query 2: every record, sorted alphabetically by title.
    $result = $conn->query(
        "SELECT game_id, title, genre, platform, release_date, price, rating, multiplayer
         FROM $table
         ORDER BY title"
    );
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    $conn->close();
    $success = true;
} catch (mysqli_sql_exception $e) {
    // Error 1146 means the table does not exist yet.
    if ($e->getCode() === 1146) {
        $message = "Table \"$table\" does not exist. Run MiguelCreateTable.php first.";
    } else {
        $message = "Error querying table: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miguel - Query Table</title>
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; background: #eef2f7; margin: 0; padding: 30px 16px; color: #222; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1); padding: 28px 32px; }
        h1 { margin-top: 0; color: #1f4e79; }
        h2 { color: #1f4e79; font-size: 1.15em; margin-top: 28px; }
        .ok    { background: #e4f3e7; border-left: 6px solid #1e7e34; padding: 12px 16px; border-radius: 6px; }
        .error { background: #fdecea; border-left: 6px solid #c0392b; padding: 12px 16px; border-radius: 6px; }
        .table-wrap { overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; font-size: 0.95em; }
        th, td { border: 1px solid #d6dee8; padding: 7px 10px; text-align: left; }
        th { background: #1f4e79; color: #fff; }
        tr:nth-child(even) td { background: #f5f8fc; }
        td.num { text-align: right; font-variant-numeric: tabular-nums; }
        nav { margin-top: 24px; }
        nav a { color: #1f4e79; margin-right: 14px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Query Table: <?= h($table) ?></h1>

<?php if (!$success): ?>
    <p class="error"><?= h($message) ?></p>
<?php else: ?>
    <?php if (empty($rows)): ?>
        <p class="error">The table exists but has no rows. Run MiguelPopulateTable.php to add data.</p>
    <?php else: ?>
        <p class="ok"><?= count($rows) ?> row(s) found in "<?= h($table) ?>".</p>
    <?php endif; ?>

    <h2>Records</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Genre</th>
                <th>Platform</th>
                <th>Release Date</th>
                <th>Price</th>
                <th>Rating</th>
                <th>Multiplayer</th>
            </tr>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td class="num"><?= h($row["game_id"]) ?></td>
                <td><?= h($row["title"]) ?></td>
                <td><?= h($row["genre"]) ?></td>
                <td><?= h($row["platform"]) ?></td>
                <td><?= h(date("M j, Y", strtotime($row["release_date"]))) ?></td>
                <td class="num">$<?= h(number_format((float) $row["price"], 2)) ?></td>
                <td class="num"><?= h($row["rating"]) ?>/10</td>
                <td><?= $row["multiplayer"] ? "Yes" : "No" ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>

    <h2>Table Structure</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Field</th>
                <th>Data Type</th>
                <th>Null</th>
                <th>Key</th>
                <th>Extra</th>
            </tr>
        <?php foreach ($columns as $col): ?>
            <tr>
                <td><?= h($col["Field"]) ?></td>
                <td><?= h($col["Type"]) ?></td>
                <td><?= h($col["Null"]) ?></td>
                <td><?= h($col["Key"]) ?></td>
                <td><?= h($col["Extra"]) ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>
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
