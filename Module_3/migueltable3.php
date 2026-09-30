<?php
/*
   Program Name: MiguelTable3.php
   Author: Miguel
   Description: HTML table where each cell holds the
                sum of two random numbers, calculated by the
                addNumbers() function
*/

require_once("MiguelSum.php");   

$rows = 7;
$cols = 7;
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