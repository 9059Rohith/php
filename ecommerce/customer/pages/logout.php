<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
ecommerce_logout();
redirect('/ecommerce/customer/pages/home.php');
