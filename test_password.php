<?php
$hash1 = '$2y$10$gvjTFbcxxfu8oMTuCpswj.OPp0oPvbUACV2aCtwK7XtbOZ6007x8e';
$hash2 = '$2y$10$Th8JdYInSMnBd62OGSX2wezUsOaKyFBa2Mw.DgKsziBiCEtGYH3bi';

echo "Testing doctor1 (password123): ";
var_dump(password_verify('password123', $hash1));

echo "Testing admin1 (admin123): ";
var_dump(password_verify('admin123', $hash2));
?>
