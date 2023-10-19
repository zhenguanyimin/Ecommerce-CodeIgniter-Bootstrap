<div id="users">
    <h1><img src="<?= base_url('assets/imgs/admin-user.png') ?>" class="header-img" style="margin-top:-3px;"> Admin Vendors List</h1> 
    <hr>
    <div class="clearfix"></div>
    <?php
    $bondStatus = array(
        0 => "未缴纳",
        1 => "已缴纳",
        2 => "已退还",
    );    

    $vendorStatus = array(
        1 => "正常",
        2 => "已销户",
    );   

    $onlineStatus = array(
        0 => "离线",
        1 => "在线",
    );   
    
    if ($vendors->result()) {
        ?>
        <div class="table-responsive">
            <table class="table table-striped custab table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th><?=lang('vendor_alipay_account') ?></th>
                        <th><?=lang('vendor_real_name') ?></th>
                        <th><?=lang('vendor_phone') ?></th>
                        <th><?=lang('vendor_IDCard') ?></th>
                        <th><?=lang('vendor_weixin') ?></th>
                        <th><?=lang('vendor_bond_status') ?></th>
                        <th><?=lang('vendor_status') ?></th>
                        <th><?=lang('online_status') ?></th>
                        <th><?=lang('login_at') ?></th>
                        <th><?=lang('logout_at') ?></th>
                        <th>Sold products amount</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <?php 
                $i = 0;
                foreach ($vendors->result() as $vendor) { 
                    ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= $vendor->id ?></td>
                        <td><?= isset($vendor->name) ? $vendor->name : 'Vendor name is empty' ?></td>
                        <td><?= $vendor->email ?></td>
                        <td><?= $vendor->vendor_alipay_account ?></td>
                        <td><?= $vendor->vendor_real_name ?></td>
                        <td><?= $vendor->vendor_phone ?></td>
                        <td><?= $vendor->vendor_IDCard ?></td>
                        <td><?= $vendor->vendor_weixin ?></td>
                        <td><span class="<?= $vendor->bond_status == 1 ? "ant-tag-green":"ant-tag"?>" ><?= array_key_exists($vendor->bond_status, $bondStatus)? $bondStatus[$vendor->bond_status]:"未知"?></span></td>                        
                        <td><span class="<?= $vendor->vendor_status == 1 ? "ant-tag-green":"ant-tag"?>" ><?= array_key_exists($vendor->vendor_status, $vendorStatus)? $vendorStatus[$vendor->vendor_status]:"未知"?></span></td>
                        <td><span class="<?= $vendor->online_status == 1 ? "ant-tag-green":"ant-tag"?>" ><?= array_key_exists($vendor->online_status, $onlineStatus)? $onlineStatus[$vendor->online_status]:"未知"?></span></td>
                        <td><?= date('Y-m-d H:i:s', $vendor->login_at) ?></td>
                        <td><?= date('Y-m-d H:i:s', $vendor->logout_at) ?></td>
                        <td>
                            <?php
                             $orders = $controller->getVendorOrders($vendor->id); 
                             if(!count($orders)) {
                                ?>
                                <span class="label label-danger">No orders</span>
                                <?php
                             } else {
                                $countSales = 0;
                                foreach($orders as $order) {
                                    $product = unserialize($order['products']);
                                    foreach ($product as $key => $value) {
                                        $countSales += (int)$value;
                                    }
                                ?>
                                <span class="label label-success"><?= $countSales ?></span>
                            <?php }
                             }
                            ?>    
                        </td>
                        <td><?= $vendor->created_at ?></td>
                    </tr>
                <?php 
                $i++;
                } ?>
            </table>
        </div>
    <?php } else { ?>
        <div class="clearfix"></div><hr>
        <div class="alert alert-info">No vendors found!</div>
    <?php } ?>
</div>