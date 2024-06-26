<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$province_list = [];
?>
<div class="container" id="checkout-page">
    <?php
    if (isset($cartItems['array']) && $cartItems['array'] != null) {
        if ($shippingOrder != 0 && $shippingOrder != null) { ?>
            <div class="filter-sidebar">
                <div class="title">
                    <span><?= lang('freeShippingHeader') ?></span>
                </div>
                <div class="oaerror info">
                    <strong><?= lang('promo') ?></strong> - <?= str_replace(array('%price%', '%currency%'), array($shippingOrder, CURRENCY), lang('freeShipping')) ?>!
                </div>
            </div>
        <?php } ?> 
        <?= purchase_steps(1, 2) ?>
        <div class="row">
            <div class="col-sm-9 left-side">
                <form method="POST" id="goOrder">
                    <div class="title alone">
                        <span><?= lang('checkout') ?></span>
                    </div>
                    <?php
                    if ($this->session->flashdata('submit_error')) {
                        ?>
                        <hr>
                        <div class="alert alert-danger">
                            <h4><span class="glyphicon glyphicon-alert"></span> <?= lang('finded_errors') ?></h4>
                            <?php
                            foreach ($this->session->flashdata('submit_error') as $error) {
                                echo $error . '<br>';
                            }
                            ?>
                        </div>
                        <hr>
                        <?php
                    }
                    ?>
                    <?php
                    if ($this->session->flashdata('userError')) {
                        ?>
                        <hr>
                        <div class="alert alert-danger">
                            <h4><span class="glyphicon glyphicon-alert"></span> <?= lang('finded_errors') ?></h4>
                            <?php
                            foreach ($this->session->flashdata('userError') as $error) {
                                echo $error . '<br>';
                            }
                            ?>
                        </div>
                        <hr>
                        <?php
                    }
                    ?>

                    <?php
                    if ($this->session->flashdata('amountError')) {
                        ?>
                        <hr>
                        <div class="alert alert-danger">
                            <h4><span class="glyphicon glyphicon-alert"></span> <?= lang('finded_errors') ?></h4>
                            <?php
                            foreach ($this->session->flashdata('amountError') as $error) {
                                echo $error . '<br>';
                            }
                            ?>
                        </div>
                        <hr>
                        <?php
                    }
                    ?>
                    <div class="payment-type-box">
                        <select class="selectpicker payment-type" data-style="btn-blue" name="payment_type">
                            <?php if ($alipay_visibility == 1) { ?>
                                <option value="alipay"><?= lang('alipay') ?> </option>
                            <?php } ?>    
                        </select>
                        <span class="top-header text-center"><?= lang('choose_payment') ?></span>
                    </div>
                    <div class="row">
                        <input type="hidden" name="order_source" value="10">
                        <div class="form-group col-sm-6">
                            <label for="nameInput"><?= lang('name') ?> (<sup><?= lang('requires') ?></sup>)</label>
                            <input id="nameInput" class="form-control" style="max-width: 300px;" name="name" value="<?= @$_POST['name'] ?>" type="text" placeholder="<?= lang('name') ?>">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="phoneInput"><?= lang('phone') ?> (<sup><?= lang('requires') ?></sup>)</label>
                            <input id="phoneInput" class="form-control" style="max-width: 300px;" name="phone" value="<?= @$_POST['phone'] ?>" type="text" placeholder="<?= lang('phone') ?>">
                        </div>                                                
                        <div class="form-group col-sm-6">
                            <label for="emailAddressInput"><?= lang('email_address') ?> (<sup><?= lang('requires') ?></sup>)</label>
                            <input id="emailAddressInput" class="form-control" style="max-width: 300px;" name="email" value="<?= @$_POST['email'] ?>" type="text" placeholder="<?= lang('email_address') ?>">
                        </div> 
                        <div class="form-group col-sm-6">
                            <label for="postInput"><?= lang('post_code') ?></label>
                            <input id="postInput" class="form-control" style="max-width: 300px;" name="post_code" value="<?= @$_POST['post_code'] ?>" type="text" placeholder="<?= lang('post_code') ?>">
                        </div>
                         <div class="form-group col-sm-6">
                            <label for="cityInput"><?= lang('city') ?> (<sup><?= lang('requires') ?></sup>)</label>
                            <input id="cityInput" class="form-control" style="max-width: 300px;" name="city" value="<?= @$_POST['city'] ?>" type="text" placeholder="省/市/区（县）例如广东省深圳市龙华区">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="addressInput"><?= lang('address') ?> (<sup><?= lang('requires') ?></sup>)</label>
                            <textarea id="addressInput" name="address" class="form-control" style="height: 64px; max-width: 300px;" rows="5" placeholder="景龙社区龙华大道3639号环智中心C座"><?= @$_POST['address'] ?></textarea>
                        </div>                         
                        <div class="form-group col-sm-12">
                            <label for="notesInput"><?= lang('notes') ?></label>
                            <textarea id="notesInput" class="form-control" name="notes" style="height: 64px; max-width: 300px;" rows="3" placeholder="备注"><?= @$_POST['notes'] ?></textarea>
                        </div>
                    </div>
                    <?php if ($codeDiscounts == 1) { ?>
                        <div class="discount">
                            <label><?= lang('discount_code') ?></label>
                            <input class="form-control" name="discountCode" value="<?= @$_POST['discountCode'] ?>" placeholder="<?= lang('enter_discount_code') ?>" type="text">
                            <a href="javascript:void(0);" class="btn btn-default" onclick="checkDiscountCode()"><?= lang('check_code') ?></a>
                        </div>
                    <?php } 
                    $finalShippingAmount = 0;
                    $vendors_order_info = array();
                    ?>
                    <div class="table-responsive">                         
                        <table class="table table-bordered table-products">
                            <thead>
                                <tr>
                                    <th><?= lang('product') ?></th>
                                    <th><?= lang('title') ?></th>
                                    <th><?= lang('quantity') ?></th>
                                    <th><?= lang('unit_price') ?></th>
                                    <th><?= lang('total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems['vendors'] as $vendor_id => $vendor_name){ 
                                    $vendorFinalSum = 0;
                                    $vendorShippingAmount = 0;
                                    $vendor_order_info = array();
                                ?>
                                <tr><td colspan="5"><label class="checkout_vendor_name">商户:<?= $vendor_name ?></label></td></tr>                                   
                                <?php foreach ($cartItems['array'] as $item) { ?>
                                    <?php if($item['vendor_id'] == $vendor_id) { 
                                        $vendorFinalSum += $item['sum_price'];
                                    ?>                               
                                    <tr>
                                        <td class="relative">
                                            <input type="hidden" name="id[]" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="quantity[]" value="<?= $item['num_added'] ?>">
                                            
                                            <?php 
                                                $productImage = base_url('/attachments/no-image-frontend.png');
                                                if(is_file('attachments/shop_images/' . $item['image'])) {
                                                    $productImage = base_url('/attachments/shop_images/' . $item['image']);
                                                }
                                            ?>
                                            <img class="product-image" src="<?= $productImage ?>" alt="">
                                            
                                            <a href="<?= base_url('home/removeFromCart?delete-product=' . $item['id'] . '&back-to=checkout') ?>" class="btn btn-xs btn-danger remove-product">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </a>
                                        </td>
                                        <td><a href="<?= LANG_URL . '/' . $item['url'] ?>"><?= $item['title'] ?></a></td>
                                        <td>
                                            <a class="btn btn-xs btn-primary refresh-me add-to-cart <?= $item['quantity'] <= $item['num_added'] ? 'disabled' : '' ?>" data-id="<?= $item['id'] ?>" href="javascript:void(0);">
                                                <span class="glyphicon glyphicon-plus"></span>
                                            </a>
                                            <span class="quantity-num">
                                                <?= $item['num_added'] ?>
                                            </span>
                                            <a class="btn  btn-xs btn-danger" onclick="removeProduct(<?= $item['id'] ?>, true)" href="javascript:void(0);">
                                                <span class="glyphicon glyphicon-minus"></span>
                                            </a>
                                        </td>
                                        <td><?= $item['price'] . CURRENCY ?></td>
                                        <td><?= $item['sum_price'] . CURRENCY ?></td>
                                    </tr>
                                    <?php } $vendorFinalSum = number_format($vendorFinalSum, 2)?>
                                <?php }?>
                                <tr>
                                    <td colspan="4" class="text-right"><?= lang('vendor_total') ?></td>
                                    <td>
                                        <span class="final-amount"><?= $vendorFinalSum ?></span><?= CURRENCY ?>
                                    </td>
                                </tr>

                                <?php
                                    if((int)$shippingAmount > 0 && ((int)$shippingOrder > $vendorFinalSum)) {
                                        $vendorShippingAmount = $shippingAmount;
                                ?>
                                <tr>
                                    <td colspan="4" class="text-right"><?= lang('vendor_shipping') ?></td>
                                    <td>                                        
                                        <span><?= (int)$vendorShippingAmount ?></span><?= CURRENCY ?>
                                    </td>
                                </tr>
                                <?php }
                                   $vendor_order_info["vendor_final_amount"] = $vendorFinalSum;
                                   $vendor_order_info["vendor_shipping_amount"] = $vendorShippingAmount;
                                   $vendors_order_info[$vendor_id] = $vendor_order_info;                                   
                                   $finalShippingAmount += $vendorShippingAmount;?>
                            <?php } $payAmount = number_format($cartItems['finalSum'] + $finalShippingAmount, 2);
                                     $vendors_amount = json_encode($vendors_order_info);?>
                                <tr><td colspan="5"><label class="checkout_subtotal">小计:</label></td></tr>
                            <tr>
                                <td colspan="4" class="text-right"><?= lang('prouducts_total') ?></td>
                                <td>
                                    <span class="final-amount checkout_subtotal_detail"><?= $cartItems['finalSum'] ?><?= CURRENCY ?></span>
                                    <input type="hidden" class="final-amount" name="final_amount" value="<?= $cartItems['finalSum'] ?>">
                                    <input type="hidden" name="vendors_amount" value='<?= $vendors_amount ?>'>                                    
                                    <input type="hidden" name="amount_currency" value="<?= CURRENCY ?>">
                                    <input type="hidden" name="discountAmount" value="">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><?= lang('order_shipping') ?></td>
                                <td>
                                    <span class="checkout_subtotal_detail"><?= (int)$finalShippingAmount ?><?= CURRENCY ?></span>
                                    <input type="hidden" class="final-amount" name="finalShippingAmount" value="<?= $finalShippingAmount ?>">                                    
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><?= lang('order_total') ?></td>
                                <td>
                                    <span class="final-amount checkout_subtotal_detail"><?= $payAmount ?><?= CURRENCY ?></span>
                                    <input type="hidden" class="final-amount" name="payAmount" value="<?= $payAmount ?>">
                                </td>
                            </tr>                            
                            </tbody>                            
                        </table>     
                    </div>                       
                </form>
                <div>
                    <a href="<?= LANG_URL ?>" class="btn btn-primary go-shop">
                        <span class="glyphicon glyphicon-circle-arrow-left"></span>
                        <?= lang('back_to_shop') ?>
                    </a>
                    <a href="javascript:void(0);" class="btn btn-primary go-order" onclick="document.getElementById('goOrder').submit();" class="pull-left">
                        <?= lang('custom_order') ?> 
                        <span class="glyphicon glyphicon-circle-arrow-right"></span>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-sm-3"> 
                <div class="filter-sidebar">
                    <div class="title">
                        <span><?= lang('best_sellers') ?></span>
                        <i class="fa fa-trophy" aria-hidden="true"></i>
                    </div>
                    <?= $load::getProducts($bestSellers, '', true) ?>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="alert alert-info"><?= lang('no_products_in_cart') ?></div>
    <?php
}
if ($this->session->flashdata('deleted')) {
    ?>
    <script>
        $(document).ready(function () {
            ShowNotificator('alert-info', '<?= $this->session->flashdata('deleted') ?>');
        });
    </script>
<?php } if ($codeDiscounts == 1 && isset($_POST['discountCode'])) { ?>
    <script>
        $(document).ready(function () {
            checkDiscountCode();
        });
    </script>
<?php } ?>
