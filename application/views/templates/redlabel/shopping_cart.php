<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container" id="shopping-cart">
    <h1><?= lang('shopping_cart') ?></h1>
    <hr>
    <?php
    if (!isset($cartItems['array']) || $cartItems['array'] == null) {
        ?>
        <div class="alert alert-info"><?= lang('no_products_in_cart') ?></div>
        <?php
    } else {
        echo purchase_steps(1);
        $finalShippingAmount = 0;?>
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
                         <?php } $vendorFinalSum = number_format($vendorFinalSum, 2) ?>
                     <?php }?>
                     <tr>
                         <td colspan="4" class="text-right"><?= lang('vendor_total') ?></td>
                         <td>
                             <span class="final-amount"><?= $vendorFinalSum ?></span><?= CURRENCY ?>
                             <input type="hidden" class="final-amount" name="vendor_final_amount" value="<?= $vendorFinalSum ?>">
                             <input type="hidden" name="amount_currency" value="<?= CURRENCY ?>">
                             <input type="hidden" name="discountAmount" value="">
                         </td>
                     </tr>
                 <?php }?>
                 <tr>
                     <td colspan="4" class="text-right"><?= lang('shop_cart_total') ?></td>
                     <td>
                         <span class="final-amount checkout_subtotal_detail"><?= $cartItems['finalSum'] ?><?= CURRENCY ?></span>
                         <input type="hidden" class="final-amount" name="final_amount" value="<?= $cartItems['finalSum'] ?>">
                         <input type="hidden" name="amount_currency checkout_subtotal_detail" value="<?= CURRENCY ?>">
                         <input type="hidden" name="discountAmount" value="">
                     </td>
                 </tr>                                  
                 </tbody>                            
             </table>     
         </div>  
        <a href="<?= LANG_URL ?>" class="btn btn-primary go-shop">
            <span class="glyphicon glyphicon-circle-arrow-left"></span>
            <?= lang('back_to_shop') ?>
        </a>
        <a class="btn btn-primary go-checkout" href="<?= LANG_URL . '/checkout' ?>">
            <?= lang('checkout') ?> 
            <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
        </a>
    <?php } ?>
</div>
<?php
if ($this->session->flashdata('deleted')) {
    ?>
    <script>
        $(document).ready(function () {
            ShowNotificator('alert-info', '<?= $this->session->flashdata('deleted') ?>');
        });
    </script>
<?php } ?>