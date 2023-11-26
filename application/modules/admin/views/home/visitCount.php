<div id="visitCount">
    <h1><img src="<?= base_url('assets/imgs/admin-user.png') ?>" class="header-img" style="margin-top:-3px;"> Admin Visit Count</h1> 
    <hr>
    <div class="clearfix"></div>
    <?php
        
    if ($users->result()) {
        ?>
        <div class="table-responsive">
            <table class="table table-striped custab table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>序号</th>                        
                        <th>访问者IP</th>                     
                        <th>访问URL</th>
                        <th>前URL</th>
                        <th>访问者地址</th>                           
                        <th>访问时间</th>
                        <th>访问者姓名</th>
                        <th>访问者邮箱</th>
                    </tr>
                </thead>
                <?php 
                $i = 0;
                foreach ($users->result() as $user) { ?>
                    <tr>
                        <td><?= $i+1 ?></td>                        
                        <td><?= $user->remote_addr ?></td>
                        <td><?= $user->request_uri ?></td>
                        <td><?= $user->http_referer ?></td>
                        <td><?= $user->remote_location ?></td>                        
                        <td><?= date('Y-m-d H:i:s', $user->visit_time) ?></td>
                        <td><?= isset($user->user_name) ? $user->user_name : 'User name is empty' ?></td>
                        <td><?= $user->email ?></td>
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