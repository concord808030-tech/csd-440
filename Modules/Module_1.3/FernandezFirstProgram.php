<?php
/*
   Program Name: FernandezFirstProgram.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Assignment:   Module 1.3 Programming Assignment
   Date:         2026-08-16
   Description:  A first PHP page that mixes HTML with two PHP snippets:
                 one prints today's date and time, the other loops through
                 an array to build an HTML list.
   AI Use:       Claude Code (Anthropic) was used to help check for bugs
                 and to write and improve comments.
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple PHP Page</title>
</head>
<body>

    <h1>Welcome to My Simple PHP Page</h1>

    <?php
        // PHP Snippet 1: Display the current date and time
        $currentDate = date("F j, Y, g:i a");
        echo "<p>Today's date is: $currentDate</p>";
    ?>

    <h2>Favorite Languages</h2>

    <ul>
        <?php
            // PHP Snippet 2: Loop through an array and print each item as a list
            $languages = ["PHP", "JavaScript", "Java", "Python"];

            foreach ($languages as $language) {
                echo "<li>$language</li>";
            }
        ?>
    </ul>

</body>
</html>