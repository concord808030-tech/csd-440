<?php
/*
   Program Name: make_banner.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Description:  Generates assets/arcade-banner.svg, the animated arcade
                 marquee at the top of the README. Letters and sprites are
                 drawn from pixel grids because GitHub does not load web
                 fonts or run scripts inside README images. The animation
                 uses CSS inside the SVG and stops for viewers who prefer
                 reduced motion.
   Usage:        php assets/make_banner.php > assets/arcade-banner.svg
   AI Use:       Claude Code (Anthropic) was used to help design and write
                 this script.
*/

// ---- 5x7 bitmap font -------------------------------------------------------
$FONT = [
 'A'=>".###.|#...#|#...#|#####|#...#|#...#|#...#", 'B'=>"####.|#...#|#...#|####.|#...#|#...#|####.",
 'C'=>".###.|#...#|#....|#....|#....|#...#|.###.", 'D'=>"####.|#...#|#...#|#...#|#...#|#...#|####.",
 'E'=>"#####|#....|#....|####.|#....|#....|#####", 'F'=>"#####|#....|#....|####.|#....|#....|#....",
 'G'=>".###.|#...#|#....|#.###|#...#|#...#|.####", 'H'=>"#...#|#...#|#...#|#####|#...#|#...#|#...#",
 'I'=>"#####|..#..|..#..|..#..|..#..|..#..|#####", 'J'=>"..###|...#.|...#.|...#.|#..#.|#..#.|.##..",
 'K'=>"#...#|#..#.|#.#..|##...|#.#..|#..#.|#...#", 'L'=>"#....|#....|#....|#....|#....|#....|#####",
 'M'=>"#...#|##.##|#.#.#|#.#.#|#...#|#...#|#...#", 'N'=>"#...#|##..#|#.#.#|#..##|#...#|#...#|#...#",
 'O'=>".###.|#...#|#...#|#...#|#...#|#...#|.###.", 'P'=>"####.|#...#|#...#|####.|#....|#....|#....",
 'Q'=>".###.|#...#|#...#|#...#|#.#.#|#..#.|.##.#", 'R'=>"####.|#...#|#...#|####.|#.#..|#..#.|#...#",
 'S'=>".####|#....|#....|.###.|....#|....#|####.", 'T'=>"#####|..#..|..#..|..#..|..#..|..#..|..#..",
 'U'=>"#...#|#...#|#...#|#...#|#...#|#...#|.###.", 'V'=>"#...#|#...#|#...#|#...#|#...#|.#.#.|..#..",
 'W'=>"#...#|#...#|#...#|#.#.#|#.#.#|##.##|#...#", 'X'=>"#...#|#...#|.#.#.|..#..|.#.#.|#...#|#...#",
 'Y'=>"#...#|#...#|.#.#.|..#..|..#..|..#..|..#..", 'Z'=>"#####|....#|...#.|..#..|.#...|#....|#####",
 '0'=>".###.|#...#|#..##|#.#.#|##..#|#...#|.###.", '1'=>"..#..|.##..|..#..|..#..|..#..|..#..|.###.",
 '2'=>".###.|#...#|....#|...#.|..#..|.#...|#####", '3'=>"####.|....#|....#|.###.|....#|....#|####.",
 '4'=>"...#.|..##.|.#.#.|#..#.|#####|...#.|...#.", '5'=>"#####|#....|####.|....#|....#|#...#|.###.",
 '6'=>".###.|#....|#....|####.|#...#|#...#|.###.", '7'=>"#####|....#|...#.|..#..|.#...|.#...|.#...",
 '8'=>".###.|#...#|#...#|.###.|#...#|#...#|.###.", '9'=>".###.|#...#|#...#|.####|....#|....#|.###.",
 '-'=>".....|.....|.....|#####|.....|.....|.....", '/'=>"....#|....#|...#.|..#..|.#...|#....|#....",
 '!'=>"..#..|..#..|..#..|..#..|..#..|.....|..#..", ' '=>".....|.....|.....|.....|.....|.....|.....",
];

