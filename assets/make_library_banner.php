<?php
/*
   Program Name: make_library_banner.php
   Author:       Miguel Fernandez
   Course:       CSD440
   Description:  Generates assets/library-banner.svg, the banner at the top
                 of the README. It is drawn like a vanitas still life on a
                 book's frontispiece: a double-ruled frame, the course title,
                 a shelf with one volume per module (each stamped with its
                 module number and subject) and, at the end of the shelf, a
                 skull resting on two books beside a candle with a rose laid
                 in front. Everything is black, white and grey except the
                 rose. GitHub does not load web fonts inside README images,
                 so the text uses serif fonts that ship with every operating
                 system. The only animation is the candle flame, and it stops
                 for viewers who prefer reduced motion.
   Usage:        php assets/make_library_banner.php > assets/library-banner.svg
   AI Use:       Claude Code (Anthropic) was used to help design and write
                 this script.
*/

// ---- Palette: black, white and greys, with one crimson rose ----------------
$INK   = '#000000';
$PAPER = '#FFFFFF';
$BONE  = '#E9E4DA';
// Spine colours paired with the colour of their lettering. The lettering is
// a soft grey rather than pure white, like a worn stamp.
$LEATHER = [
    ['#1C1C1C', '#BDBDBD'],
    ['#E6E6E6', '#2A2A2A'],
    ['#333333', '#CFCFCF'],
    ['#0A0A0A', '#BDBDBD'],
    ['#8C8C8C', '#141414'],
];
// Rose petals from shadow to highlight.
$ROSE = ['#3E0710', '#6B0F1C', '#8E1626', '#B02A38'];

$SERIF = "Georgia, 'Palatino Linotype', 'Book Antiqua', Palatino, serif";

$W = 1200;
$H = 460;
$SHELF_Y = 400;   // Top of the shelf plank; every book stands on this line

