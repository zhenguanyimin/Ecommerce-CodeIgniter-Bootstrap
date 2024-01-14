<link href="<?= base_url('assets/css/bootstrap-toggle.min.css') ?>" rel="stylesheet">
<?php
$searchTypes = array(
    10 => "订单号",
    20 => "客户姓名",
    30 => "客户手机号",
    40 => "客户邮箱",                        
    50 => "收货人姓名",
    60 => "收货人手机号",
    70 => "收货人邮箱",
);
$orderSources = array(
    -1 => "全部",
    10 => "普通订单",
    20 => "商户诚信保证金",        
); 
$payTypes = array(
    -1 => "全部",
    20 => "支付宝支付",        
); 
$deliveryTypes = array(
    -1 => "全部",
    10 => "快递配送",
);

$payTypeEnum = array(
    10 => "余额支付",
    20 => "支付宝支付",
);

$orderStatus = array(
    10 => "进行中",
    20 => "取消",
    21 => "待取消",
    30 => "已完成",                        
); 
?>
<div>
    <h1><img src="<?= base_url('assets/imgs/orders.png') ?>" class="header-img" style="margin-top:-2px;"> Orders <?= isset($_GET['settings']) ? ' / Settings' : '' ?></h1>
    <?php if (!isset($_GET['settings'])) { ?>
        <a href="?settings" class="pull-right orders-settings"><i class="fa fa-cog" aria-hidden="true"></i> <span>Settings</span></a>
    <?php } else { ?>
        <a href="<?= base_url('admin/orders') ?>" class="pull-right orders-settings"><i class="fa fa-angle-left" aria-hidden="true"></i> <span>Back</span></a>
    <?php } ?>
</div>
<hr>
<div class="table-operator">
<form class="form-horizontal" method="GET" action="<?= base_url('admin/orders') ?>" id="admin-orders-search" style="margin-bottom:10px;">
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="searchValue" title="关键词" class="">关键词</label>
                <select class="selectpicker" name="searchType" id="searchType">
                    <?php foreach ($searchTypes as $id => $name) { ?>
                        <option <?= isset($_GET['searchType']) && $_GET['searchType'] == $id ? 'selected' : '' ?> value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php } ?>                                                
                </select>        
                <input value="<?= isset($_GET['searchValue']) ? htmlspecialchars($_GET['searchValue']) : '' ?>" placeholder="请输入关键词" type="text"  name="searchValue" id="searchValue" style="height: 34px;" class="clear-control">                
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="orderSource" title="订单来源" class="">订单来源</label>
                <select class="selectpicker" name="orderSource" id="orderSource">
                    <?php foreach ($orderSources as $id => $name) { ?>
                        <option <?= isset($_GET['orderSource']) && $_GET['orderSource'] == $id ? 'selected' : '' ?> value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php } ?>                                                   
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="payType" title="支付方式" class="">支付方式</label>
                <select class="selectpicker" name="payType" id="payType"> 
                    <?php foreach ($payTypes as $id => $name) { ?>
                        <option <?= isset($_GET['payType']) && $_GET['payType'] == $id ? 'selected' : '' ?> value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php } ?>                                
                </select>  
            </div>
        </div>        
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label for="deliveryType" title="配送方式" class="">配送方式</label>                
                <select class="selectpicker" name="deliveryType" id="deliveryType"> 
                    <?php foreach ($deliveryTypes as $id => $name) { ?>
                        <option <?= isset($_GET['deliveryType']) && $_GET['deliveryType'] == $id ? 'selected' : '' ?> value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php } ?>                                
                </select>
            </div>     
        </div>        
        <div class="col-sm-8">
            <div class="form-group">
                <label for="start_time">订单时间:</label> 
                <input type="date" value="<?= isset($_GET['start_time']) ? htmlspecialchars($_GET['start_time']) : '' ?>" name="start_time" id="start_time" style="width:20%; height: 34px;" class="clear-control">
                <label for="end_time"> — </label>                        
                <input type="date" value="<?= isset($_GET['end_time']) ? htmlspecialchars($_GET['end_time']) : '' ?>" name="end_time" id="end_time" style="width: 20%; height: 34px;" class="clear-control">
                <button type="submit" class="btn btn-inner-search">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"> 搜索</span>
                </button>
                <a class="btn btn-default" id="clear-form-admin-orders" href="javascript:void(0);">重置</a>             
            </div>                    
        </div>        
    </div>
