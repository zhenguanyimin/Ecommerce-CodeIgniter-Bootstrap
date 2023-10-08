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
        <ul class="user_navbar" style="margin-left: -100px;">
            <li class="active"><a href="<?= base_url('/myaccount') ?>">个人资料</a></li>
            <li><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["所有订单"] ?>">所有订单</a></li>
            <li><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["待发货"] ?>">待发货</a></li>
            <li><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["待收货"] ?>">待收货</a></li>
            <li><a href="<?= base_url('/userorders?queryOrderType=').$queryOrderTypes["已完成"] ?>">待评价</a></li>         
        </ul>
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
            <?= $links_pagination ?>
        </div>
    </div>  
</div>
</div>