// Turns a grid of rows ("#" = filled) into path data, one rect per horizontal run.
function gridPath(array $rows, float $x, float $y, float $px): string {
    $d = '';
    foreach ($rows as $r => $row) {
        $len = strlen($row);
        for ($c = 0; $c < $len; ) {
            if ($row[$c] !== '#') { $c++; continue; }
            $start = $c;
            while ($c < $len && $row[$c] === '#') $c++;
            $d .= sprintf('M%s %sh%sv%sh-%sz', $x + $start * $px, $y + $r * $px, ($c - $start) * $px, $px, ($c - $start) * $px);
        }
    }
    return $d;
}

function textWidth(string $s, float $px): float { return (strlen($s) * 6 - 1) * $px; }

function textPath(string $s, float $x, float $y, float $px): string {
    global $FONT;
    $d = '';
    foreach (str_split($s) as $i => $ch) {
        $d .= gridPath(explode('|', $FONT[$ch]), $x + $i * 6 * $px, $y, $px);
    }
    return $d;
}

// Filters a multi-colour sprite down to one colour key, as a "#" grid.
function spriteLayer(array $rows, string $key): array {
    return array_map(fn($row) => preg_replace('/[^#]/', '.', str_replace($key, '#', $row)), $rows);
}

// ---- Sprites ---------------------------------------------------------------
// L = body, D = ear/shade, K = eye, W = tusk
$elephantTop = [
 "....LLLLLL......",
 "..LLLLLLLLLDD...",
 ".LLLLLLLLLDDDL..",
 "LLLLLLLLLLDDDLL.",
 "LLLLLLLLLLDDLKL.",
 "LLLLLLLLLLLDLLLL",
 "LLLLLLLLLLLLLWLL",
 "LLLLLLLLLLLL..LL",
 ".LLLLLLLLLL...LL",
];
$legsA = [".LL.LL..LL.LL..L", ".LL.LL..LL.LL...", ".DD.DD..DD.DD..."];
$legsB = ["..LLLL...LLLL..L", "..LL..L..LL..L..", "..DD..D..DD..D.."];

$bug = [".R...R.", "..RRR..", "RRRRRRR", ".RKRKR.", "RRRRRRR", "R.R.R.R"];

$C = ['bg'=>'#1A1033','maze'=>'#5A4FCF','body'=>'#9AA3D6','shade'=>'#6C74B0','yellow'=>'#FFD23F',
      'red'=>'#EE4266','cyan'=>'#3BCEAC','white'=>'#F4F4F8','eye'=>'#1A1033'];

$W = 960; $H = 380; $DUR = 9;          // canvas and loop length (seconds)
$PX = 6;                               // sprite pixel size
$LANE_Y = 236;                         // top of elephant sprite
$START = -110; $END = 1000; $WALK = 0.72; // walk covers 72% of the loop

// Loop fraction at which the elephant's trunk reaches x.
$eatAt = fn(float $x) => (($x - 88) - $START) / ($END - $START) * $WALK;
$pct = fn(float $f) => round($f * 100, 2) . '%';

function elephantPath(array $top, array $legs, string $key, float $px): string {
    return gridPath(spriteLayer(array_merge($top, $legs), $key), 0, 0, $px);
}

$css = [];
$body = [];

