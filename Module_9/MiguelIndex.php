<?php
/* ============================================================================
   Program Name: MiguelIndex.php
   Author:       Miguel Fernandez
   Course:       CSD440 - PHP / Apache
   Assignment:   Module 9 - Query and Add Records with Forms
   Date:         09/27/2026

   Description:  Home page with links to every page in the project
                 (Module 8 table pages and Module 9 query and form pages).

   Inputs:       None.
   Outputs:      An HTML page with a list of links.
   ============================================================================ */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Index - Miguel Fernandez</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 40px; }
        h1   { border-bottom: 2px solid #333; padding-bottom: 6px; }
        li   { line-height: 1.8em; }
    </style>
</head>
<body>

<h1>Fleet Pilot Roster</h1>

<h2>Module 9</h2>
<ul>
    <li><a href="MiguelQuery.php">Search Pilots</a></li>
    <li><a href="MiguelForms.php">Add a Pilot</a></li>
</ul>

<h2>Module 8</h2>
<ul>
    <li><a href="MiguelCreateTable.php">Create Table</a></li>
    <li><a href="MiguelPopulateTable.php">Populate Table</a></li>
    <li><a href="MiguelQueryTable.php">Query Table</a></li>
    <li><a href="MiguelDropTable.php">Drop Table</a></li>
</ul>

</body>
</html>