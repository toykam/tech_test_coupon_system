<?php

require_once __DIR__.'/../includes/entities/CouponCode.php';
require_once __DIR__.'/../includes/entities/Cart.php';
// require_once __DIR__.'/../includes/db.php';
$response = ['status' => false, 'message' => 'An error occurred, Please try again later'];
try {
    if (isset($_POST)) {
        $couponCode = $_POST['code'];
        $cart_id = $_POST['cart_id'];

        // Get Cart Details To validate cart information
        $cartDetails = Cart::get_cart_detail($cart_id);
    
        if ($cartDetails) {
            // There is cart in the database
            // Check if it already has a coupon code
            // if (in_array($cartDetails['coupon_code'], [null, '0', ''])) {
            //     // Cart Has No Coupon Code
            //     // Get coupon detail based on the coupon provided by the user
            // } else {
            //     $response['message'] = 'Cart already has a coupon code applied already';
            // }
            $couponDetail = CouponCode::get_coupon_details($couponCode);
            // $conn->pdo->beginTransaction();
            if ($couponDetail) {
                // Do calculation
                $result = CouponCode::use_coupon($cartDetails, $couponDetail);
                if ($result['status'] == true) {
                    // Update cart
                    // var_dump($result['data']['cart']);
                    Cart::update_cart_details($cartDetails['cart_id'], $result['data']['cart']);
                    $response['status'] = true;
                    $response['data'] = $result['data'];
                    $response['message'] = "Coupon Code: {$couponDetail['code']} applied";
                } else {
                    $response['message'] = $result['data'];
                }
            } else {
                $response['message'] = 'Invalid coupon code, Please check and try again';
            }
        } else {
            $response['message'] = 'An error occurred, Please try again later';
        }
    } else {
        $response['message'] = 'An error occurred, Please try again later';
    }
    echo json_encode($response);
} catch (Exception $e) {
    $response['message'] = "Error: {$e->getMessage()}";
    echo json_encode($response);
    // throw $e;
}