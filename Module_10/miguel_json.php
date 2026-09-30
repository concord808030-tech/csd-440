<?php
/*
 * File:        miguel_json.php
 * Author:      Miguel
 * Date:        2026-09-29
 * Course:      Module 10 - JSON Encoding with PHP
 * Description: PHP CGI script that receives the POST data from
 *              "miguel_json_form.html", validates every field, encodes the
 *              data into JSON using json_encode(), and returns a formatted
 *              page showing the JSON. If the request is invalid, any field
 *              fails validation, or encoding fails, an error display listing
 *              each problem is returned instead.
 */

/**
 * Reads a value from $_POST, trims surrounding whitespace, and returns it.
 * Returns an empty string when the field was not submitted.
 *
 * @param string $name The form field name.
 * @return string      The trimmed value, or "" if missing.
 */
function getField($name)
{
    return isset($_POST[$name]) ? trim((string) $_POST[$name]) : "";
}

/**
 * Escapes a string for safe output inside HTML to prevent XSS.
 *
 * @param string $text The text to escape.
 * @return string      The HTML-safe text.
 */
function h($text)
{
    return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}

// ---------------------------------------------------------------------------
// Main processing
// ---------------------------------------------------------------------------

// Use Eastern Time for the submission timestamp and the future-date check.
date_default_timezone_set("America/New_York");

$errors = [];   // List of error messages to report to the user
$json   = "";   // The encoded JSON string on success

// Only accept data submitted from the form via POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $errors[] = "No form data was received. Please submit the form from miguel_json_form.html.";
} else {
    // Collect every submitted field.
    $firstName     = getField("firstName");
    $lastName      = getField("lastName");
    $email         = getField("email");
    $phone         = getField("phone");
    $age           = getField("age");
    $birthDate     = getField("birthDate");
    $address       = getField("address");
    $city          = getField("city");
    $state         = strtoupper(getField("state"));
    $zip           = getField("zip");
    $contactMethod = getField("contactMethod");
    $comments      = getField("comments");

    // --- Required-field and format validation ---
    if ($firstName === "") {
        $errors[] = "First Name is required.";
    }
    if ($lastName === "") {
        $errors[] = "Last Name is required.";
    }

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email \"" . $email . "\" is not a valid email address.";
    }

    if ($phone === "") {
        $errors[] = "Phone is required.";
    } elseif (!preg_match('/^\d{3}-\d{3}-\d{4}$/', $phone)) {
        $errors[] = "Phone must be in the format ###-###-####.";
    }

    if ($age === "") {
        $errors[] = "Age is required.";
    } elseif (filter_var($age, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 120]]) === false) {
        $errors[] = "Age must be a whole number between 1 and 120.";
    }

    if ($birthDate === "") {
        $errors[] = "Date of Birth is required.";
    } else {
        // Confirm the date is a real calendar date in YYYY-MM-DD format
        // and is not in the future.
        $date = DateTime::createFromFormat("Y-m-d", $birthDate);
        if (!$date || $date->format("Y-m-d") !== $birthDate) {
            $errors[] = "Date of Birth must be a valid date (YYYY-MM-DD).";
        } elseif ($date > new DateTime()) {
            $errors[] = "Date of Birth cannot be in the future.";
        }
    }

    if ($address === "") {
        $errors[] = "Street Address is required.";
    }
    if ($city === "") {
        $errors[] = "City is required.";
    }

    if ($state === "") {
        $errors[] = "State is required.";
    } elseif (!preg_match('/^[A-Z]{2}$/', $state)) {
        $errors[] = "State must be a 2-letter abbreviation (e.g. FL).";
    }

    if ($zip === "") {
        $errors[] = "ZIP Code is required.";
    } elseif (!preg_match('/^\d{5}(-\d{4})?$/', $zip)) {
        $errors[] = "ZIP Code must be 5 digits or ZIP+4 (#####-####).";
    }

    $validMethods = ["Email", "Phone", "Text"];
    if ($contactMethod === "") {
        $errors[] = "Preferred Contact method is required.";
    } elseif (!in_array($contactMethod, $validMethods, true)) {
        $errors[] = "Preferred Contact method is not a valid choice.";
    }

    // --- Encode to JSON only when all fields are valid ---
    if (empty($errors)) {
        // Build an associative array; json_encode turns it into a JSON object.
        $data = [
            "firstName"     => $firstName,
            "lastName"      => $lastName,
            "email"         => $email,
            "phone"         => $phone,
            "age"           => (int) $age,
            "birthDate"     => $birthDate,
            "address"       => [
                "street" => $address,
                "city"   => $city,
                "state"  => $state,
                "zip"    => $zip
            ],
            "contactMethod" => $contactMethod,
            "comments"      => $comments,
            "submittedAt"   => date("Y-m-d H:i:s")
        ];

        // Pretty-print for readability; keep slashes and Unicode readable.
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // json_encode returns false on failure (e.g. invalid UTF-8 input).
        if ($json === false) {
            $errors[] = "JSON encoding failed: " . json_last_error_msg();
        }
    }
}

$success = empty($errors);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miguel JSON <?= $success ? "Result" : "Error" ?></title>
    <style>
        /* ---------- Page layout ---------- */
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #eef2f7;
            margin: 0;
            padding: 30px 16px;
            color: #222;
        }
        .container {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            padding: 28px 32px;
        }
        h1 {
            margin-top: 0;
            text-align: center;
        }
        h1.ok    { color: #1e7e34; }
        h1.error { color: #c0392b; }

        /* ---------- JSON output box ---------- */
        pre.json {
            background: #1e1e2e;
            color: #a6e3a1;
            padding: 18px 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: Consolas, "Courier New", monospace;
            font-size: 0.95em;
            line-height: 1.45;
        }

        /* ---------- Error box ---------- */
        .error-box {
            background: #fdecea;
            border: 1px solid #f5c2c0;
            border-left: 6px solid #c0392b;
            border-radius: 6px;
            padding: 14px 20px;
        }
        .error-box ul {
            margin: 8px 0 0;
            padding-left: 20px;
        }
        .error-box li {
            margin-bottom: 4px;
        }

        /* ---------- Back link ---------- */
        .back {
            display: inline-block;
            margin-top: 20px;
            background: #1f4e79;
            color: #fff;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 6px;
        }
        .center { text-align: center; }
    </style>
</head>
<body>
<div class="container">
<?php if ($success): ?>
    <!-- Success display: the submitted data in JSON format -->
    <h1 class="ok">Form Submitted Successfully</h1>
    <p>Your data was encoded with <code>json_encode()</code>. The JSON output is shown below:</p>
    <pre class="json"><?= h($json) ?></pre>
<?php else: ?>
    <!-- Error display: list every problem that was found -->
    <h1 class="error">Submission Error</h1>
    <div class="error-box">
        <strong>The form could not be processed for the following reason(s):</strong>
        <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= h($error) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
    <div class="center">
        <a class="back" href="miguel_json_form.html">Back to Form</a>
    </div>
</div>
</body>
</html>
