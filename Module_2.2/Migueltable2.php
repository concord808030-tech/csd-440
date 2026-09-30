<?php
/* 
   Author: Miguel
   Course: CSD440
   Date: August 23, 2026
   Description: Displays a two-dimensional HTML table filled
                with PHP-generated random numbers.
    */

// Table size settings
$rows = 6;   
$cols = 6;   
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