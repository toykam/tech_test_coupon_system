<?php

require_once __DIR__.'/../db.php';
require_once __DIR__.'/../Medoo.php';

use Medoo\Medoo;

class Cart {


    public static function get_user_cart() {
        try {
            global $conn;
            $data = $conn->query("SELECT * FROM cart")->fetch();
            // $data = $conn->select('cart', ['cart_id', 'user_id', 'coupon_code', 'total_amount', 'amount_payable', 'id']);
            return $data;
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    public static function get_cart_detail($cartId) {
        try {
            global $conn;
            $data = $conn->query("SELECT * FROM cart WHERE cart_id = '$cartId'")->fetchObject();
            // $data = $conn->select('cart', ['cart_id', 'user_id', 'coupon_code', 'total_amount', 'amount_payable', 'id']);
            return (array)$data;
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    public static function get_cart_items($cartId) {
        global $conn;
        $data = $conn->query("SELECT * FROM cart_items JOIN products ON products.product_id = cart_items.item_id WHERE cart_id = '$cartId'")->fetchAll();
        return $data;
    }


    public static function update_cart_details($cartId, $cartData) {
        global $conn;
        // $str = self::generateSql($cartData);
        // $data = $conn->query("UPDATE cart SET $str WHERE cart_id = '$cartId'");
        $data = $conn->update('cart', $cartData, ['cart_id' => $cartId]);

        echo $conn->error;
        return $data;
    }

    // static function generateSql ($data) {
    //     var_dump($data);
    //     $str = '';
    //     foreach ($data as $key=>$value) {
    //         $str .= "$key='$value',";
    //     }
    //     return trim($str, ',');
    // }
}