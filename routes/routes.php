<?php

app()->setLocale('fr');

ini_set('memory_limit', '512M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

setlocale(LC_ALL, 'french', 'fr_CA.utf8');
setlocale(LC_NUMERIC, 'english', 'en_CA.utf8');

require_once(base_path('routes/routes_backend.php'));
require_once(base_path('routes/routes_frontend.php'));
require_once(base_path('routes/routes_core.php'));

require_once(base_path('routes/viewComposers_backend.php'));
require_once(base_path('routes/viewComposers_core.php'));
require_once(base_path('routes/viewComposers_frontend.php'));
