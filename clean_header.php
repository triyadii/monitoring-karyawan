<?php

$lines = file('resources/views/layouts/header.blade.php');
$out = [];
foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    if ($lineNum >= 24 && $lineNum <= 3073) {
        continue;
    } // remove menu wrapper
    if ($lineNum >= 3076 && $lineNum <= 4561) {
        continue;
    } // remove extra navbar items
    $out[] = $line;
}
file_put_contents('resources/views/layouts/header.blade.php', implode('', $out));
echo "Header cleaned.\n";
