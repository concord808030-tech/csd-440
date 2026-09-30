<?php
/* ============================================================================
   Program Name: MiguelPopulateTable.php
   Author:       Miguel Fernandez
   Course:       CSD440 - PHP / Apache
   Assignment:   Module 8 - Create and Populate a Database Table
   Date:         09/20/2026

   Description:  Uses MySQLi to insert ten sample pilot records into the
                 "miguel_fleet_roster" table. A prepared statement is used so
                 the values are bound rather than concatenated into the SQL,
                 which protects against SQL injection and allows one compiled
                 statement to be reused for every row.

   Inputs:       A two-dimensional array of pilot records defined in this file.
   Outputs:      An HTML page listing each insert attempt and a final count of
                 the rows added.

   Note:         The table is emptied first so the script can be run more than
                 once without producing duplicate call signs.

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
    <title>Populate Table - Miguel Fernandez</title>
    <style>
        body  { font-family: Arial, Helvetica, sans-serif; margin: 40px; }
        h1    { border-bottom: 2px solid #333; padding-bottom: 6px; }
        .ok   { color: #14601f; }
        .fail { color: #a00000; font-weight: bold; }
        ul    { line-height: 1.6em; }
        code  { background: #f2f2f2; padding: 2px 4px; }
    </style>
</head>
<body>

<h1>Populate Table</h1>

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
// Step 2: Clear any existing rows so repeated runs stay consistent.
//         TRUNCATE also resets the AUTO_INCREMENT counter back to 1.
// ---------------------------------------------------------------------------
if (!$conn->query("TRUNCATE TABLE " . TBL_NAME)) {
    echo "<p class='fail'>Unable to clear the table: " .
         htmlspecialchars($conn->error) .
         " Run MiguelCreateTable.php first.</p>";
    $conn->close();
    echo "</body></html>";
    exit();
}

// ---------------------------------------------------------------------------
// Step 3: Sample data. Each inner array holds the nine values that are
//         inserted; pilot_id is generated automatically by MySQL.
//         Order: call_sign, given_name, faction, rank_title, ship_class,
//                missions_flown, combat_rating, active_duty, enlisted_date
// ---------------------------------------------------------------------------
$pilots = [
    ['Nightglass', 'Elena Reyes',      'Meridian Fleet',  'Commander',   'Interceptor', 214,  9.4, 1, '2041-03-12'],
    ['Ashfall',    'Tobias Kade',      'Meridian Fleet',  'Lieutenant',  'Interceptor', 138,  8.1, 1, '2043-07-04'],
    ['Halcyon',    'Mira Okonkwo',     'Meridian Fleet',  'Captain',     'Carrier',     301,  9.8, 1, '2038-11-29'],
    ['Vesper',     'Dane Lindqvist',   'Orion Coalition', 'Ensign',      'Scout',        47,  6.5, 1, '2046-01-18'],
    ['Ironveil',   'Salma Haddad',     'Orion Coalition', 'Major',       'Gunship',     186,  8.9, 1, '2042-05-23'],
    ['Cinder',     'Rafael Duarte',    'Orion Coalition', 'Lieutenant',  'Gunship',     122,  7.7, 0, '2044-09-09'],
    ['Solstice',   'Yuki Nakamura',    'Free Drift Wing', 'Commander',   'Frigate',     265,  9.1, 1, '2039-02-14'],
    ['Graywatch',  'Owen Baptiste',    'Free Drift Wing', 'Ensign',      'Scout',        33,  5.9, 0, '2047-06-30'],
    ['Requiem',    'Ingrid Vasquez',   'Free Drift Wing', 'Captain',     'Frigate',     198,  8.6, 1, '2040-10-05'],
    ['Lodestar',   'Amara Sinclair',   'Meridian Fleet',  'Ensign',      'Carrier',      61,  7.2, 1, '2045-12-21'],
];

// ---------------------------------------------------------------------------
// Step 4: Prepare the INSERT statement once, then bind and execute per row.
//         The type string "sssssidis" describes the bound parameters:
//           s = string, i = integer, d = double (decimal)
// ---------------------------------------------------------------------------
$sql = "INSERT INTO " . TBL_NAME . "
            (call_sign, given_name, faction, rank_title, ship_class,
             missions_flown, combat_rating, active_duty, enlisted_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo "<p class='fail'>Prepare failed: " .
         htmlspecialchars($conn->error) . "</p>";
    $conn->close();
    echo "</body></html>";
    exit();
}

// Initialize the variables before they are bound by reference.
$callSign = $givenName = $faction = $rankTitle = $shipClass = "";
$missions = $active = 0;
$rating   = 0.0;
$enlisted = "";

// Bind the nine placeholders to variables that are refilled on each pass.
$stmt->bind_param(
    "sssssidis",
    $callSign, $givenName, $faction, $rankTitle, $shipClass,
    $missions, $rating, $active, $enlisted
);

$rowsAdded = 0;   // running total of successful inserts

echo "<ul>";

// Loop through the array and execute the statement once per pilot.
foreach ($pilots as $p) {
    // list() unpacks the row into the bound variables.
    list($callSign, $givenName, $faction, $rankTitle, $shipClass,
         $missions, $rating, $active, $enlisted) = $p;

    if ($stmt->execute()) {
        $rowsAdded++;
        echo "<li class='ok'>Inserted pilot ID " . $stmt->insert_id .
             " - " . htmlspecialchars($callSign) . "</li>";
    } else {
        echo "<li class='fail'>Failed to insert " .
             htmlspecialchars($callSign) . ": " .
             htmlspecialchars($stmt->error) . "</li>";
    }
}

echo "</ul>";
echo "<p class='ok'><strong>" . $rowsAdded . " of " . count($pilots) .
     " rows inserted into " . TBL_NAME . ".</strong></p>";
echo "<p>Run <code>MiguelQueryTable.php</code> to verify the data.</p>";

// ---------------------------------------------------------------------------
// Step 5: Release the statement and close the connection.
// ---------------------------------------------------------------------------
$stmt->close();
$conn->close();
?>

</body>
</html>