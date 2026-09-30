<?php
/*
   Program Name: Migueltable2.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Assignment:   Module 2.2
   Date:         2026-08-23
   Description:  Displays a two-dimensional HTML table filled with
                 PHP-generated random numbers (1-100) using nested for loops.
   AI Use:       Claude Code (Anthropic) was used to help check for bugs
                 and to write and improve comments.
*/

// Table size settings (change these to resize the table)
$rows = 6;   // number of table rows
$cols = 6;   // number of cells in each row
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Random Number Table</title>
    <style>
        body  { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 10px 16px; text-align: center; }
        th    { background-color: #ddd; }
    </style>
</head>
<body>

    <h1>Random Number Table</h1>

    <table>
        <!-- Outer loop: builds one table row per pass -->
        <?php for ($row = 1; $row <= $rows; $row++) { ?>
        <tr>
            <!-- Inner loop: builds one cell per pass -->
            <?php for ($col = 1; $col <= $cols; $col++) { ?>
            <td><?php echo rand(1, 100); ?></td>
            <?php } // end inner loop ?>
        </tr>
        <?php } // end outer loop ?>
    </table>

</body>
</html>