<?php
declare(strict_types=1);

require_once __DIR__ . '/../customer/includes/bootstrap.php';
$code = clean_text(request('code', ''));
$stmt = pdo()->prepare('SELECT * FROM coupons WHERE code = :code AND status = "active" LIMIT 1');
$stmt->execute([':code' => $code]);
$coupon = $stmt->fetch();

if (!$coupon) {
    json_response(['success' => false, 'message' => 'Invalid coupon']);
}

json_response(['success' => true, 'message' => 'Coupon applied', 'coupon' => $coupon]);
