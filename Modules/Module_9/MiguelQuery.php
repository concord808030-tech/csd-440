<?php
/* ============================================================================
   Program Name: MiguelQuery.php
   Author:       Miguel Fernandez
   Course:       CSD440 - PHP / Apache
   Assignment:   Module 9 - Query and Add Records with Forms
   Date:         09/27/2026

   Description:  Displays a search form. When the user submits it, the page
                 uses a MySQLi prepared statement to find pilots in the
                 "miguel_fleet_roster" table whose call sign or name contains
                 the text entered, and shows the results in an HTML table.

   Inputs:       search - text typed by the user (sent with GET).
   Outputs:      The search form and a table of matching pilots, or a
                 message if nothing matched or an error occurred.

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

// Show errors as messages instead of exceptions (PHP 8.1+).
mysqli_report(MYSQLI_REPORT_OFF);

// Read the search text from the form (empty if the form was not submitted).
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Pilots - Miguel Fernandez</title>
    <style>
        body   { font-family: Arial, Helvetica, sans-serif; margin: 40px; }
        h1     { border-bottom: 2px solid #333; padding-bottom: 6px; }
        table  { border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px 10px; text-align: left; }
        th     { background: #333; color: #fff; }
        .fail  { color: #a00000; font-weight: bold; }
    </style>
</head>
<body>

<h1>Search Pilots</h1>
<p><a href="MiguelIndex.php">Back to Index</a></p>

<!-- Search form -->
<form method="get" action="MiguelQuery.php">
    <label for="search">Call sign or name:</label>
    <input type="text" id="search" name="search"
           value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<?php
// Only run the query after the form has been submitted.
if (isset($_GET['search'])) {

    // Step 1: Connect to the database.
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        echo "<p class='fail'>Connection failed: " .
             htmlspecialchars($conn->connect_error) . "</p>";
    } else {

        // Step 2: Prepare the query. % lets the text match anywhere.
        $sql = "SELECT call_sign, given_name, faction, rank_title,
                       ship_class, combat_rating
                FROM " . TBL_NAME . "
                WHERE call_sign LIKE ? OR given_name LIKE ?
                ORDER BY call_sign";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "<p class='fail'>Query failed: " .
                 htmlspecialchars($conn->error) . "</p>";
        } else {
            // Step 3: Bind the search text and run the query.
            $like = "%" . $search . "%";
            $stmt->bind_param("ss", $like, $like);
            $stmt->execute();
            $result = $stmt->get_result();

            // Step 4: Display the results.
            if ($result->num_rows === 0) {
                echo "<p>No pilots found.</p>";
            } else {
                echo "<p>" . $result->num_rows . " pilot(s) found.</p>";
                echo "<table>";
                echo "<tr><th>Call Sign</th><th>Name</th><th>Faction</th>
                          <th>Rank</th><th>Ship Class</th><th>Rating</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['call_sign'])     . "</td>";
                    echo "<td>" . htmlspecialchars($row['given_name'])    . "</td>";
                    echo "<td>" . htmlspecialchars($row['faction'])       . "</td>";
                    echo "<td>" . htmlspecialchars($row['rank_title'])    . "</td>";
                    echo "<td>" . htmlspecialchars($row['ship_class'])    . "</td>";
                    echo "<td>" . htmlspecialchars($row['combat_rating']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            $stmt->close();
        }

        // Step 5: Close the connection.
        $conn->close();
    }
}
?>

</body>
</html>