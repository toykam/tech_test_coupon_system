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
            // $percentage = (int) $coupon['value'];
            // $percentageAmount = ($percentage/100) * $cart['total_amount'];

            // echo "Percentage: $percentage";
            // echo "Percentage Amount: $percentageAmount";
    
            $amountPayable = self::get_discount_amount('PERCENT', $coupon['value'], $cart['total_amount']);
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
            
            $amountPayable = self::get_discount_amount('FIXED', $coupon['value'], $cart['total_amount']);;
            $cart['amount_payable'] = $amountPayable;
            $cart['coupon_code'] = $coupon['code'];

            return ['status' => true, 'data' => ['cart' => $cart]];
        } else {
            return ['status' => false, 'data' => "Coupon terms not met\nMin Items: $itemRange\nMin Payable Amount: $amountRange"];
        }
    }
    private static function mixed_coupon($cart, $coupon) {
        $cartItems = Cart::get_cart_items($cart['cart_id']);

        $itemRange = $coupon['min_item'];
        $amountRange = $coupon['min_amount'];

        if ((count($cartItems) >= $itemRange) && $cart['total_amount'] >= $amountRange) {
            // check for percent
            $percentAmount = self::get_discount_amount('PERCENT', $coupon['value'], $cart['total_amount']);
            // check for fixed
            $fixedAmount = self::get_discount_amount('FIXED', $coupon['value'], $cart['total_amount']);
            // compare result
            // echo "PERCENT AMOUNT: $percentAmount";
            // echo "FIXED AMOUNT: $fixedAmount";
            if (($cart['total_amount'] - $percentAmount) > ($cart['total_amount'] - $fixedAmount)) {
                // Use Percentage Amount
                $cart['amount_payable'] = $percentAmount;
                $cart['coupon_code'] = $coupon['code'];
            } else {
                // Use Fixed Amount
                $cart['amount_payable'] = $fixedAmount;
                $cart['coupon_code'] = $coupon['code'];
            }

            return ['status' => true, 'data' => ['cart' => $cart]];
        } else {
            return ['status' => false, 'data' => "Coupon terms not met\nMin Items: $itemRange\nMin Payable Amount: $amountRange"];
        }
    }

    private static function get_discount_amount($type, $value, $amount) {
        if ($type == 'FIXED') {
            return $amount - $value;
        } else if ($type == 'PERCENT') {
            $percentage = (int) $value;
            $percentageAmount = ($percentage/100) * $amount;
            return ($amount - $percentageAmount);
        } else {
            return 0;
        }
    }
}