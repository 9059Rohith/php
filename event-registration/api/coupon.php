<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$action = clean_text(request('action', 'validate'));
$code = clean_text(request('code', ''));

if ($action === 'validate') {
    // Validate coupon code
    if (!$code) {
        json_response(['success' => false, 'message' => 'Coupon code is required'], 400);
    }
    
    $stmt = pdo()->prepare('SELECT * FROM coupons WHERE code = :code LIMIT 1');
    $stmt->execute([':code' => $code]);
    $coupon = $stmt->fetch();
    
    if (!$coupon) {
        json_response(['success' => false, 'message' => 'Invalid coupon code'], 404);
    }
    
    // Check if coupon is active
    if ($coupon['status'] !== 'active') {
        json_response(['success' => false, 'message' => 'This coupon is no longer active'], 400);
    }
    
    // Check coupon validity dates
    $now = new DateTime();
    $validFrom = new DateTime($coupon['valid_from']);
    $validTo = new DateTime($coupon['valid_to']);
    
    if ($now < $validFrom) {
        json_response(['success' => false, 'message' => 'This coupon is not yet valid'], 400);
    }
    
    if ($now > $validTo) {
        json_response(['success' => false, 'message' => 'This coupon has expired'], 400);
    }
    
    // Check usage limit
    if ($coupon['max_usage'] > 0) {
        $stmt = pdo()->prepare('SELECT COUNT(*) AS usage_count FROM coupon_usage WHERE coupon_id = :coupon_id');
        $stmt->execute([':coupon_id' => $coupon['id']]);
        $usageCount = (int) $stmt->fetch()['usage_count'];
        
        if ($usageCount >= $coupon['max_usage']) {
            json_response(['success' => false, 'message' => 'Coupon usage limit exceeded'], 400);
        }
    }
    
    // Success - return discount percentage
    json_response([
        'success' => true,
        'discount' => $coupon['discount_percent'],
        'message' => 'Coupon applied successfully',
    ]);
} else {
    json_response(['success' => false, 'message' => 'Invalid action'], 400);
}
