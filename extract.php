<?php

$lines = file('resources/views/dashboard.blade.php');

$header = array_slice($lines, 51, 4835 - 52 + 1); // lines are 0-indexed
$sidebar = array_slice($lines, 4837, 8363 - 4838 + 1);
$footer = array_slice($lines, 10563, 10590 - 10564 + 1);
$content = array_slice($lines, 8409, 10561 - 8410 + 1);

// Save partials
file_put_contents('resources/views/layouts/header.blade.php', implode('', $header));
file_put_contents('resources/views/layouts/sidebar.blade.php', implode('', $sidebar));
file_put_contents('resources/views/layouts/footer.blade.php', implode('', $footer));

// Build new app.blade.php
$appLines = [];
$inHeader = false;
$inSidebar = false;
$inFooter = false;
$inContent = false;

foreach ($lines as $i => $line) {
    $lineNum = $i + 1;

    // Check ranges
    if ($lineNum == 52) {
        $appLines[] = "\t\t\t\t\t@include('layouts.header')\n";
        $inHeader = true;
    }
    if ($lineNum == 4838) {
        $appLines[] = "\t\t\t\t\t@include('layouts.sidebar')\n";
        $inSidebar = true;
    }
    if ($lineNum == 8410) {
        $appLines[] = "\t\t\t\t\t@yield('content')\n";
        $inContent = true;
    }
    if ($lineNum == 10564) {
        $appLines[] = "\t\t\t\t\t@include('layouts.footer')\n";
        $inFooter = true;
    }

    if (! $inHeader && ! $inSidebar && ! $inFooter && ! $inContent) {
        $appLines[] = $line;
    }

    if ($lineNum == 4835) {
        $inHeader = false;
    }
    if ($lineNum == 8363) {
        $inSidebar = false;
    }
    if ($lineNum == 10561) {
        $inContent = false;
    }
    if ($lineNum == 10590) {
        $inFooter = false;
    }
}
file_put_contents('resources/views/layouts/app.blade.php', implode('', $appLines));

// Build new dashboard.blade.php
$dashContent = "@extends('layouts.app')\n\n@section('content')\n".implode('', $content)."\n@endsection\n";
file_put_contents('resources/views/dashboard.blade.php', $dashContent);
echo 'Extraction completed.';