// ---- One volume per module: [module number, subject, width, height] --------
// Subjects are short so every spine can use the same letter size.
$books = [
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

// ---- Still life geometry ----------------------------------------------------
$SX = 935;     // Centre line of the skull
$CX = 1112;    // Centre line of the candle
// Two books lying flat at the end of the shelf: [x, width, height, colour, lettering, title]
$stack = [
    [790, 300, 38, '#1C1C1C', '#BDBDBD', 'VITA BREVIS'],
    [818, 240, 30, '#D9D9D9', '#2A2A2A', 'ARS LONGA'],
];
$stackTop = $SHELF_Y - $stack[0][2] - $stack[1][2];

function esc(string $s): string { return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8'); }

// Mirror a path written for the left side of the skull onto the right side.
function mirror(string $d): string {
    return preg_replace_callback('/(-?\d+(?:\.\d+)?) (-?\d+(?:\.\d+)?)/', function ($m) {
        return (-(float)$m[1]) . ' ' . $m[2];
    }, $d);
}

$out = [];
$out[] = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 $W $H' width='$W' height='$H' role='img' aria-labelledby='t d'>";
$out[] = "<title id='t'>CSD 440: Server-Side Scripting with PHP</title>";
$out[] = "<desc id='d'>A black-and-white library by candlelight: ten volumes on a shelf, one for each module from 1.3 to 11.2, beside a skull resting on two books with a single crimson rose laid in front of it.</desc>";

// ---- Gradients and animation ------------------------------------------------
$out[] = "<defs>"
    . "<radialGradient id='glow' cx='$CX' cy='250' r='520' gradientUnits='userSpaceOnUse'><stop offset='0' stop-color='$PAPER' stop-opacity='.22'/><stop offset='.3' stop-color='$PAPER' stop-opacity='.07'/><stop offset='1' stop-color='$PAPER' stop-opacity='0'/></radialGradient>"
    // Shading laid over each spine so it reads as a rounded back.
    . "<linearGradient id='round' x1='0' y1='0' x2='1' y2='0'><stop offset='0' stop-color='$INK' stop-opacity='.55'/><stop offset='.28' stop-color='$PAPER' stop-opacity='.12'/><stop offset='.6' stop-color='$INK' stop-opacity='0'/><stop offset='1' stop-color='$INK' stop-opacity='.6'/></linearGradient>"
    // The same rounding turned on its side for the books lying flat.
    . "<linearGradient id='roundFlat' x1='0' y1='0' x2='0' y2='1'><stop offset='0' stop-color='$PAPER' stop-opacity='.14'/><stop offset='.45' stop-color='$INK' stop-opacity='0'/><stop offset='1' stop-color='$INK' stop-opacity='.6'/></linearGradient>"
    // Bone lit from the candle on the right.
    . "<linearGradient id='bone' x1='0' y1='0' x2='1' y2='.3'><stop offset='0' stop-color='#6E6A64'/><stop offset='.55' stop-color='#CFC9BE'/><stop offset='1' stop-color='$BONE'/></linearGradient>"
    . "<radialGradient id='petal' cx='.5' cy='.5' r='.6'><stop offset='0' stop-color='{$ROSE[3]}'/><stop offset='.7' stop-color='{$ROSE[1]}'/><stop offset='1' stop-color='{$ROSE[0]}'/></radialGradient>"
    . "<linearGradient id='wax' x1='0' y1='0' x2='1' y2='0'><stop offset='0' stop-color='#9A9A9A'/><stop offset='.6' stop-color='$PAPER'/><stop offset='1' stop-color='#CFCFCF'/></linearGradient>"
    . "</defs>";

$out[] = "<style>"
    . ".flame{transform-origin:{$CX}px 254px;animation:flicker 2.6s ease-in-out infinite}"
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
$out[] = "<text x='" . ($W - 66) . "' y='78' text-anchor='end' font-family=\"$SERIF\" font-size='15' font-style='italic' letter-spacing='4' fill='$PAPER' fill-opacity='.55'>memento mori</text>";

// Candlelight, drawn under everything else in the scene.
$out[] = "<rect class='halo' x='24' y='24' width='" . ($W - 48) . "' height='" . ($H - 48) . "' fill='url(#glow)'/>";

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
    // spine, centred between the head and tail bands.
    $top    = $y + 52;
    $bottom = $SHELF_Y - 28;
    $ty     = ($top + $bottom) / 2 + 1.5;
    $g .= "<text transform='translate($cx $ty) rotate(90)' text-anchor='middle' dominant-baseline='central' font-family=\"$SERIF\" font-size='11' letter-spacing='3' fill='$ink'>" . esc(strtoupper($title)) . "</text>";
    $g .= "</g>";
    $out[] = $g;

    $x += $w + 3;
}

// ---- Two books lying flat ---------------------------------------------------
$y = $SHELF_Y;
foreach ($stack as [$bx, $bw, $bh, $col, $ink, $title]) {
    $y -= $bh;
    $g  = "<g>";
    $g .= "<rect x='$bx' y='$y' width='$bw' height='$bh' rx='3' fill='$col'/>";
    $g .= "<rect x='$bx' y='$y' width='$bw' height='$bh' rx='3' fill='url(#roundFlat)'/>";
    if (hexdec(substr($col, 1, 2)) < 0x80) {
        $g .= "<rect x='" . ($bx + .5) . "' y='" . ($y + .5) . "' width='" . ($bw - 1) . "' height='" . ($bh - 1) . "' rx='3' fill='none' stroke='$PAPER' stroke-opacity='.22'/>";
    }
    foreach ([$bx + 12, $bx + 16, $bx + $bw - 17, $bx + $bw - 13] as $bandX) {
        $g .= "<rect x='$bandX' y='" . ($y + 5) . "' width='1.4' height='" . ($bh - 10) . "' fill='$ink' fill-opacity='.8'/>";
    }
    $g .= "<text x='" . ($bx + $bw / 2) . "' y='" . ($y + $bh / 2) . "' text-anchor='middle' dominant-baseline='central' font-family=\"$SERIF\" font-size='11' letter-spacing='4' fill='$ink'>" . esc($title) . "</text>";
    $g .= "</g>";
    $out[] = $g;
}

// ---- Skull ------------------------------------------------------------------
// Drawn facing forward around (0, 0), with the jaw resting on the top book.
// Left-hand features are written once and mirrored for the right.
$cranium = "M-62 4 C-68 -52 -36 -86 0 -86 C36 -86 68 -52 62 4 C61 16 68 26 59 36 C52 44 45 46 41 60 L-41 60 C-45 46 -52 44 -59 36 C-68 26 -61 16 -62 4 Z";
$jaw     = "M-47 58 C-49 78 -38 97 0 100 C38 97 49 78 47 58 C41 66 35 72 29 72 L-29 72 C-35 72 -41 66 -47 58 Z";
$eye     = "M-47 -4 C-48 -20 -31 -24 -17 -17 C-7 -12 -7 4 -11 12 C-17 23 -40 23 -45 12 C-47 7 -47 1 -47 -4 Z";
$nose    = "M0 18 C-4 26 -11 35 -10 42 C-8 47 -3 45 0 40 C3 45 8 47 10 42 C11 35 4 26 0 18 Z";
// Shadow under the cheekbone and in the temple.
$cheek   = "M-59 36 C-52 30 -44 32 -38 40 C-40 48 -42 54 -41 60 C-45 46 -52 44 -59 36 Z";
$temple  = "M-62 4 C-63 -14 -60 -26 -54 -34 C-56 -20 -55 -6 -50 8 C-55 8 -59 6 -62 4 Z";

$skullY = $stackTop - 100;
$g  = "<g transform='translate($SX $skullY)'>";
$g .= "<ellipse cx='0' cy='99' rx='58' ry='4' fill='$INK' fill-opacity='.55'/>";
$g .= "<path d='$jaw' fill='url(#bone)' stroke='$INK' stroke-opacity='.6'/>";
$g .= "<path d='$cranium' fill='url(#bone)' stroke='$INK' stroke-opacity='.6'/>";
foreach ([$cheek, $temple] as $d) {
    $g .= "<path d='$d' fill='$INK' fill-opacity='.35'/><path d='" . mirror($d) . "' fill='$INK' fill-opacity='.12'/>";
}
// Coronal suture across the crown.
$g .= "<path d='M-40 -66 C-30 -60 -26 -68 -16 -62 C-8 -57 -4 -66 4 -61 C12 -56 16 -66 26 -61 C33 -57 37 -64 42 -64' fill='none' stroke='$INK' stroke-opacity='.4' stroke-width='1.1'/>";
$g .= "<path d='$eye' fill='#0A0A0A'/><path d='" . mirror($eye) . "' fill='#0A0A0A'/>";
// A glint of candlelight in the far edge of each socket.
$g .= "<path d='M-14 -10 C-10 -4 -10 4 -13 10' fill='none' stroke='$PAPER' stroke-opacity='.18' stroke-width='1.5'/>";
$g .= "<path d='M40 -8 C44 -2 44 6 41 12' fill='none' stroke='$PAPER' stroke-opacity='.3' stroke-width='1.5'/>";
$g .= "<path d='$nose' fill='#0A0A0A'/>";
// Upper and lower teeth.
foreach ([[60, 69], [70, 79]] as [$t0, $t1]) {
    $g .= "<rect x='-30' y='$t0' width='60' height='" . ($t1 - $t0) . "' rx='2' fill='#D8D2C6'/>";
    for ($tx = -30; $tx <= 30; $tx += 7.5) {
        $g .= "<line x1='$tx' y1='$t0' x2='$tx' y2='$t1' stroke='$INK' stroke-opacity='.65' stroke-width='1'/>";
    }
}
$g .= "<line x1='-31' y1='69.5' x2='31' y2='69.5' stroke='$INK' stroke-opacity='.8' stroke-width='1.2'/>";
$g .= "</g>";
$out[] = $g;

// ---- Candle -----------------------------------------------------------------
$cupY = $SHELF_Y - 54;
$out[] = "<ellipse cx='$CX' cy='" . ($SHELF_Y - 4) . "' rx='34' ry='6' fill='#2A2A2A' stroke='$PAPER' stroke-opacity='.6'/>"
    . "<rect x='" . ($CX - 5) . "' y='$cupY' width='10' height='50' fill='#1A1A1A' stroke='$PAPER' stroke-opacity='.5'/>"
    . "<ellipse cx='$CX' cy='$cupY' rx='22' ry='5' fill='#2A2A2A' stroke='$PAPER' stroke-opacity='.7'/>"
    . "<rect x='" . ($CX - 10) . "' y='266' width='20' height='" . ($cupY - 266) . "' fill='url(#wax)'/>"
    // A drip of wax down the shadowed side.
    . "<path d='M" . ($CX - 10) . " 266 L" . ($CX - 4) . " 266 L" . ($CX - 4) . " 284 C" . ($CX - 4) . " 290 " . ($CX - 10) . " 290 " . ($CX - 10) . " 284 Z' fill='#BDBDBD'/>"
    . "<rect x='" . ($CX - 1) . "' y='256' width='2' height='10' fill='#555'/>";
$out[] = "<g class='flame'>"
    . "<path d='M$CX 208 C" . ($CX + 11) . " 225 " . ($CX + 14) . " 241 $CX 258 C" . ($CX - 14) . " 241 " . ($CX - 11) . " 225 $CX 208 Z' fill='$PAPER' fill-opacity='.55'/>"
    . "<path d='M$CX 221 C" . ($CX + 7) . " 233 " . ($CX + 8) . " 244 $CX 255 C" . ($CX - 8) . " 244 " . ($CX - 7) . " 233 $CX 221 Z' fill='$PAPER'/>"
    . "</g>";

// ---- Rose, laid along the top book in front of the skull --------------------
// The stem and leaves stay grey so the bloom is the only colour in the banner.
$RX = 1022;
$RY = $stackTop - 14;
$stemY = $stackTop - 2;   // The stem rests along the top edge of the book
$stem = "M" . ($RX - 16) . " " . ($RY + 10) . " C970 " . ($stemY + 2) . " 900 " . ($stemY - 3) . " 836 $stemY";
$g  = "<g>";
$g .= "<path d='$stem' fill='none' stroke='#5A5A5A' stroke-width='3.2' stroke-linecap='round'/>";
foreach ([968, 912, 866] as $tx) {
    $ty = $stemY - 1;
    $g .= "<path d='M$tx $ty l-3 -6 l-3 6 Z' fill='#5A5A5A'/>";
}
// Two leaves rising from the stem, clear of the lettering on the book.
foreach ([[946, $stemY - 1, 32], [884, $stemY - 1, 148]] as [$lx, $ly, $rot]) {
    $g .= "<g transform='translate($lx $ly) rotate($rot)'>"
        . "<path d='M0 0 C-8 -9 -26 -10 -34 0 C-26 10 -8 9 0 0 Z' fill='#3A3A3A' stroke='#7A7A7A' stroke-width='.8'/>"
        . "<path d='M0 0 L-32 0' stroke='#7A7A7A' stroke-width='.7'/>"
        . "</g>";
}
// Sepals where the bloom meets the stem.
foreach ([-40, 0, 40] as $a) {
    $g .= "<path transform='translate(" . ($RX - 14) . " " . ($RY + 10) . ") rotate($a)' d='M0 0 C-6 -2 -12 1 -16 6 C-10 5 -5 4 0 0 Z' fill='#4A4A4A'/>";
}
// The bloom: an outer ring of five petals, an inner ring of five turned
// between them, and a tight spiral at the heart.
$g .= "<g transform='translate($RX $RY) scale(1.3)'>";
for ($k = 0; $k < 5; $k++) {
    $a = $k * 72 - 90;
    $px = round(cos(deg2rad($a)) * 11, 1);
    $py = round(sin(deg2rad($a)) * 11, 1);
    $g .= "<ellipse cx='$px' cy='$py' rx='16' ry='12.5' transform='rotate(" . ($a + 90) . " $px $py)' fill='url(#petal)' stroke='{$ROSE[0]}' stroke-width='1'/>";
}
for ($k = 0; $k < 5; $k++) {
    $a = $k * 72 - 54;
    $px = round(cos(deg2rad($a)) * 6, 1);
    $py = round(sin(deg2rad($a)) * 6, 1);
    $g .= "<ellipse cx='$px' cy='$py' rx='10.5' ry='8' transform='rotate(" . ($a + 90) . " $px $py)' fill='{$ROSE[2]}' stroke='{$ROSE[0]}' stroke-width='1'/>";
}
$g .= "<circle r='7.5' fill='{$ROSE[1]}'/>";
$g .= "<path d='M0 0 C3 -2 4 2 1 4 C-3 6 -6 1 -4 -3 C-1 -8 6 -7 7 -1 C8 5 2 9 -3 8' fill='none' stroke='{$ROSE[0]}' stroke-width='1.4' stroke-linecap='round'/>";
$g .= "</g></g>";
$out[] = $g;

// ---- Shelf plank, drawn last so it sits in front of everything ---------------
$out[] = "<rect x='40' y='$SHELF_Y' width='" . ($W - 80) . "' height='18' fill='#1F1F1F'/>"
    . "<rect x='40' y='$SHELF_Y' width='" . ($W - 80) . "' height='1.6' fill='$PAPER' fill-opacity='.7'/>"
    . "<rect x='40' y='" . ($SHELF_Y + 18) . "' width='" . ($W - 80) . "' height='1' fill='$PAPER' fill-opacity='.25'/>";

$out[] = "</svg>";
echo implode("\n", $out), "\n";
