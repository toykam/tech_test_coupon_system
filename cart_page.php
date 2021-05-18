<?php

    require_once 'includes/entities/Cart.php';
    require_once 'includes/entities/CouponCode.php';

    $cart = Cart::get_user_cart();
    $cartItems = [];

    $totalPrice = 0;

    $discountAmount = 0;

    $couponDetails = null;

    if ($cart) {
        $cartItems = Cart::get_cart_items($cart['cart_id']);

        // $totalPrice = array_reduce($cartItems, function($a, $b) {
        //     // return ($a['item_total'] * $a['price']) + ($b['quantity'] * $b['price']);
        //     return $a += ((int)$b['item_total']);
        // }, 0);
        $totalPrice = $cart['total_amount'];

        $discountAmount = $cart['total_amount'] - $cart['amount_payable'];

        if (!in_array($cart['coupon_code'], [null, '0', ''])) {
            $couponDetails = CouponCode::get_coupon_details($cart['coupon_code']);
        }
    }
?>


<div class="px-4 px-lg-0">
  <!-- For demo purpose -->
  <div class="container text-white py-5 text-center">
    <h1 class="display-4"></h1>
    <p class="lead mb-0">Cart Page With Coupon</p>
    
  </div>
  <!-- End -->

  <div class="pb-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 p-5 bg-white rounded shadow-sm mb-5">

          <!-- Shopping cart table -->
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col" class="border-0 bg-light">
                    <div class="p-2 px-3 text-uppercase">Product</div>
                  </th>
                  <th scope="col" class="border-0 bg-light">
                    <div class="py-2 text-uppercase">Price</div>
                  </th>
                  <th scope="col" class="border-0 bg-light">
                    <div class="py-2 text-uppercase">Quantity</div>
                  </th>
                  <th scope="col" class="border-0 bg-light">
                    <div class="py-2 text-uppercase">Subtotal</div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php if ($cart) { ?>
                    <input type="hidden" value="<?php echo $cart['cart_id']; ?>" id="cart-id"/>
                <?php } ?>
                <?php foreach ($cartItems as $key => $cart) { ?>
                    <tr>
                    <th scope="row" class="border-0">
                        <div class="p-2">
                        <img src="<?php echo $cart['image_url'] ?>" alt="" width="70" class="img-fluid rounded shadow-sm">
                        <div class="ml-3 d-inline-block align-middle">
                            <h5 class="mb-0"> <a href="#" class="text-dark d-inline-block align-middle"><?php echo $cart['title'] ?></a></h5><span class="text-muted font-weight-normal font-italic d-block">Category: Watches</span>
                        </div>
                        </div>
                    </th>
                    <td class="border-0 align-middle"><strong>N <?php echo $cart['price'] ?></strong></td>
                    <td class="border-0 align-middle"><strong><?php echo $cart['quantity'] ?></strong></td>
                    <td class="border-0 align-middle"><strong>N <?php echo ($cart['quantity'] * $cart['price']) ?></strong></td>
                    </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- End -->
        </div>
      </div>

      <div class="row py-5 p-4 bg-white rounded shadow-sm">
        <div class="col-lg-6">
          <div class="bg-light rounded-pill px-4 py-3 text-uppercase font-weight-bold">Coupon code</div>
          <div class="p-4">
            <p class="font-italic mb-4">If you have a coupon code, please enter it in the box below</p>
            <p class='alert' id='coupon-status'></p>
            <div class="input-group mb-4 border rounded-pill p-2">
              <input id="coupon-code" type="text" placeholder="Apply coupon" aria-describedby="apply-coupon-button" class="form-control border-0">
              <div class="input-group-append border-0">
                <button id="apply-coupon-button" type="button" class="btn btn-dark px-4 rounded-pill"><i class="fa fa-gift mr-2"></i>Apply coupon</button>
              </div>
            </div>
          </div>
          <div class="bg-light rounded-pill px-4 py-3 text-uppercase font-weight-bold">Instructions for seller</div>
          <div class="p-4">
            <p class="font-italic mb-4">If you have some information for the seller you can leave them in the box below</p>
            <textarea name="" cols="30" rows="2" class="form-control"></textarea>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="bg-light rounded-pill px-4 py-3 text-uppercase font-weight-bold">Order summary </div>
          <div class="p-4">
            <!-- <p class="font-italic mb-4">Shipping and additional costs are calculated based on values you have entered.</p> -->
            <ul class="list-unstyled mb-4">
              <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Order Subtotal </strong><strong>$<span id="sub-total-display"><?php echo (float) $totalPrice ?>.00</span></strong></li>
              <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Discount Amount</strong><strong>$<span id="discount-amount-display"><?php echo $discountAmount; ?>.00</span></strong></li>
              <!-- <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Tax</strong><strong>$0.00</strong></li> -->
              <li class="d-flex justify-content-between py-3 border-bottom"><strong class="text-muted">Total</strong>
                <h5 class="font-weight-bold"> $<span id="total-amount-display"><?php echo $totalPrice - $discountAmount; ?>.00</span></h5>
              </li>
            </ul><a href="#" class="btn btn-dark rounded-pill py-2 btn-block">Proceed to checkout</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        let apply_coupon_btn = $("#apply-coupon-button");
        let coupon_code      = $("#coupon-code");
        let coupon_status    = $("#coupon-status");
        let sub_total_display    = $("#sub-total-display")
        let total_amount_display    = $("#total-amount-display")
        let discount_amount_display    = $("#discount-amount-display")
        let cart_id_display    = $("#cart-id")

        // discount_amount_display.hide();

        apply_coupon_btn.click(function() {
            if (coupon_code.val() == '') {
                coupon_status.attr('class', 'alert alert-warning text-center').text('A coupon code is required')
            } else {
                coupon_status.attr('class', 'alert alert-info text-center').text(`Applying coupon code...`)

                var couponData = {
                    'code': coupon_code.val(),
                    'cart_id': cart_id_display.val()
                };

                $.post('actions/apply-coupon.php', couponData, (data) => {
                    console.log(data);
                    const response = JSON.parse(data);
                    
                    console.log(response);
                    if (response.status == true) {
                        coupon_status.attr('class', 'alert alert-success text-center').text(`${response.message}`)
                        discount_amount_display.text(`${response.data.cart.total_amount - response.data.cart.amount_payable}`);
                        total_amount_display.text(`${response.data.cart.amount_payable}`);
                    } else {
                        coupon_status.attr('class', 'alert alert-danger text-center').text(`Error: ${response.message}`)
                    }
                })

            }
        })
    })
</script>