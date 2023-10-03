<link rel="stylesheet" href="<?= base_url('assets/bootstrap-select-1.12.1/bootstrap-select.min.css') ?>">
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
    ); 
    $payTypes = array(
        -1 => "全部",
        20 => "支付宝支付",
    ); 
    $deliveryTypes = array(
        -1 => "全部",
        10 => "快递配送",
    );     
?>
<div class="table-operator">
<form class="form-horizontal" method="GET" action="<?= base_url('vendor/orders') ?>" id="bigger-search"/>
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="searchValue" title="关键词" class="">关键词</label>
                <select class="selectpicker" name="searchType">
                    <?php foreach ($searchTypes as $id => $name) { ?>
                        <option <?= isset($_GET['searchType']) && $_GET['searchType'] == $id ? 'selected' : '' ?> value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php } ?>                                                
                </select>        
                <input value="<?= isset($_GET['searchValue']) ? htmlspecialchars($_GET['searchValue']) : '' ?>" placeholder="请输入关键词" type="text"  name="searchValue" id="searchValue" style="height: 34px;">                
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="orderSource" title="订单来源" class="">订单来源</label>
                <select class="selectpicker" name="orderSource">
                    <?php foreach ($orderSources as $id => $name) { ?>
                        <option <?= isset($_GET['orderSource']) && $_GET['orderSource'] == $id ? 'selected' : '' ?> value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php } ?>                                                   
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="payType" title="支付方式" class="">支付方式</label>
                <select class="selectpicker" name="payType"> 
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
                <select class="selectpicker" name="deliveryType"> 
                    <?php foreach ($deliveryTypes as $id => $name) { ?>
                        <option <?= isset($_GET['deliveryType']) && $_GET['deliveryType'] == $id ? 'selected' : '' ?> value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php } ?>                                
                </select>
            </div>     
        </div>        
        <div class="col-sm-8">
            <div class="form-group">
                <label for="start_time">订单时间:</label> 
                <input type="date" value="<?= isset($_GET['start_time']) ? htmlspecialchars($_GET['start_time']) : '' ?>" name="start_time" id="start_time" style="width:20%; height: 34px;">
                <label for="end_time"> — </label>                        
                <input type="date" value="<?= isset($_GET['end_time']) ? htmlspecialchars($_GET['end_time']) : '' ?>" name="end_time" id="end_time" style="width: 20%; height: 34px;">
                <button type="submit" class="btn btn-inner-search">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"> 搜索</span>
                </button>
                <a class="btn btn-default" id="clear-form" href="javascript:void(0);">重置</a>             
            </div>                    
        </div>        
    </div>
</form>   
</div>
<div class="content orders-page">
    <table class="table">
        <thead class="blue-grey lighten-4">
            <tr>
                <th>订单号</th>
                <th><?= lang('time_created') ?></th>
                <th><?= lang('order_type') ?></th>
                <th><?= lang('phone') ?></th>
                <th><?= lang('status') ?></th>
                <th>订单总金额</th>
                <th>商户销售金额</th>
                <th>佣金金额</th>
                <th>运费</th>                  
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
                    <td><?= $order['order_id'] ?></td>
                    <td><?= date('d.m.Y', $order['date']) ?></td>
                    <td><?= $order['payment_type'] ?></td>
                    <td><?= $order['phone'] ?></td>
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
                    <td><?= $order['total_amount'] . CURRENCY ?></td>
                    <td><?= $order['vendor_share'] . CURRENCY ?></td>
                    <td><?= $order['commission'] . CURRENCY ?></td>
                    <td><?= $order['shipping_amount'] . CURRENCY ?></td>                     
                    <td class="text-right" >
                        <a href="javascript:void(0);" class="btn btn-sm btn-green show-more" data-show-tr="<?= $i ?>">
                            详情
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                            <i class="fa fa-chevron-up" aria-hidden="true"></i>
                        </a>
                        <?php if($order['pay_status'] == 20 && $order['delivery_status'] == 10){ ?>
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#addExpressNo"  class="btn btn-sm btn-green show-more">发货</a>                                
                        <?php }?>                     
                    </td>                     
                </tr>
                <tr class="tr-more" data-tr="<?= $i ?>">
                    <td colspan="6">
                        <div class="row">
                            <div class="col-sm-6">
                                <ul>
                                    <li>
                                        <b><?= lang('first_name') ?></b> <span><?= $order['first_name'] ?></span>
                                    </li>
                                    <li>
                                        <b><?= lang('last_name') ?></b> <span><?= $order['last_name'] ?></span>
                                    </li>
                                    <li>
                                        <b><?= lang('email') ?></b> <span><?= $order['email'] ?></span>
                                    </li>
                                    <li>
                                        <b><?= lang('phone') ?></b> <span><?= $order['phone'] ?></span>
                                    </li>
                                    <li>
                                        <b><?= lang('address') ?></b> <span><?= $order['address'] ?></span>
                                    </li>
                                    <li>
                                        <b><?= lang('city') ?></b> <span><?= $order['city'] ?></span>
                                    </li>
                                    <li>
                                        <b><?= lang('post_code') ?></b> <span><?= $order['post_code'] ?></span>
                                    </li>
                                    <li>
                                        <b><?= lang('notes') ?></b> <span><?= $order['notes'] ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <?php
                                $product = unserialize($order['products']);
                                foreach ($product as $prod_id => $prod_qua) {
                                    $productInfo = modules::run('vendor/orders/getProductInfo', $prod_id, $order['vendor_id']);
                                    ?>
                                    <div class="product">
                                        <a href="" target="_blank">
                                            <img src="<?= base_url('/attachments/shop_images/' . $productInfo['image']) ?>" alt="">
                                            <div class="info">
                                                <span class="qiantity">
                                                    <b><?= lang('quantity') ?></b> <?= $prod_qua ?>
                                                </span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="addExpressNo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
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
                    <button type="button" onclick="location.href = '<?= base_url('vendor/orders') ?>';" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" name="submit" class="btn btn-primary">确定</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/bootstrap-select-1.12.1/js/bootstrap-select.min.js') ?>"></script>
<script>
                        $(document).ready(function () {
                            $('[data-toggle="tooltip"]').tooltip();
<?php if (isset($_POST['express_no'])) { ?>
                                $('#addExpressNo').modal('show');
<?php } ?>
                        });
</script>