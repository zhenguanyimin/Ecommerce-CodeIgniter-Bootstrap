<div id="users">
    <h1><img src="<?= base_url('assets/imgs/admin-user.png') ?>" class="header-img" style="margin-top:-3px;"> Admin Users List</h1> 
    <hr>
    <div class="clearfix"></div>
    <?php
    
    $userStatus = array(
        1 => "正常",
        2 => "已销户",
    );   

    $onlineStatus = array(
        0 => "离线",
        1 => "在线",
    );   
    
    if ($users->result()) {
        ?>
        <div class="table-responsive">
            <table class="table table-striped custab table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>序号</th>                        
                        <th>用户ID</th>
                        <th>姓名</th>
                        <th>邮箱</th>
                        <th>手机号码</th>
                        <th>密码</th>
                        <th><?=lang('user_status') ?></th>
                        <th><?=lang('online_status') ?></th>
                        <th><?=lang('login_at') ?></th>
                        <th><?=lang('logout_at') ?></th>                        
                        <th>创建时间</th>
                    </tr>
                </thead>
                <?php 
                $i = 0;
                foreach ($users->result() as $user) { ?>
                    <tr>
                        <td><?= $i+1 ?></td>                        
                        <td><?= $user->id ?></td>
                        <td><?= isset($user->name) ? $user->name : 'User name is empty' ?></td>
                        <td><?= $user->email ?></td>
                        <td><?= $user->phone ?></td>
                        <td><?= $user->password ?></td>
                        <td><span class="<?= $user->status == 1 ? "ant-tag-green":"ant-tag"?>" ><?= array_key_exists($user->status, $userStatus)? $userStatus[$user->status]:"未知"?></span></td>
                        <td><span class="<?= $user->online_status == 1 ? "ant-tag-green":"ant-tag"?>" ><?= array_key_exists($user->online_status, $onlineStatus)? $onlineStatus[$user->online_status]:"未知"?></span></td>
                        <td><?= date('Y-m-d H:i:s', $user->login_at) ?></td>
                        <td><?= date('Y-m-d H:i:s', $user->logout_at) ?></td>                        
                        <td><?= $user->created ?></td>
                    </tr>
                <?php 
                $i++;
                } ?>
            </table>
        </div>
    <?php } else { ?>
        <div class="clearfix"></div><hr>
        <div class="alert alert-info">No users found!</div>
    <?php } ?>
</div>