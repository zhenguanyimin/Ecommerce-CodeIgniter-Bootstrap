<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
    <?= purchase_steps(1, 2) ?>
    <div class="alert alert-success"><?= lang('there_is_payment_error') ?></div>
</div>