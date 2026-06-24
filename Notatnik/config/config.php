<?php

define('DB_DRIVER', 'sqlite');
define('DB_HOST', '');
define('DB_NAME', __DIR__ . '/../database/database.sqlite');
define('DB_USER', '');
define('DB_PASS', '');

define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('BASE_URL', '/notatnik/public/');

define('TITLE_MIN', 3);
define('TITLE_MAX', 100);
define('TAG_MAX', 50);
define('PREVIEW_LENGTH', 600);

define('PRIORITIES', ['niski', 'średni', 'wysoki']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
