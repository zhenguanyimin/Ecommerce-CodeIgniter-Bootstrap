<?php if ($this->session->flashdata('update_vend_err')) { ?>
    <div class="alert alert-danger"><?= implode('<br>', $this->session->flashdata('update_vend_err')) ?></div>
<?php } ?>
<?php if ($this->session->flashdata('update_vend_details')) { ?>
    <div class="alert alert-success"><?= $this->session->flashdata('update_vend_details') ?></div>
<?php } ?>
<div class="home-page">
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class="active">
                    <i class="fa fa-dashboard"></i> 统计概述
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $total_vendor_share.CURRENCY ?></div>
                            <div>销售总金额</div>                            
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('vendor/orders') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">查看详情</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading fast-view-panel">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $newOrdersCount ?></div>
                            <div>新订单！</div>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('vendor/orders') ?>">
                    <div class="panel-footer">
                        <span class="pull-left">查看详情</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>        
    </div>       
    <script src="<?= base_url('assets/highcharts/highcharts.js') ?>"></script>
    <div id="container-by-month" style="min-width: 310px; height: 400px; margin: 0 auto;"></div>
    <script>
        /*
         * Chart for orders by mount/year 
         */
        $(function () {
            Highcharts.chart('container-by-month', {
                title: {
                    text: 'Monthly Orders',
                    x: -20
                },
                subtitle: {
                    text: 'Source: Orders table',
                    x: -20
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                yAxis: {
                    title: {
                        text: 'Orders'
                    },
                    plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                },
                tooltip: {
                    valueSuffix: ' Orders'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [
<?php foreach ($ordersByMonth['years'] as $year) { ?>
                        {
                            name: '<?= $year ?>',
                            data: [<?= implode(',', $ordersByMonth['orders'][$year]) ?>]
                        },
<?php } ?>
                ]
            });
        });
    </script>
</div>