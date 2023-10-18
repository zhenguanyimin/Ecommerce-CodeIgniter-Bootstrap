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
    
    $queryOrderTypes = array(
        "所有订单" => -1,
        "待发货" => 10,
        "待收货" => 20,
        "未支付" => 30,
        "已完成" => 40,                        
        "已取消" => 50,
    );     
?>
<div class="inner-nav">
    <div class="container">
        <a href="<?= LANG_URL ?>"><?= lang('home') ?></a> <span class="active"> > <?= lang('my_acc') ?></span>
    </div>
    <div class="container">
        <ul class="user_navbar" style="margin-left: -80px;">
            <li><a href="<?= base_url('/myaccount') ?>">个人资料</a></li>
            <li class=<?= isset($_GET["queryOrderType"])&&($_GET["queryOrderType"] == $queryOrderTypes["所有订单"])? "active":"" ?>><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["所有订单"] ?>">所有订单</a></li>
            <li class=<?= isset($_GET["queryOrderType"])&&($_GET["queryOrderType"] == $queryOrderTypes["待发货"])? "active":"" ?>><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["待发货"] ?>">待发货</a></li>
            <li class=<?= isset($_GET["queryOrderType"])&&($_GET["queryOrderType"] == $queryOrderTypes["待收货"])? "active":"" ?>><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["待收货"] ?>">待收货</a></li>
            <li class=<?= isset($_GET["queryOrderType"])&&($_GET["queryOrderType"] == $queryOrderTypes["已完成"])? "active":"" ?>><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["已完成"] ?>">待评价</a></li>    
        </ul>
    </div>
</div>

<div class="container user-page">
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-condensed table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>序号</th>
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
                        $i = 0;
                        if (!empty($orders_history)) {
                            foreach ($orders_history as $order) {
                                ?>
                                <tr>
                                    <td><?= $i+1 ?></td>
                                    <td><?= $order['child_order_id'] ?></td>
                                    <td><?= date('Y-m-d H:i:s', $order['date']) ?></td>
                                    <td><?= array_key_exists($order['pay_type'], $payTypeEnum)? $payTypeEnum[$order['pay_type']]:"未知"?></td>
                                    <td><?= $order['total_amount'] ?></td>
                                    <td><span class="<?= $order['vendor_order_status'] == 30 ? "ant-tag-green":"ant-tag"?>"><?= array_key_exists($order['vendor_order_status'], $orderStatus)? $orderStatus[$order['vendor_order_status']]:"进行中"?></span></td>
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
                                    <td><?= $order['express_company'] == ""? "无": $order['express_company'] ?></td>
                                    <td><?= $order['express_no'] == ""? "无": $order['express_no']?></td>
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
<!--                                    <?php if($order['pay_status'] == 10 && $order['delivery_status'] == 10&& $order['receipt_status'] == 10){ ?>
                                        <a href="<?= base_url('/checkout') ?>" class="btn btn-sm btn-green show-more">支付</a>
                                    <?php }?>                                          -->
                                    <?php if($order['pay_status'] == 20 && $order['delivery_status'] == 20&& $order['receipt_status'] == 10){ ?>
                                        <a href="<?= base_url('/vendor/orders/receipt?order_id='. $order['child_order_id'] ."&queryOrderType=".$_GET["queryOrderType"]) ?>" class="btn btn-sm btn-green show-more">确认收货</a>
                                    <?php }?>                                      
                                     </td>                                    
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="13"><?= lang('usr_no_orders') ?></td>
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