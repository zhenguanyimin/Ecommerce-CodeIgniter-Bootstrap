<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$waitTime = 3; 
$vendor_home_url = LANG_URL . '/vendor/me';
header("Refresh:$waitTime;url=$vendor_home_url");
?>
<div class="container">
    <?= purchase_steps(1, 2, 3) ?>
    <div class="alert alert-success"><?= lang('bond_success_msg') ?></div>
</div>