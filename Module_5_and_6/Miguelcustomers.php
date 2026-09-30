<?php
/*
 * Program Name: Miguelcustomers.php
 * Author:       Miguel Fernandez
 * Course:       CSD440
 * Assignment:   Modules 5 and 6
 * Date:         2026-09-08
 * Description:  Creates an array of 10 customers (each one an associative
 *               array) and uses PHP array functions to search them by last
 *               name, age, and phone number, and to sort them by age.
 * AI Use:       Claude Code (Anthropic) was used to help check for bugs
 *               and to write and improve comments.
 */

// The customer list: an indexed array of associative arrays
$customers = array(
    array("first" => "Ana",    "last" => "Torres", "age" => 28, "phone" => "555-0101"),
    array("first" => "Brian",  "last" => "Cole",   "age" => 45, "phone" => "555-0102"),
    array("first" => "Carmen", "last" => "Diaz",   "age" => 33, "phone" => "555-0103"),
    array("first" => "David",  "last" => "Nguyen", "age" => 19, "phone" => "555-0104"),
    array("first" => "Elena",  "last" => "Rossi",  "age" => 52, "phone" => "555-0105"),
    array("first" => "Frank",  "last" => "Okafor", "age" => 37, "phone" => "555-0106"),
    array("first" => "Grace",  "last" => "Kim",    "age" => 24, "phone" => "555-0107"),
    array("first" => "Hector", "last" => "Torres", "age" => 41, "phone" => "555-0108"),
    array("first" => "Isabel", "last" => "Moreno", "age" => 30, "phone" => "555-0109"),
    array("first" => "Jamal",  "last" => "Wright", "age" => 58, "phone" => "555-0110")
);

// Prints a heading and a table of customers
function showCustomers($title, $list) {
    echo "<h2>$title</h2>";
    echo "<table>";
    echo "<tr><th>First</th><th>Last</th><th>Age</th><th>Phone</th></tr>";
    foreach ($list as $c) {
        echo "<tr><td>{$c['first']}</td><td>{$c['last']}</td><td>{$c['age']}</td><td>{$c['phone']}</td></tr>";
    }
    echo "</table>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Miguel Customers</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; max-width: 550px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #eee; }
  </style>
</head>
<body>

<h1>Customer Records</h1>

<?php
// 1. Show all customers
showCustomers("All Customers", $customers);

// 2. Find by last name
$byLastName = array_filter($customers, function ($c) {
    return $c["last"] == "Torres";
});
showCustomers("Last Name: Torres", $byLastName);

// 3. Find by age
$older = array_filter($customers, function ($c) {
    return $c["age"] >= 40;
});
showCustomers("Age 40 and Older", $older);

// 4. Find by phone number
$phones = array_column($customers, "phone");
$found  = array_search("555-0107", $phones);
// array_search returns false when nothing matches, so show an empty table then
showCustomers("Phone: 555-0107", $found === false ? array() : array($customers[$found]));

// 5. Sort by age
$byAge = $customers;
usort($byAge, function ($a, $b) {
    return $a["age"] - $b["age"];
});
showCustomers("Sorted by Age", $byAge);
?>

</body>
</html>