// ---- Pellets (modules) and bugs -------------------------------------------
$pelletXs = []; for ($i = 0; $i < 10; $i++) $pelletXs[] = 110 + $i * 84;
$bugAfter = [0, 2, 4, 6, 8];            // a bug sits after these pellets
$items = [];
foreach ($pelletXs as $i => $x) {
    $items[] = ['pellet', $x];
    if (in_array($i, $bugAfter)) $items[] = ['bug', $x + 42];
}
$pelletTimes = [];
foreach ($items as $n => [$type, $x]) {
    $t = $eatAt($x);
    if ($type === 'pellet') $pelletTimes[] = $t;
    $css[] = ".i$n{animation:eat$n {$DUR}s step-end infinite}@keyframes eat$n{0%{opacity:1}{$pct($t)}{opacity:0}100%{opacity:0}}";
    if ($type === 'pellet') {
        $body[] = "<rect class=\"i$n\" x=\"" . ($x - 6) . "\" y=\"" . ($LANE_Y + 30) . "\" width=\"12\" height=\"12\" fill=\"{$C['yellow']}\"/>";
    } else {
        $body[] = "<g class=\"i$n\"><path fill=\"{$C['red']}\" d=\"" . gridPath(spriteLayer($bug, 'R'), $x - 14, $LANE_Y + 24, 4) . "\"/>"
                . "<path fill=\"{$C['eye']}\" d=\"" . gridPath(spriteLayer($bug, 'K'), $x - 14, $LANE_Y + 24, 4) . "\"/></g>";
    }
}

// ---- HUD: stage counter (00-10) and test score ----------------------------
$hudPx = 4;
$body[] = "<path fill=\"{$C['red']}\" d=\"" . textPath('STAGE', 40, 24, $hudPx) . "\"/>";
$scoreX = 40 + textWidth('STAGE ', $hudPx);
for ($k = 0; $k <= 10; $k++) {
    $from = $k === 0 ? 0 : $pelletTimes[$k - 1];
    $to   = $k === 10 ? 1 : $pelletTimes[$k];
    $frames = $k === 0 ? "0%{opacity:1}{$pct($to)}{opacity:0}" : "0%{opacity:0}{$pct($from)}{opacity:1}" . ($k < 10 ? "{$pct($to)}{opacity:0}" : '');
    $css[] = ".s$k{opacity:" . ($k === 0 ? 1 : 0) . ";animation:sc$k {$DUR}s step-end infinite}@keyframes sc$k{{$frames}100%{opacity:" . ($k === 10 ? 1 : 0) . "}}";
    $body[] = "<path class=\"s$k\" fill=\"{$C['cyan']}\" d=\"" . textPath(sprintf('%02d', $k), $scoreX, 24, $hudPx) . "\"/>";
}
$testsLabel = 'TESTS 52/52';
$tx = $W - 40 - textWidth($testsLabel, $hudPx);
$body[] = "<path fill=\"{$C['red']}\" d=\"" . textPath('TESTS', $tx, 24, $hudPx) . "\"/>";
$body[] = "<path fill=\"{$C['cyan']}\" d=\"" . textPath('52/52', $tx + textWidth('TESTS ', $hudPx), 24, $hudPx) . "\"/>";

// ---- Title -----------------------------------------------------------------
$tPx = 12; $title = 'CSD 440';
$titleX = ($W - textWidth($title, $tPx)) / 2; $titleY = 64;
$body[] = "<path fill=\"{$C['red']}\" d=\"" . textPath($title, $titleX + 6, $titleY + 4, $tPx) . "\"/>";
$body[] = "<path fill=\"{$C['yellow']}\" d=\"" . textPath($title, $titleX, $titleY, $tPx) . "\"/>";

$sPx = 4; $sub = 'SERVER-SIDE PHP';
$body[] = "<path fill=\"{$C['body']}\" d=\"" . textPath($sub, ($W - textWidth($sub, $sPx)) / 2, 168, $sPx) . "\"/>";

// ---- Maze lane -------------------------------------------------------------
foreach ([$LANE_Y - 20, $LANE_Y + 80] as $wy) {
    $body[] = "<rect x=\"24\" y=\"$wy\" width=\"" . ($W - 48) . "\" height=\"10\" rx=\"5\" fill=\"none\" stroke=\"{$C['maze']}\" stroke-width=\"3\"/>";
}

