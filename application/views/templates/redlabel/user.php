<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    $orderStatus = array(
        10 => "进行中",
        20 => "取消",
        21 => "待取消",
        30 => "已完成",                        
    );

    $payTypeEnum = array(
        10 => "余额支付",
        20 => "支付宝支付",
    );   
?>
<div class="inner-nav">
    <div class="container">
        <a href="<?= LANG_URL ?>"><?= lang('home') ?></a> <span class="active"> > <?= lang('user_login') ?></span>
    </div>
</div>
<div class="container user-page">
    <?php if ($this->session->flashdata('error')) { ?>
        <div class="alert alert-danger"><?= implode('<br>', $this->session->flashdata('error')) ?></div>
    <?php } ?>      
    <div class="row">
        <div class="col-sm-4">
            <div class="loginmodal-container">
                <h1><?= lang('my_acc') ?></h1><br>
                <form method="POST" action="">
                    <input type="text" name="name" value="<?= $userInfo['name'] ?>" placeholder="Name">
                    <input type="text" name="phone" value="<?= $userInfo['phone'] ?>" placeholder="Phone">
                    <input type="text" name="email"  value="<?= $userInfo['email'] ?>" placeholder="Email">
                    <input type="password" name="pass" placeholder="Password (leave blank if no change)"> 
                    <input type="submit" name="update" class="login loginmodal-submit" value="<?= lang('update') ?>">
                    <a href="<?= LANG_URL . '/logout' ?>" class="login loginmodal-submit text-center"><?= lang('logout') ?></a>
                </form>
            </div>
        </div>
    </div>  
    <div class="row">
        <div class="col-sm-12">
            <div><b><?= lang('my_order') ?></b></div>
            <div class="table-responsive">
                <table class="table table-condensed table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?= lang('usr_order_id') ?></th>
                            <th><?= lang('usr_order_date') ?></th>
                            <th><?= lang('usr_order_pay_type') ?></th>
                            <th><?= lang('usr_order_amount') ?></th>
                            <th><?= lang('usr_order_status') ?></th>
                            <th><?= lang('status') ?></th>
                            <th><?= lang('express_company') ?></th>
                            <th><?= lang('express_no') ?></th>                            
                            <th><?= lang('usr_order_address') ?></th>
                            <th><?= lang('usr_order_phone') ?></th>
                            <th><?= lang('vendor_name') ?></th>    
                            <th><?= lang('product_detail') ?></th>
                            <th class="text-right"><i class="fa fa-list" aria-hidden="true"></i>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        echo "orders_history".count($orders_history);
                        if (!empty($orders_history)) {
                            foreach ($orders_history as $order) {
                                ?>
                                <tr>
                                    <td><?= $order['child_order_id'] ?></td>
                                    <td><?= date('Y-m-d H:i:s', $order['date']) ?></td>
                                    <td><?= array_key_exists($order['pay_type'], $payTypeEnum)? $payTypeEnum[$order['pay_type']]:"未知"?></td>
                                    <td><?= $order['total_amount'] ?></td>
                                    <td><span class="<?= $order['order_status'] == 30 ? "ant-tag":"ant-tag-green"?>"><?= array_key_exists($order['order_status'], $orderStatus)? $orderStatus[$order['order_status']]:"进行中"?></span></td>
                                    <td>
                                        <p>
                                            <span>付款状态：</span>
                                            <span class="<?= $order['pay_status'] == 10 ? "ant-tag":"ant-tag-green"?>"><?= $order['pay_status'] == 10 ? "未付款":"已付款"?></span>
                                        </p>
                                        <p>
                                            <span>发货状态：</span>
                                            <span class="<?= $order['delivery_status'] == 10 ? "ant-tag":"ant-tag-green"?>"><?= $order['delivery_status'] == 10 ? "未发货":"已发货"?></span>
                                        </p>                        
                                        <p>
                                            <span>收货状态：</span>
                                            <span class="<?= $order['receipt_status'] == 10 ? "ant-tag":"ant-tag-green"?>"><?= $order['receipt_status'] == 10 ? "未收货":"已收货"?></span>
                                        </p>        
                                    </td>                                    
                                    <td><?= $order['express_company'] ?></td>
                                    <td><?= $order['express_no'] ?></td>
                                    <td><?= $order['address'] ?></td>
                                    <td><?= $order['phone'] ?></td>
                                    <td><?= $order['vendor_name'] ?></td>
                                    <td>    
                                        <?php
                                        $arr_products = unserialize($order['products']);
                                        foreach ($arr_products as $product) {
                                            if(empty($product['product_info']['vendor_id']) || !empty($product['product_info']['vendor_id']) && ($order['vendor_id'] == $product['product_info']['vendor_id'])){                                                
                                            ?>
                                            <div style="word-break: break-all;">
                                                <div>
                                                    <img src="<?= base_url('attachments/shop_images/' . $product['product_info']['image']) ?>" alt="Product" style="width:100px; margin-right:10px;" class="img-responsive">
                                                </div>
                                                <a target="_blank" href="<?= base_url($product['product_info']['url']) ?>">
                                                    <?= base_url($product['product_info']['url']) ?> 
                                                </a> 
                                                <div style=" background-color: #f1f1f1; border-radius: 2px; padding: 2px 5px;"><b><?= lang('user_order_quantity') ?></b> <?= $product['product_quantity']; ?></div>
                                                 <div style=" background-color: #f1f1f1; border-radius: 2px; padding: 2px 5px;"><b><?= lang('unit_price') ?></b> <?= $product['product_info']['price'].CURRENCY;?></div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <hr>
                                            <?php }}
                                        ?>
                                    </td>
                                    <td>
                                    <?php if($order['pay_status'] == 20 && $order['delivery_status'] == 20&& $order['receipt_status'] == 10){ ?>
                                        <a href="<?= base_url('/vendor/orders/receipt?order_id='. $order['child_order_id']) ?>" class="btn btn-sm btn-green show-more">确认收货</a>
                                    <?php }?>
                                     </td>                                    
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="5"><?= lang('usr_no_orders') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?= $links_pagination ?>
            </div>
        </div>
        </div>
    </div>
</div>