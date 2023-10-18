<div id="users">
    <h1><img src="<?= base_url('assets/imgs/admin-user.png') ?>" class="header-img" style="margin-top:-3px;"> Admin Users List</h1> 
    <hr>
    <div class="clearfix"></div>
    <?php
    if ($users->result()) {
        ?>
        <div class="table-responsive">
            <table class="table table-striped custab">
                <thead>
                    <tr>
                        <th>用户ID</th>
                        <th>姓名</th>
                        <th>邮箱</th>
                        <th>手机号码</th>
                        <th>密码</th>
                        <th>创建时间</th>
                    </tr>
                </thead>
                <?php foreach ($users->result() as $user) { ?>
                    <tr>
                        <td><?= $user->id ?></td>
                        <td><?= isset($user->name) ? $user->name : 'User name is empty' ?></td>
                        <td><?= $user->email ?></td>
                        <td><?= $user->phone ?></td>
                        <td><?= $user->password ?></td>
                        <td><?= $user->created ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } else { ?>
        <div class="clearfix"></div><hr>
        <div class="alert alert-info">No users found!</div>
    <?php } ?>
</div>