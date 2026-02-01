-- Update password hash for admin1
UPDATE users SET password_hash = '$2y$10$xMsP0dkhiZtuY17lWDhL/ezxeo3tk/FGlfzdoVp9wslWewHpj220a' WHERE username = 'admin1';
