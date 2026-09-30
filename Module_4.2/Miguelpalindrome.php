<?php
/* 
   Program Name: MiguelPalindrome.php
   Author: Miguel
   Date: 2026-08-30
*/

// Returns true if the string is a palindrome (ignores case, spaces, punctuation)
function isPalindrome($text)
{
    $clean = strtolower(preg_replace("/[^A-Za-z0-9]/", "", $text));
    return $clean === strrev($clean);
}

// Displays one table row: string, reversed string, and the test result
function displayResult($text)
{
    $result = isPalindrome($text) ? "Palindrome" : "Not a palindrome";
    echo "<tr><td>$text</td><td>" . strrev($text) . "</td><td>$result</td></tr>";
}

// Three palindromes and three that are not
$strings = array("Level", "WoW", "Star Wars raw rats","Zelda", "Final Fantasy", "Sega");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Palindrome Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 10px 16px; text-align: left; }
        th { background-color: #ddd; }
    </style>
</head>
<body>
    <h1>Palindrome Test</h1>
    <table>
        <tr><th>String</th><th>Reversed</th><th>Result</th></tr>
        <!-- Loop tests and displays each of the six strings -->
        <?php foreach ($strings as $string) { displayResult($string); } ?>
    </table>
</body>
</html>