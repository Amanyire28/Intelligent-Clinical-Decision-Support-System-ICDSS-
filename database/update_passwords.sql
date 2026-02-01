-- Update password hashes for demo users
UPDATE users SET password_hash = '$2y$10$gvjTFbcxxfu8oMTuCpswj.OPp0oPvbUACV2aCtwK7XtbOZ6007x8e' WHERE username = 'doctor1';
UPDATE users SET password_hash = '$2y$10$Th8JdYInSMnBd62OGSX2wezUsOaKyFBa2Mw.DgKsziBiCEtGYH3bi' WHERE username = 'admin1';
