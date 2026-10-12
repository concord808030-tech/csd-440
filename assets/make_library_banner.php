<?php
/*
   Program Name: make_library_banner.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Description:  Generates assets/library-banner.svg, the black-and-white
                 banner at the top of the README. It is drawn like a book's
                 frontispiece: a double-ruled frame, the course title, a
                 shelf with one volume per module (each stamped with its
                 module number and subject) and a Gothic library window
                 with a candle in front of it. GitHub does not load web
                 fonts inside README images, so the text uses serif fonts
                 that ship with every operating system. The only animation
                 is the candle flame, and it stops for viewers who prefer
                 reduced motion.
   Usage:        php assets/make_library_banner.php > assets/library-banner.svg
   AI Use:       Claude Code (Anthropic) was used to help design and write
                 this script.
*/

// ---- Palette: black, white and greys only ----------------------------------
$INK   = '#000000';
$PAPER = '#FFFFFF';
// Spine colours paired with the colour of their lettering. The lettering is
// a soft grey rather than pure white, like a worn stamp.
$LEATHER = [
    ['#1C1C1C', '#BDBDBD'],
    ['#E6E6E6', '#2A2A2A'],
    ['#333333', '#CFCFCF'],
    ['#0A0A0A', '#BDBDBD'],
    ['#8C8C8C', '#141414'],
];

$SERIF = "Georgia, 'Palatino Linotype', 'Book Antiqua', Palatino, serif";

$W = 1200;
$H = 460;
$SHELF_Y = 400;   // Top of the shelf plank; every book stands on this line

// ---- One volume per module: [module number, subject, width, height] --------
// Subjects are short so every spine can use the same letter size.
$books = [
    ['1.2',  'GitHub',     50, 162],
    ['1.3',  'First Page', 46, 184],
    ['2.2',  'Loops',      54, 158],
    ['3',    'Functions',  50, 178],
    ['4.2',  'Strings',    48, 164],
    ['5 & 6','Classes',    66, 182],
    ['7.2',  'Forms',      52, 160],
    ['8',    'MySQL',      58, 172],
    ['9',    'Queries',    54, 166],
    ['10',   'JSON',       56, 170],
    ['11.2', 'PDF',        60, 180],
];

// ---- Window geometry --------------------------------------------------------
$WX  = 960;    // Centre line of the window (the candle stands on it)
$LW  = 94;     // Width of each lancet (tall pointed pane)
$GAP = 14;     // Stone mullion between the two lancets
$SPRING = 262; // Height where the pointed arches start to curve
$SILL   = 372;

