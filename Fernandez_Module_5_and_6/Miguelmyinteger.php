<?php
/*
 * MiguelMyInteger.php
 * CSD440
 * Defines the MiguelMyInteger class and tests its methods.
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

    // True if the given number is even
    public function isEven($number) {
        return $number % 2 == 0;
    }

    // True if the given number is odd
    public function isOdd($number) {
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