<?php
/*
 * Program Name: Miguelmyinteger.php
 * Author:       Miguel Fernandez
 * Course:       CSD440
 * Assignment:   Modules 5 and 6
 * Date:         2026-09-08
 * Description:  Defines the MiguelMyInteger class, which stores one integer
 *               and provides a getter, a setter, and isEven(), isOdd(), and
 *               isPrime() checks. Two objects are created to test every
 *               method, including the setter.
 * AI Use:       Claude Code (Anthropic) was used to help check for bugs
 *               and to write and improve comments.
 */

class MiguelMyInteger {

    private $value;

    // Sets the integer from a parameter
    public function __construct($number) {
        $this->value = $number;
    }

    // Getter
    public function getValue() {
        return $this->value;
    }

    // Setter
    public function setValue($number) {
        $this->value = $number;
    }

    // True if the given number (or the stored number if none is given) is even
    public function isEven($number = null) {
        $number = $number ?? $this->value;
        return $number % 2 == 0;
    }

    // True if the given number (or the stored number if none is given) is odd
    public function isOdd($number = null) {
        $number = $number ?? $this->value;
        return $number % 2 != 0;
    }

    // True if the stored number is prime
    public function isPrime() {
        if ($this->value < 2) {
            return false;
        }
        for ($i = 2; $i <= sqrt($this->value); $i++) {
            if ($this->value % $i == 0) {
                return false;
            }
        }
        return true;
    }
}

// Prints all method results for one object
function testObject($obj) {
    $n = $obj->getValue();
    echo "<ul>";
    echo "<li>getValue(): $n</li>";
    echo "<li>isEven($n): "  . ($obj->isEven($n) ? "true" : "false")  . "</li>";
    echo "<li>isOdd($n): "   . ($obj->isOdd($n)  ? "true" : "false")  . "</li>";
    echo "<li>isPrime(): "   . ($obj->isPrime()  ? "true" : "false")  . "</li>";
    echo "</ul>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Miguel MyInteger</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    ul { line-height: 1.6; }
  </style>
</head>
<body>

<h1>MyInteger Class Test</h1>

<?php
$first  = new MiguelMyInteger(7);
$second = new MiguelMyInteger(12);

echo "<h2>Object 1</h2>";
testObject($first);

echo "<h2>Object 2</h2>";
testObject($second);

// Test the setter
$first->setValue(20);
echo "<h2>Object 1 after setValue(20)</h2>";
testObject($first);
?>

</body>
</html>