function esc(string $s): string { return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8'); }

// Path for a pointed (equilateral) arch from x0 to x1, springing at $spring,
// running straight down to $bottom.
function lancet(float $x0, float $x1, float $spring, float $bottom): string {
    $w   = $x1 - $x0;
    $mid = ($x0 + $x1) / 2;
    $apex = $spring - $w * sqrt(3) / 2;
    return sprintf('M%s %s L%s %s A%s %s 0 0 1 %s %s A%s %s 0 0 1 %s %s L%s %s Z',
        $x0, $bottom, $x0, $spring, $w, $w, $mid, round($apex, 1), $w, $w, $x1, $spring, $x1, $bottom);
}

$leftL  = [$WX - $GAP / 2 - $LW, $WX - $GAP / 2];
$rightL = [$WX + $GAP / 2, $WX + $GAP / 2 + $LW];
$outer  = [$leftL[0] - 14, $rightL[1] + 14];
$outerSpring = $SPRING - 6;
$outerApex   = $outerSpring - ($outer[1] - $outer[0]) * sqrt(3) / 2;
$lancetApex  = $SPRING - $LW * sqrt(3) / 2;
// Round window (oculus) in the space between the two lancet tips and the outer arch.
$ocY = round(($lancetApex + $outerApex) / 2 + 8, 1);
$ocR = 24;

$glass = lancet($leftL[0], $leftL[1], $SPRING, $SILL) . ' ' . lancet($rightL[0], $rightL[1], $SPRING, $SILL);

$out = [];
$out[] = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 $W $H' width='$W' height='$H' role='img' aria-labelledby='t d'>";
$out[] = "<title id='t'>CSD 440: Server-Side Scripting with PHP</title>";
$out[] = "<desc id='d'>A black-and-white library at night: eleven volumes on a shelf, one for each module from 1.2 to 11.2, beside a Gothic window with the moon behind it and a lit candle in front.</desc>";

// ---- Gradients, patterns and animation --------------------------------------
$out[] = "<defs>"
    // Diamond leaded glass, the classic pattern of old college windows.
    . "<pattern id='lead' width='16' height='22' patternUnits='userSpaceOnUse'><path d='M0 0 L16 22 M16 0 L0 22' stroke='$PAPER' stroke-opacity='.28' stroke-width='1'/></pattern>"
    . "<radialGradient id='night' cx='" . ($leftL[0] + 24) . "' cy='" . ($SPRING - 30) . "' r='190' gradientUnits='userSpaceOnUse'><stop offset='0' stop-color='$PAPER' stop-opacity='.22'/><stop offset='1' stop-color='$PAPER' stop-opacity='.03'/></radialGradient>"
    . "<radialGradient id='glow' cx='$WX' cy='278' r='420' gradientUnits='userSpaceOnUse'><stop offset='0' stop-color='$PAPER' stop-opacity='.20'/><stop offset='.35' stop-color='$PAPER' stop-opacity='.06'/><stop offset='1' stop-color='$PAPER' stop-opacity='0'/></radialGradient>"
    // Shading laid over each spine so it reads as a rounded back.
    . "<linearGradient id='round' x1='0' y1='0' x2='1' y2='0'><stop offset='0' stop-color='$INK' stop-opacity='.55'/><stop offset='.28' stop-color='$PAPER' stop-opacity='.12'/><stop offset='.6' stop-color='$INK' stop-opacity='0'/><stop offset='1' stop-color='$INK' stop-opacity='.6'/></linearGradient>"
    . "<linearGradient id='wax' x1='0' y1='0' x2='1' y2='0'><stop offset='0' stop-color='#BDBDBD'/><stop offset='.4' stop-color='$PAPER'/><stop offset='1' stop-color='#9A9A9A'/></linearGradient>"
    . "<clipPath id='panes'><path d='$glass'/></clipPath>"
    . "</defs>";

$out[] = "<style>"
    . ".flame{transform-origin:{$WX}px 300px;animation:flicker 2.6s ease-in-out infinite}"
    . ".halo{animation:halo 2.6s ease-in-out infinite}"
    . "@keyframes flicker{0%,100%{transform:scale(1,1) skewX(0)}22%{transform:scale(.93,1.07) skewX(-3deg)}47%{transform:scale(1.04,.94) skewX(2deg)}71%{transform:scale(.97,1.04) skewX(-1deg)}}"
    . "@keyframes halo{0%,100%{opacity:.9}47%{opacity:.65}71%{opacity:1}}"
    . "@media (prefers-reduced-motion:reduce){.flame,.halo{animation:none}}"
    . "</style>";

// ---- Ground and frontispiece frame ------------------------------------------
$out[] = "<rect width='$W' height='$H' fill='$INK'/>";
$out[] = "<rect x='16' y='16' width='" . ($W - 32) . "' height='" . ($H - 32) . "' fill='none' stroke='$PAPER' stroke-width='2'/>"
    . "<rect x='23' y='23' width='" . ($W - 46) . "' height='" . ($H - 46) . "' fill='none' stroke='$PAPER' stroke-opacity='.55' stroke-width='.8'/>";

// ---- Title -----------------------------------------------------------------
$out[] = "<g font-family=\"$SERIF\" fill='$PAPER'>"
    . "<text x='66' y='78' font-size='18' font-style='italic' fill-opacity='.7'>Ex libris Miguel Fernandez</text>"
    . "<text x='62' y='156' font-size='90' letter-spacing='8'>CSD 440</text>"
    . "<text x='66' y='198' font-size='27' font-style='italic' fill-opacity='.9'>Server-Side Scripting with PHP</text>"
    . "</g>";

// ---- Gothic window ----------------------------------------------------------
$out[] = "<g>"
    // Night sky through the glass, with the moon in the left pane.
    . "<path d='$glass' fill='url(#night)'/>"
    . "<g clip-path='url(#panes)'>"
    . "<circle cx='" . ($leftL[0] + 28) . "' cy='" . ($SPRING - 22) . "' r='30' fill='$PAPER' fill-opacity='.08'/>"
    . "<circle cx='" . ($leftL[0] + 28) . "' cy='" . ($SPRING - 22) . "' r='17' fill='$PAPER' fill-opacity='.92'/>"
    . "<circle cx='" . ($leftL[0] + 34) . "' cy='" . ($SPRING - 26) . "' r='15' fill='$INK' fill-opacity='.85'/>"
    . "<rect x='" . $outer[0] . "' y='0' width='" . ($outer[1] - $outer[0]) . "' height='$SILL' fill='url(#lead)'/>"
    . "</g>"
    // Stone tracery: the two lancets, the outer arch, the oculus and the sill.
    . "<path d='$glass' fill='none' stroke='$PAPER' stroke-width='2.2'/>"
    . "<path d='" . lancet($outer[0], $outer[1], $outerSpring, $SILL) . "' fill='none' stroke='$PAPER' stroke-width='1.2' stroke-opacity='.75'/>"
    . "<circle cx='$WX' cy='$ocY' r='$ocR' fill='$PAPER' fill-opacity='.05' stroke='$PAPER' stroke-width='1.8'/>"
    . "<circle cx='$WX' cy='$ocY' r='" . ($ocR - 9) . "' fill='none' stroke='$PAPER' stroke-opacity='.5' stroke-width='1'/>"
    . "<rect x='" . ($outer[0] - 10) . "' y='$SILL' width='" . ($outer[1] - $outer[0] + 20) . "' height='7' fill='$PAPER' fill-opacity='.85'/>"
    . "</g>";

// ---- The shelf of volumes ---------------------------------------------------
$x = 64;
foreach ($books as $i => [$num, $title, $w, $h]) {
    $y = $SHELF_Y - $h;
    $cx = $x + $w / 2;
    [$col, $ink] = $LEATHER[$i % count($LEATHER)];

    $g  = "<g>";
    $g .= "<rect x='$x' y='$y' width='$w' height='$h' rx='3' fill='$col'/>";
    $g .= "<rect x='$x' y='$y' width='$w' height='$h' rx='3' fill='url(#round)'/>";
    // Dark spines get a hairline edge so they stand out against the black ground.
    if (hexdec(substr($col, 1, 2)) < 0x80) {
        $g .= "<rect x='" . ($x + .5) . "' y='" . ($y + .5) . "' width='" . ($w - 1) . "' height='" . ($h - 1) . "' rx='3' fill='none' stroke='$PAPER' stroke-opacity='.22'/>";
    }
    // Bands at the head and tail of the spine.
    foreach ([$y + 12, $y + 16, $y + 44, $SHELF_Y - 22, $SHELF_Y - 18] as $by) {
        $g .= "<rect x='" . ($x + 5) . "' y='$by' width='" . ($w - 10) . "' height='1.4' fill='$ink' fill-opacity='.8'/>";
    }
    // Module number between the head bands.
    $g .= "<text x='$cx' y='" . ($y + 35) . "' text-anchor='middle' font-family=\"$SERIF\" font-size='12' letter-spacing='1' fill='$ink'>" . esc($num) . "</text>";
    // Subject in spaced capitals, read top to bottom as on an English-language
    // spine, centred between the head and tail bands. Letter spacing also
    // adds a gap after the last letter, so shift back by half of it to centre.
    $top    = $y + 52;
    $bottom = $SHELF_Y - 28;
    $ty     = ($top + $bottom) / 2 + 1.5;
    $g .= "<text transform='translate($cx $ty) rotate(90)' text-anchor='middle' dominant-baseline='central' font-family=\"$SERIF\" font-size='11' letter-spacing='3' fill='$ink'>" . esc(strtoupper($title)) . "</text>";
    $g .= "</g>";
    $out[] = $g;

    $x += $w + 3;
}

// ---- Candle in front of the window ------------------------------------------
$cupY = $SHELF_Y - 58;
$out[] = "<ellipse cx='$WX' cy='" . ($SHELF_Y - 4) . "' rx='38' ry='7' fill='#2A2A2A' stroke='$PAPER' stroke-opacity='.6'/>"
    . "<rect x='" . ($WX - 5) . "' y='$cupY' width='10' height='54' fill='#1A1A1A' stroke='$PAPER' stroke-opacity='.5'/>"
    . "<ellipse cx='$WX' cy='$cupY' rx='24' ry='5.5' fill='#2A2A2A' stroke='$PAPER' stroke-opacity='.7'/>"
    . "<rect x='" . ($WX - 11) . "' y='312' width='22' height='" . ($cupY - 312) . "' fill='url(#wax)'/>"
    . "<rect x='" . ($WX - 1) . "' y='301' width='2' height='11' fill='#555'/>";

// Flame and the light it throws.
$out[] = "<rect class='halo' x='24' y='24' width='" . ($W - 48) . "' height='" . ($H - 48) . "' fill='url(#glow)'/>";
$out[] = "<g class='flame'>"
    . "<path d='M$WX 252 C" . ($WX + 12) . " 270 " . ($WX + 15) . " 287 $WX 304 C" . ($WX - 15) . " 287 " . ($WX - 12) . " 270 $WX 252 Z' fill='$PAPER' fill-opacity='.55'/>"
    . "<path d='M$WX 266 C" . ($WX + 7) . " 278 " . ($WX + 8) . " 290 $WX 301 C" . ($WX - 8) . " 290 " . ($WX - 7) . " 278 $WX 266 Z' fill='$PAPER'/>"
    . "</g>";

// ---- Shelf plank, drawn last so it sits in front of the books ---------------
$out[] = "<rect x='40' y='$SHELF_Y' width='" . ($W - 80) . "' height='18' fill='#1F1F1F'/>"
    . "<rect x='40' y='$SHELF_Y' width='" . ($W - 80) . "' height='1.6' fill='$PAPER' fill-opacity='.7'/>"
    . "<rect x='40' y='" . ($SHELF_Y + 18) . "' width='" . ($W - 80) . "' height='1' fill='$PAPER' fill-opacity='.25'/>";

$out[] = "</svg>";
echo implode("\n", $out), "\n";
