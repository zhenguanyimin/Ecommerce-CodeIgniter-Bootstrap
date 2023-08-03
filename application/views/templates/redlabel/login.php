<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="inner-nav">
    <div class="container">
        <?= lang('home') ?> <span class="active"> > <?= lang('user_login') ?></span>
    </div>
</div>
<div class="container user-page">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
            <div class="loginmodal-container">
                <h1><?= lang('login_to_acc') ?></h1><br>
		<?php
		if ($this->session->flashdata('userError')) {
			?>
			<hr>
			<div class="alert alert-danger"><h4><span class="glyphicon glyphicon-alert"></span> <?= lang('finded_errors') ?></h4><?php
				echo $this->session->flashdata('userError') . '<br>';
				?>
			</div>
			<hr>
			<?php
		}
		?> 
		<form method="POST" action="">
                    <input type="text" name="email" placeholder="Email">
                    <input type="password" name="pass" placeholder="Password">
                    <input type="submit" name="login" class="login loginmodal-submit" value="<?= lang('login') ?>">
                </form> 
                <div class="login-help">
                    <a href="<?= LANG_URL . '/register' ?>"><?= lang('register') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
