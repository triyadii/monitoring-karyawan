<?php

$lines = file('resources/views/layouts/sidebar.blade.php');
$out = [];
$inMenu = false;
foreach ($lines as $i => $line) {
    $lineNum = $i + 1;

    if ($lineNum >= 37 && $lineNum <= 3506) {
        if ($lineNum == 37) {
            $out[] = '										<!--begin:Menu item-->
										<div class="menu-item pt-5">
											<!--begin:Menu content-->
											<div class="menu-content">
												<span class="menu-heading fw-bold text-uppercase fs-7">Main</span>
											</div>
											<!--end:Menu content-->
										</div>
										<!--end:Menu item-->
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link" href="/dashboard">
												<span class="menu-icon">
													<i class="ki-duotone ki-element-11 fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
														<span class="path3"></span>
														<span class="path4"></span>
													</i>
												</span>
												<span class="menu-title">Dashboard</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
';
        }

        continue;
    }

    $out[] = $line;
}
file_put_contents('resources/views/layouts/sidebar.blade.php', implode('', $out));
echo "Sidebar cleaned.\n";
