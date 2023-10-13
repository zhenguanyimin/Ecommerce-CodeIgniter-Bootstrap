<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container" id="checkout-page">
    <div class="row">
        <div class="col-sm-12">
            <form method="POST" action="" >
                <?php
                if ($this->session->flashdata('vendor_info_warning')) {
                    ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('vendor_info_warning') ?></div>
                    <?php
                }
                ?>                   
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
                if ($this->session->flashdata('result_publish')) {
                    ?> 
                    <div class="alert alert-success"><?= $this->session->flashdata('result_publish') ?></div> 
                    <?php
                }
                ?>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="alipayAccountInput"><?= lang('vendor_alipay_account') ?> (<sup><?= lang('requires') ?></sup>)</label>
                        <input id="alipayAccountInput" class="form-control" name="vendor_alipay_account" value="<?= $vendorInfo['vendor_alipay_account'] ?>" type="text" placeholder="<?= lang('vendor_alipay_account') ?>">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="realNameInput"><?= lang('vendor_real_name') ?> (<sup><?= lang('requires') ?></sup>)</label>
                        <input id="realNameInput" class="form-control" name="vendor_real_name" value="<?= $vendorInfo['vendor_real_name'] ?>" type="text" placeholder="<?= lang('vendor_real_name') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="phoneInput"><?= lang('vendor_phone') ?> (<sup><?= lang('requires') ?></sup>)</label>
                        <input id="phoneInput" class="form-control" name="vendor_phone" value="<?= $vendorInfo['vendor_phone'] ?>" type="text" placeholder="<?= lang('vendor_phone') ?>">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="IDCardInput"><?= lang('vendor_IDCard') ?> (<sup><?= lang('requires') ?></sup>)</label>
                        <input id="IDCardInput" class="form-control" name="vendor_IDCard" value="<?= $vendorInfo['vendor_IDCard'] ?>" type="text" placeholder="<?= lang('vendor_IDCard') ?>">
                    </div>                    
                </div>
                <div class="row">    
                    <div class="form-group col-sm-6">
                        <label for="emailAddressInput"><?= lang('email_address') ?> (<sup><?= lang('requires') ?></sup>)</label>
                        <input id="emailAddressInput" class="form-control" name="email" value="<?= $vendorInfo['email'] ?>" type="text" placeholder="<?= lang('email_address') ?>">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="weixinInput"><?= lang('vendor_weixin') ?> (<sup><?= lang('requires') ?></sup>)</label>
                        <input id="weixinAddressInput" class="form-control" name="vendor_weixin" value="<?= $vendorInfo['vendor_weixin'] ?>" type="text" placeholder="<?= lang('vendor_weixin') ?>">
                    </div>                   
                </div>
                <div>
                    <button type="submit" name="setVendorInfo" class="btn btn-green"><?= lang('save') ?></button>
                </div>                    
            </form>
        </div>
    </div>
</div>

