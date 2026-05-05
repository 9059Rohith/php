<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
event_logout();
redirect('/event-registration/public/home.php');
