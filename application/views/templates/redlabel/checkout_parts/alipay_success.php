<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
    <?= purchase_steps(1, 2, 3) ?>
    <div class="alert alert-success"><?= lang('pay_success_msg') ?></div>
</div>