// ---- Elephant (two walk frames) -------------------------------------------
$frame = function (array $legs, string $cls) use ($elephantTop, $PX, $C) {
    return "<g class=\"$cls\">"
         . "<path fill=\"{$C['body']}\" d=\"" . elephantPath($elephantTop, $legs, 'L', $PX) . "\"/>"
         . "<path fill=\"{$C['shade']}\" d=\"" . elephantPath($elephantTop, $legs, 'D', $PX) . "\"/>"
         . "<path fill=\"{$C['eye']}\" d=\"" . elephantPath($elephantTop, $legs, 'K', $PX) . "\"/>"
         . "<path fill=\"{$C['white']}\" d=\"" . elephantPath($elephantTop, $legs, 'W', $PX) . "\"/></g>";
};
$body[] = "<g class=\"walker\">" . $frame($legsA, 'fa') . $frame($legsB, 'fb') . "</g>";
$css[] = ".walker{transform:translate(40px,{$LANE_Y}px);animation:walk {$DUR}s infinite}"
       . "@keyframes walk{0%{transform:translate({$START}px,{$LANE_Y}px);animation-timing-function:steps(90)}"
       . "{$pct($WALK)}{transform:translate({$END}px,{$LANE_Y}px)}100%{transform:translate({$END}px,{$LANE_Y}px)}}";
$css[] = ".fa{animation:fa .36s step-end infinite}.fb{opacity:0;animation:fb .36s step-end infinite}"
       . "@keyframes fa{0%{opacity:1}50%{opacity:0}}@keyframes fb{0%{opacity:0}50%{opacity:1}}";

// ---- Bottom prompt: PRESS START while walking, STAGE CLEAR at the end ------
$pPx = 4;
foreach (['PRESS START' => ['ps', $C['white']], 'STAGE CLEAR!' => ['sc', $C['yellow']]] as $label => [$cls, $fill]) {
    $body[] = "<g class=\"$cls\"><path class=\"blink\" fill=\"$fill\" d=\"" . textPath($label, ($W - textWidth($label, $pPx)) / 2, 336, $pPx) . "\"/></g>";
}
$css[] = ".ps{animation:ps {$DUR}s step-end infinite}@keyframes ps{0%{opacity:1}{$pct($WALK)}{opacity:0}}"
       . ".sc{opacity:0;animation:scl {$DUR}s step-end infinite}@keyframes scl{0%{opacity:0}{$pct($WALK)}{opacity:1}}"
       . ".blink{animation:blink 1s step-end infinite}@keyframes blink{0%{opacity:1}60%{opacity:.15}}";

// Still frame for viewers who prefer reduced motion.
$css[] = "@media (prefers-reduced-motion:reduce){*{animation:none!important}}";

// ---- Assemble --------------------------------------------------------------
$desc = 'Retro arcade marquee for CSD 440 Server-Side PHP: a pixel PHP elephant walks a maze lane eating ten module pellets and five bugs while the stage counter climbs from 00 to 10.';
echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 $W $H\" width=\"$W\" height=\"$H\" role=\"img\" aria-labelledby=\"t d\">\n";
echo "<title id=\"t\">CSD 440 Server-Side PHP</title><desc id=\"d\">$desc</desc>\n";
echo "<defs><pattern id=\"scan\" width=\"4\" height=\"4\" patternUnits=\"userSpaceOnUse\"><rect width=\"4\" height=\"2\" fill=\"#000\" opacity=\".22\"/></pattern>"
   . "<radialGradient id=\"glow\" cx=\"50%\" cy=\"45%\" r=\"75%\"><stop offset=\"60%\" stop-color=\"#000\" stop-opacity=\"0\"/><stop offset=\"100%\" stop-color=\"#000\" stop-opacity=\".55\"/></radialGradient>"
   . "<clipPath id=\"screen\"><rect width=\"$W\" height=\"$H\" rx=\"22\"/></clipPath></defs>\n";
echo "<style>" . implode("\n", $css) . "</style>\n";
echo "<g clip-path=\"url(#screen)\"><rect width=\"$W\" height=\"$H\" fill=\"{$C['bg']}\"/>\n";
echo implode("\n", $body) . "\n";
echo "<rect width=\"$W\" height=\"$H\" fill=\"url(#scan)\"/><rect width=\"$W\" height=\"$H\" fill=\"url(#glow)\"/></g>\n";
echo "</svg>\n";