</form>   
</div>
<?php       
if (!isset($_GET['settings'])) {
    if (!empty($orders)) {
        ?>
        <div class="table-responsive">
            <table class="table table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>订单号</th>
                        <th><?= lang('time_created') ?></th>
                        <th><?= lang('name') ?></th>
                        <th><?= lang('order_type') ?></th>
                        <th><?= lang('pay_type') ?></th>
                        <th><?= lang('phone') ?></th>
                        <th><?= lang('usr_order_status') ?></th>
                        <th><?= lang('status') ?></th>
                        <th>订单总金额</th>
                        <th>商户销售金额</th>
                        <th>佣金金额</th>
                        <th><?= lang('shipping') ?></th>                  
                        <th class="text-right"><i class="fa fa-list" aria-hidden="true"></i>操作</th>                       
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    $order_id = "";
                    foreach ($orders as $order) {
                        $order_id = $order['order_id'];
                        ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td class="relative" id="order_id-id-<?= $order['order_id'] ?>"><?= $order['order_id'] ?></td>
                            <td><?= date('Y-m-d H:i:s', $order['date']) ?></td>
                            <td><?= $order['name'] ?></td>
                            <td><?= array_key_exists($order['order_source'], $orderSources)? $orderSources[$order['order_source']]:"未知" ?></td>
<!--                            <td><?= array_key_exists($order['pay_type'], $payTypeEnum)? $payTypeEnum[$order['pay_type']]:"未知" ?></td>-->
                            <td><?= array_key_exists($order['pay_type'], $payTypeEnum)? $payTypeEnum[$order['pay_type']]:"未知"?></td>
                            <td><?= $order['phone'] ?></td>
                            <td><span class="<?= $order['order_status'] == 30 ? "ant-tag-green":"ant-tag"?>"><?= array_key_exists($order['order_status'], $orderStatus)? $orderStatus[$order['order_status']]:"进行中"?></span></td>                    
                            <td>
                                <p>
                                    <span>付款状态：</span>
                                    <span class="<?= $order['pay_status'] == 10 ? "ant-tag":"ant-tag-green"?>"><?= $order['pay_status'] == 10 ? "未付款":"已付款"?></span>
                                </p>
                                <?php if($order['order_source'] != 20){?>
                                <p>
                                    <span>发货状态：</span>
                                    <span class="<?= $order['delivery_status'] == 10 ? "ant-tag":"ant-tag-green"?>"><?= $order['delivery_status'] == 10 ? "未发货":"已发货"?></span>
                                </p>                        
                                <p>
                                    <span>收货状态：</span>
                                    <span class="<?= $order['receipt_status'] == 10 ? "ant-tag":"ant-tag-green"?>"><?= $order['receipt_status'] == 10 ? "未收货":"已收货"?></span>
                                </p>
                                <?php }?>
                            </td>
                            <td><?= $order['total_amount'] . CURRENCY ?></td>
                            <td><?= $order['vendor_share'] . CURRENCY ?></td>
                            <td><?= $order['commission'] . CURRENCY ?></td>
                            <td><?= $order['shipping_amount'] . CURRENCY ?></td>                             
                            <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-default more-info" data-toggle="modal" data-target="#modalPreviewMoreInfo" style="margin-top:10%;" data-more-info="<?= $order['order_id'] ?>">
                                    详情 
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                </a>
                                <?php if($order['user_id'] == 5 && $order['pay_status'] == 20 && $order['delivery_status'] == 10) { ?> 
                                    <a href="javascript:void(0);" data-toggle="modal" data-target="#addExpressNo"  class="btn btn-default more-info" style="margin-top:10%;">发货</a>                                 
                                <?php }?>                                
                                <a href="<?= base_url('/admin/orders/delete/'. $order['id']) ?>" onclick="return confirm('确定要删除该订单?')" class="btn btn-danger" style="margin-top:10%;">
                                    删除 
                                    <i class="fa fa-remove" aria-hidden="true"></i>
                                </a>                            
                            </td>
                            <td class="hidden" id="order-id-<?= $order['order_id'] ?>">
                                <div class="table-responsive">
                                    <table class="table more-info-purchase">
                                        <tbody>
                                            <tr>
                                                <td><b><?= lang('email') ?></b></td>
                                                <td><a href="mailto:<?= $order['email'] ?>"><?= $order['email'] ?></a></td>
                                            </tr>
                                            <tr>
                                                <td><b><?= lang('city') ?></b></td>
                                                <td><?= $order['city'] ?></td>
                                            </tr>
                                            <tr>
                                                <td><b><?= lang('address') ?></b></td>
                                                <td><?= $order['address'] ?></td>
                                            </tr>
                                            <tr>
                                                <td><b><?= lang('post_code') ?></b></td>
                                                <td><?= $order['post_code'] ?></td>
                                            </tr>
                                            <tr>
                                                <td><b><?= lang('notes') ?></b></td>
                                                <td><?= $order['notes'] ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>Come from site</b></td>
                                                <td>
                                                    <?php if ($order['referrer'] != 'Direct') { ?>
                                                        <a target="_blank" href="<?= $order['referrer'] ?>" class="orders-referral">
                                                            <?= $order['referrer'] ?>
                                                        </a>
                                                    <?php } else { ?>
                                                        Direct traffic or referrer is not visible                       
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b><?= lang('pay_type') ?></b></td>
                                                <td><?= $order['payment_type'] ?></td>
                                            </tr>
                                            <tr>
                                                <td><b><?= lang('discount') ?></b></td>
                                                <td><?= $order['discount_type'] == 'float' ? '-' . $order['discount_amount'] : '-' . $order['discount_amount'] . '%' ?></td>
                                            </tr>
                                            <?php if ($order['payment_type'] == 'PayPal') { ?>
                                                <tr>
                                                    <td><b>PayPal Status</b></td>
                                                    <td><?= $order['paypal_status'] ?></td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                            <?php if ($order['payment_type'] == 'Alipay') { ?>
                                                <tr>
                                                    <td><b>Alipay Status</b></td>
                                                    <td><?= $order['alipay_status'] ?></td>
                                                </tr>
                                            <?php } ?>
                                                <td colspan="2"><b><?= lang('products') ?></b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <?php
                                                    $arr_products = unserialize($order['products']);
                                                    foreach ($arr_products as $product) {
                                                        $total_amount = 0;
                                                        $total_amount += str_replace(' ', '', str_replace(',', '.',$product['product_info']['price'])*$product['product_quantity']);
                                                        ?>
                                                        <div style="word-break: break-all;">
                                                            <div>
                                                                <img src="<?= base_url('attachments/shop_images/' . $product['product_info']['image']) ?>" alt="Product" style="width:100px; margin-right:10px;" class="img-responsive">
                                                            </div>
                                                            <a data-toggle="tooltip" data-placement="top" title="Click to preview" target="_blank" href="<?= base_url($product['product_info']['url']) ?>">
                                                                <?= base_url($product['product_info']['url']) ?>
                                                                <div style=" background-color: #f1f1f1; border-radius: 2px; padding: 2px 5px;">
                                                                    <b>Quantity:</b> <?= $product['product_quantity'] ?> / 
                                                                    <b>Price: <?= $product['product_info']['price'].' '.$this->config->item('currency') ?></b>
                                                                </div>
                                                            </a>
                                                            <div class="">
                                                                <b>Vendor:</b>
                                                                <a href="<?= base_url('admin/listvendors?id=' . $product['product_info']['vendor_id']) ?>"><?= isset($product['product_info']['vendor_name']) ? $product['product_info']['vendor_name'] : '-Vendor name is missing-' ?></a>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <div style="padding-top:10px; font-size:16px;">Total amount of products: <?= $total_amount.' '.$this->config->item('currency') ?></div>
                                                        <hr>
                                                    <?php }
                                                    ?>
                                                </td>

                                                <?php 
                                                $total_parsed = str_replace(' ', '', str_replace(',', '', $order['total_amount']));
                                                if((int)$shippingAmount > 0 && ((int)$shippingOrder > $total_parsed)) { ?>
                                                    <tr>
                                                        <td><b><?= lang('shippingAmount') ?></b></td>
                                                        <td><?= (int)$shippingAmount.' '.$this->config->item('currency') ?></td>
                                                    </tr>
                                                <?php } ?>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php $i++;} ?>
                </tbody>
            </table>
        </div>
        <?= $links_pagination ?>
    <?php } else { ?>
        <div class="alert alert-info">没有订单!</div>
    <?php }
    ?>        
    <hr>
    <?php
}
if (isset($_GET['settings'])) {
    ?>
    <h3>Cash On Delivery</h3>
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">Change visibility of this purchase option</div>
                <div class="panel-body">
                    <?php if ($this->session->flashdata('cashondelivery_visibility')) { ?>
                        <div class="alert alert-info"><?= $this->session->flashdata('cashondelivery_visibility') ?></div>
                    <?php } ?>
                    <form method="POST" action="">
                        <input type="hidden" name="cashondelivery_visibility" value="<?= htmlspecialchars($cashondelivery_visibility) ?>">
                        <input <?= $cashondelivery_visibility == 1 ? 'checked' : '' ?> data-toggle="toggle" data-for-field="cashondelivery_visibility" class="toggle-changer" type="checkbox">
                        <button class="btn btn-default" value="" type="submit">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <h3>Alipay</h3>
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">Change visibility of this purchase option</div>
                <div class="panel-body">
                    <?php if ($this->session->flashdata('alipay_visibility')) { ?>
                        <div class="alert alert-info"><?= $this->session->flashdata('alipay_visibility') ?></div>
                    <?php } ?>                      
                    <form method="POST" action="">
                        <input type="hidden" name="alipay_visibility" value="<?= htmlspecialchars($alipay_visibility) ?>">
                        <input <?= $alipay_visibility == 1 ? 'checked' : '' ?> data-toggle="toggle" data-for-field="alipay_visibility" class="toggle-changer" type="checkbox">
                        <button class="btn btn-default" value="" type="submit">
                            Save
                        </button>                      
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">Alipay sandbox mode (use for alipay account tests)</div>
                <div class="panel-body">
                    <?php if ($this->session->flashdata('alipay_sandbox')) { ?>
                        <div class="alert alert-info"><?= $this->session->flashdata('alipay_sandbox') ?></div>
                    <?php } ?>                        
                    <form method="POST" action="">
                        <input type="hidden" name="alipay_sandbox" value="<?= htmlspecialchars($alipay_sandbox) ?>">
                        <input <?= $alipay_sandbox == 1 ? 'checked' : '' ?> data-toggle="toggle" data-for-field="alipay_sandbox" class="toggle-changer" type="checkbox">
                        <button class="btn btn-default" value="" type="submit">
                            Save
                        </button>                        
                    </form>
                </div>
            </div>
        </div>         
    </div>    
    <hr>
    <h3>Paypal Account Settings</h3>
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">Paypal sandbox mode (use for paypal account tests)</div>
                <div class="panel-body">
                    <?php if ($this->session->flashdata('paypal_sandbox')) { ?>
                        <div class="alert alert-info"><?= htmlspecialchars($this->session->flashdata('paypal_sandbox')) ?></div>
                    <?php } ?>
                    <form method="POST" action="">
                        <input type="hidden" name="paypal_sandbox" value="<?= htmlspecialchars($paypal_sandbox) ?>">
                        <input <?= $paypal_sandbox == 1 ? 'checked' : '' ?> data-toggle="toggle" data-for-field="paypal_sandbox" class="toggle-changer" type="checkbox">
                        <button class="btn btn-default" value="" type="submit">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">Paypal business email</div>
                <div class="panel-body">
                    <?php if ($this->session->flashdata('paypal_email')) { ?>
                        <div class="alert alert-info"><?= htmlspecialchars($this->session->flashdata('paypal_email')) ?></div>
                    <?php } ?>
                    <form method="POST" action="">
                        <div class="input-group">
                            <input class="form-control" placeholder="Leave empty for no paypal available method" name="paypal_email" value="<?= htmlspecialchars($paypal_email) ?>" type="text">
                            <span class="input-group-btn">
                                <button class="btn btn-default" value="" type="submit">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </div>
    <hr>
    <h3>Bank Account Settings</h3>
    <div class="row">
        <div class="col-sm-6">
            <?php if ($this->session->flashdata('bank_account')) { ?>
                <div class="alert alert-info"><?= $this->session->flashdata('bank_account') ?></div>
            <?php } ?>
            <form method="POST" action="">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td colspan="2"><b>Pay to - Recipient name/ltd</b></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="text" name="name" value="<?= $bank_account != null ? $bank_account['name'] : '' ?>" class="form-control" placeholder="Example: BoxingTeam Ltd."></td>
                            </tr>
                            <tr>
                                <td><b>IBAN</b></td>
                                <td><b>BIC</b></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" value="<?= $bank_account != null ? $bank_account['iban'] : '' ?>" name="iban" placeholder="Example: BG11FIBB329291923912301230"></td>
                                <td><input type="text" class="form-control" value="<?= $bank_account != null ? $bank_account['bic'] : '' ?>" name="bic" placeholder="Example: FIBBGSF"></td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Bank</b></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="text" value="<?= $bank_account != null ? $bank_account['bank'] : '' ?>" name="bank" class="form-control" placeholder="Example: First Investment Bank"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <input type="submit" class="form-control" value="Save Bank Account Settings">
            </form>
        </div>
    </div>
<?php } ?>
<!-- Modal for more info buttons in orders -->
<div class="modal fade" id="modalPreviewMoreInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Preview <b id="client-name"></b></h4>
            </div>
            <div class="modal-body" id="preview-info-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/bootstrap-toggle.min.js') ?>"></script>
<div class="modal fade" id="addExpressNo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" action="<?= base_url('admin/orders') ?>">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">;
                <input type="hidden" name="delivery_time" value="<?= time() ?>">;      
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=lang('order_delivery') ?></h4>
                </div>
                <div class="modal-body">
                    <?php if ($this->session->flashdata('error')) { ?>
                        <div class="alert alert-danger"><?= implode('<br>', $this->session->flashdata('error')) ?></div>
                    <?php } ?>
                    <div class="form-group">
                        <b style="font-size: large;color: red; ">*</b><label><?= lang('express_id') ?></label>
                        <select class="selectpicker form-control show-tick show-menu-arrow" name="express_id"> 
                            <option value="" style="display: none">请选择物流公司</option>
                            <?php foreach ($expresses as $express) { ?>
                                <option value="<?= $express['express_id'] ?>"><?= htmlspecialchars($express['express_name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <b style="font-size: large;color: red">*</b><label><?= lang('express_no') ?></label>
                        <input type="text"  name="express_no" value="<?= isset($_POST['express_no']) ? htmlspecialchars($_POST['express_no']) : '' ?>" class="form-control">
                    </div>
                    <div class="form-group">请手动录入物流单号或快递单号</div>                        
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="location.href = '<?= base_url('admin/orders') ?>';" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" name="submit" class="btn btn-primary">确定</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
                        $(document).ready(function () {
                            $('[data-toggle="tooltip"]').tooltip();
<?php if (isset($_POST['express_no'])) { ?>
                                $('#addExpressNo').modal('show');
<?php } ?>
                        });
</script>