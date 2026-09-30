<?php
/* ============================================================================
   Program Name: MiguelQueryTable.php
   Author:       Miguel Fernandez
   Course:       CSD440 - PHP / Apache
   Assignment:   Module 8 - Create and Populate a Database Table
   Date:         09/20/2026

   Description:  Uses MySQLi to run three test queries against the
                 "miguel_fleet_roster" table and display the results in HTML
                 tables:
                   Query 1 - every row in the table, ordered by call sign
                   Query 2 - a filtered/prepared query returning only active
                             pilots with a combat rating of 8.5 or higher
                   Query 3 - an aggregate query summarizing each faction

   Inputs:       None (the filter values are set in the code).
   Outputs:      An HTML page containing the three result sets, or an error
                 message if a query fails.

   AI Use:       Claude Code (Anthropic) was used to help check for bugs
                 and to write and improve comments.
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
    <title>Query Table - Miguel Fernandez</title>
    <style>
        body   { font-family: Arial, Helvetica, sans-serif; margin: 40px; }
        h1     { border-bottom: 2px solid #333; padding-bottom: 6px; }
        h2     { margin-top: 32px; }
        table  { border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px 10px; text-align: left; }
        th     { background: #333; color: #fff; }
        tr:nth-child(even) td { background: #f5f5f5; }
        .ok    { color: #14601f; }
        .fail  { color: #a00000; font-weight: bold; }
        .num   { text-align: right; }
    </style>
</head>
<body>

<h1>Query Table - Fleet Pilot Roster</h1>

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

// ===========================================================================
// QUERY 1 - Display every record in the table.
// ===========================================================================
echo "<h2>Query 1: Complete Roster</h2>";

$sql1 = "SELECT pilot_id, call_sign, given_name, faction, rank_title,
                ship_class, missions_flown, combat_rating, active_duty,
                enlisted_date
         FROM " . TBL_NAME . "
         ORDER BY call_sign";

$result1 = $conn->query($sql1);

if ($result1 === false) {
    echo "<p class='fail'>Query 1 failed: " .
         htmlspecialchars($conn->error) . "</p>";
} elseif ($result1->num_rows === 0) {
    echo "<p class='fail'>No records found. Run MiguelPopulateTable.php " .
         "first.</p>";
} else {
    echo "<p>" . $result1->num_rows . " record(s) returned.</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Call Sign</th><th>Name</th><th>Faction</th>
              <th>Rank</th><th>Ship Class</th><th>Missions</th>
              <th>Rating</th><th>Status</th><th>Enlisted</th></tr>";

    // Fetch one associative row at a time until the result set is exhausted.
    while ($row = $result1->fetch_assoc()) {
        // The TINYINT status flag is converted to readable text.
        $status = ($row['active_duty'] == 1) ? "Active" : "Reserve";

        echo "<tr>";
        echo "<td class='num'>" . htmlspecialchars($row['pilot_id'])       . "</td>";
        echo "<td>"             . htmlspecialchars($row['call_sign'])      . "</td>";
        echo "<td>"             . htmlspecialchars($row['given_name'])     . "</td>";
        echo "<td>"             . htmlspecialchars($row['faction'])        . "</td>";
        echo "<td>"             . htmlspecialchars($row['rank_title'])     . "</td>";
        echo "<td>"             . htmlspecialchars($row['ship_class'])     . "</td>";
        echo "<td class='num'>" . htmlspecialchars($row['missions_flown']) . "</td>";
        echo "<td class='num'>" . htmlspecialchars($row['combat_rating'])  . "</td>";
        echo "<td>"             . $status                                  . "</td>";
        echo "<td>"             . htmlspecialchars($row['enlisted_date'])  . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    $result1->free();   // release the result set memory
}

// ===========================================================================
// QUERY 2 - Filtered search using a prepared statement.
//           Returns active pilots whose combat rating meets the minimum.
// ===========================================================================
echo "<h2>Query 2: Active Pilots Rated 8.5 or Higher</h2>";

$minRating = 8.5;   // filter value bound to the prepared statement

$sql2 = "SELECT call_sign, given_name, rank_title, combat_rating
         FROM " . TBL_NAME . "
         WHERE combat_rating >= ? AND active_duty = 1
         ORDER BY combat_rating DESC";

$stmt2 = $conn->prepare($sql2);

if ($stmt2 === false) {
    echo "<p class='fail'>Query 2 prepare failed: " .
         htmlspecialchars($conn->error) . "</p>";
} else {
    $stmt2->bind_param("d", $minRating);   // d = double / decimal value
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows === 0) {
        echo "<p>No pilots matched that filter.</p>";
    } else {
        echo "<p>" . $result2->num_rows . " record(s) returned.</p>";
        echo "<table>";
        echo "<tr><th>Call Sign</th><th>Name</th><th>Rank</th>
                  <th>Rating</th></tr>";

        while ($row = $result2->fetch_assoc()) {
            echo "<tr>";
            echo "<td>"             . htmlspecialchars($row['call_sign'])     . "</td>";
            echo "<td>"             . htmlspecialchars($row['given_name'])    . "</td>";
            echo "<td>"             . htmlspecialchars($row['rank_title'])    . "</td>";
            echo "<td class='num'>" . htmlspecialchars($row['combat_rating']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        $result2->free();
    }
    $stmt2->close();
}

// ===========================================================================
// QUERY 3 - Aggregate query grouping the roster by faction.
// ===========================================================================
echo "<h2>Query 3: Summary by Faction</h2>";

$sql3 = "SELECT faction,
                COUNT(*)                AS pilot_count,
                SUM(missions_flown)     AS total_missions,
                ROUND(AVG(combat_rating), 2) AS avg_rating
         FROM " . TBL_NAME . "
         GROUP BY faction
         ORDER BY avg_rating DESC";

$result3 = $conn->query($sql3);

if ($result3 === false) {
    echo "<p class='fail'>Query 3 failed: " .
         htmlspecialchars($conn->error) . "</p>";
} else {
    echo "<table>";
    echo "<tr><th>Faction</th><th>Pilots</th><th>Total Missions</th>
              <th>Average Rating</th></tr>";

    while ($row = $result3->fetch_assoc()) {
        echo "<tr>";
        echo "<td>"             . htmlspecialchars($row['faction'])        . "</td>";
        echo "<td class='num'>" . htmlspecialchars($row['pilot_count'])    . "</td>";
        echo "<td class='num'>" . htmlspecialchars($row['total_missions']) . "</td>";
        echo "<td class='num'>" . htmlspecialchars($row['avg_rating'])     . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    $result3->free();
}

// ---------------------------------------------------------------------------
// Close the connection once all queries are complete.
// ---------------------------------------------------------------------------
$conn->close();
?>

</body>
</html>