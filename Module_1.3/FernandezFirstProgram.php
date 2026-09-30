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