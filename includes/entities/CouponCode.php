<?php

require_once __DIR__.'/../db.php';

require_once __DIR__.'/Cart.php';

class CouponCode {


    public static function get_coupon_details($couponId) {
        try {
            global $conn;
            $data = $conn->query("SELECT * FROM coupon_codes WHERE code = '$couponId'")->fetch();
            return $data;
        } catch (Exception $e) {
            var_dump($e);
            return null;
        }
    }


    public static function use_coupon($cart, $coupon) {
        if ($coupon['type'] == 'FIXED') {
            return self::fixed_coupon($cart, $coupon);
        } else if ($coupon['type'] == 'PERCENT') {
            return self::percent_coupon($cart, $coupon);
        } else if ($coupon['type'] == 'MIXED'){
            return self::mixed_coupon($cart, $coupon);
        } else {
            return ['status' => false, 'data' => 'Invalid coupon code '.$coupon['code']];
        }
    }

    private static function percent_coupon($cart, $coupon) {

        // Get cart items
        $cartItems = Cart::get_cart_items($cart['cart_id']);

        $itemRange = $coupon['min_item'];
        $amountRange = $coupon['min_amount'];

        if ((count($cartItems) >= $itemRange) && $cart['total_amount'] >= $amountRange) {
            $percentage = (int) $coupon['value'];
            $percentageAmount = ($percentage/100) * $cart['total_amount'];

            // echo "Percentage: $percentage";
            // echo "Percentage Amount: $percentageAmount";
    
            $amountPayable = $cart['total_amount'] - $percentageAmount;
            $cart['amount_payable'] = $amountPayable;
            $cart['coupon_code'] = $coupon['code'];
    
            return ['status' => true, 'data' => ['cart' => $cart]];
        } else {
            return ['status' => false, 'data' => "Coupon terms not met\nMin Items: $itemRange\nMin Payable Amount: $amountRange"];
        }
    }
    private static function fixed_coupon($cart, $coupon) {
        $cartItems = Cart::get_cart_items($cart['cart_id']);

        $itemRange = $coupon['min_item'];
        $amountRange = $coupon['min_amount'];

        if ((count($cartItems) >= $itemRange) && $cart['total_amount'] >= $amountRange) {
            $discountAmount = $coupon['value'];

            $amountPayable = $cart['total_amount'] - $discountAmount;
            $cart['amount_payable'] = $amountPayable;
            $cart['coupon_code'] = $coupon['code'];

            return ['status' => true, 'data' => ['cart' => $cart]];
        } else {
            return ['status' => false, 'data' => "Coupon terms not met\nMin Items: $itemRange\nMin Payable Amount: $amountRange"];
        }
    }
    private static function mixed_coupon($cart, $coupon) {

    }
}