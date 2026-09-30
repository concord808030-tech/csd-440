<?php
/*
   Program Name: migueltable3.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Assignment:   Module 3
   Date:         2026-08-23
   Description:  HTML table where each cell holds the sum of two random
                 numbers (1-50), calculated by the addNumbers() function
                 from the external file miguelsum.php.
   AI Use:       Claude Code (Anthropic) was used to help check for bugs
                 and to write and improve comments.
*/

// Load addNumbers(). The filename must match exactly (lowercase) because
// Linux web servers treat "MiguelSum.php" and "miguelsum.php" as different.
require_once("miguelsum.php");

// Table size settings
$rows = 7;   // number of table rows
$cols = 7;   // number of cells in each row
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Random Number Sum Table</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; }
        td { border: 1px solid #333; padding: 10px 16px; text-align: center; }
    </style>
</head>
<body>
    <h1>Random Number Sum Table</h1>
    <table>
        <!-- Outer loop: one row per pass -->
        <?php for ($row = 1; $row <= $rows; $row++) { ?>
        <tr>
            <!-- Inner loop: one cell per pass -->
            <?php for ($col = 1; $col <= $cols; $col++) { ?>
            <td><?php echo addNumbers(rand(1, 50), rand(1, 50)); ?></td>
            <?php } ?>
        </tr>
        <?php } ?>
    </table>
</body>
</html>