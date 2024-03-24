<script src="<?= base_url('assets/highcharts/highcharts.js') ?>"></script>
<script src="<?= base_url('assets/highcharts/data.js') ?>"></script>
<script src="<?= base_url('assets/highcharts/drilldown.js') ?>"></script>
<h1><img src="<?= base_url('assets/imgs/admin-home.png') ?>" class="header-img" style="margin-top:-3px;"> Home</h1>
<hr>
<div class="home-page">
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class="active">
                    <i class="fa fa-dashboard"></i> Dashboard - Statistics Overview
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-check fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= number_format($total_commission, 2).CURRENCY ?></div>
                            <div>平台总佣金</div>                            
                            <div ><?= "平台销售总金额：".number_format($total_amount, 2).CURRENCY ?></div>
                            <div ><?= "商户分成总金额：".number_format($total_vendor_share, 2).CURRENCY ?></div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/orders') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-calculator fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $total_login_users ?></div>
                            <div>当前在线用户数</div>
                            <div ><?= "注册用户数：".$total_users."人" ?></div>
                            <div ><?= "有效用户数：".$total_valid_users."人" ?></div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/listusers') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $payed_orders ?></div>
                            <div>当前付款订单数</div>
                            <div ><?= "未付款订单数：".$unpay_orders ."笔" ?></div>
                            <div ><?= "当前订单总数：".$all_orders."笔" ?></div> 
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/orders') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-product-hunt fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $total_login_vendors ?></div>
                            <div>当前在线商户数</div>
                            <div ><?= "注册商户数：".$total_vendors."人" ?></div>
                            <div ><?= "有效商户数：".$total_valid_vendors."人" ?></div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/listvendors') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>        
    </div>    
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-clock-o fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">
                                <div style="font-size: 25px;"><?= date('d.m.Y', $_SESSION['last_login']) ?></div>
                                <div style="font-size: 16px;"><?= date('H:i:s', $_SESSION['last_login']) ?></div>
                            </div>
                            <div>Last login!</div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/adminusers') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-envelope-o fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div><?= '日用户访问数:'.$user_visit_count_by_day ?></div>
                            <div><?= '月用户访问数:'.$user_visit_count_by_month ?></div>
                            <div><?= '日商户访问数:'.$vendor_visit_count_by_day ?></div>
                            <div><?= '月商户访问数:'.$vendor_visit_count_by_month ?></div>                            
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/visitCount') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $newOrdersCount ?></div>
                            <div>New Orders!</div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/orders') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-sort-numeric-desc fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $lowQuantity ?></div>
                            <div>Low Quantity Products!<br> (lower than 5)</div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('admin/products?orderby=quantity=asc') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-long-arrow-right fa-fw"></i> Most Orders By Payment Type</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Payment type</th>
                                    <th>Num Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($ordersByPaymentType)) {
                                    foreach ($ordersByPaymentType as $paymentT) {
                                        ?>
                                        <tr>
                                            <td><?= $paymentT['payment_type'] ?></td>
                                            <td><?= $paymentT['num'] ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="2">No Orders</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> Last Activity Log</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($activity->result()) {
                                    foreach ($activity->result() as $action) {
                                        ?>
                                        <tr>
                                            <td><i class="fa fa-user" aria-hidden="true"></i> <b><?= $action->username ?></b></td>
                                            <td><?= $action->activity . ' on ' . date('d.m.Y / H.m.s', $action->time) ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="2">No history found!</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="<?= base_url('admin/history') ?>">View All Activity <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-money fa-fw"></i> Most Sold</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Sales</th>
                                    <th>Url</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($mostSold)) {
                                    foreach ($mostSold as $product) {
                                        ?>
                                        <tr>
                                            <td><?= $product['procurement'] ?></td>
                                            <td><a target="_blank" href="<?= base_url($product['url']) ?>"><?= base_url($product['url']) ?></a></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="2">No Orders</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="<?= base_url('admin/products?orderby=procurement=desc') ?>">View All Products <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>