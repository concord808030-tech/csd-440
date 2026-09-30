<?php
/*
   Program Name: miguelsum.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Assignment:   Module 3
   Date:         2026-08-23
   Description:  External function file. It holds the addNumbers() function
                 so other pages can reuse it by including this file with
                 require_once (see migueltable3.php).
   AI Use:       Claude Code (Anthropic) was used to help check for bugs
                 and to write and improve comments.
*/

// Adds two numbers and returns the result.
// $num1, $num2 - the numbers to add
// Returns      - their sum
function addNumbers($num1, $num2) {
    return $num1 + $num2;
}
?>