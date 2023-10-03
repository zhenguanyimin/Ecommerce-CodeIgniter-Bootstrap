<?php
$timeNow = time();
?>
<script src="<?= base_url('assets/ckeditor/ckeditor.js') ?>"></script>
<link rel="stylesheet" href="<?= base_url('assets/bootstrap-select-1.12.1/bootstrap-select.min.css') ?>">
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <?php
        if ($this->session->flashdata('result_publish')) {
            ?> 
            <div class="alert alert-success"><?= $this->session->flashdata('result_publish') ?></div> 
            <?php
        }
        ?>
        <div class="content">
            <form class="form-box" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" value="<?= isset($_POST['folder']) ? htmlspecialchars($_POST['folder']) : $timeNow ?>" name="folder">
                <div class="form-group">
                    <label><?= lang('express_id') ?></label>
                    <select class="selectpicker form-control show-tick show-menu-arrow" name="shop_categorie">
                        <?php foreach ($shop_categories as $key_cat => $shop_categorie) { ?>
                            <option <?= isset($_POST['shop_categorie']) && $_POST['shop_categorie'] == $key_cat ? 'selected=""' : '' ?> value="<?= $key_cat ?>">
                                <?php
                                foreach ($shop_categorie['info'] as $nameAbbr) {
                                    if ($nameAbbr['abbr'] == $this->config->item('language_abbr')) {
                                        echo $nameAbbr['name'];
                                    }
                                }
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>   
                <div class="form-group"><label><?= lang('express_no') ?></label></div>
                <div class="form-group">
                    <input type="text"  name="quantity" value="<?= isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '' ?>" class="form-control">
                </div>
                <div class="form-group">请手动录入物流单号或快递单号</div>                
                <button type="submit" name="Delivery" class="btn btn-green"><?= lang('order_delivery') ?></button>
            </form> 
        </div>
    </div>
</div>
<script src="<?= base_url('assets/bootstrap-select-1.12.1/js/bootstrap-select.min.js') ?>"></script>
