<?php
/**
 * Shared bootstrap for every api/*.php endpoint.
 * Sets JSON + no-cache headers and loads the DB connection.
 * (config/install.php doesn't need to run here — it only needs to run
 * once, from index.php, before any API calls happen.)
 */

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
