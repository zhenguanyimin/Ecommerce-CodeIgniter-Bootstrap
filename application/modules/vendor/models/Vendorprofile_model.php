<?php

class Vendorprofile_model extends CI_Model
{
    // 进行中
    const NORMAL = 10;

    // 已取消
    const CANCELLED = 20;

    // 待取消
    const APPLY_CANCEL = 21;

    // 已完成
    const COMPLETED = 30;

    public function __construct()
    {
        parent::__construct();
    }

    public function getVendorInfoFromEmail($email)
    {
        $this->db->where('email', $email);
        $result = $this->db->get('vendors');
        return $result->row_array();
    }
    
    public function getVendorInfoFromId($vendor_id)
    {
        $this->db->where('id', $vendor_id);
        $result = $this->db->get('vendors');
        return $result->row_array();
    }
    
    public function getVendorByUrlAddress($urlAddr)
    {
        $this->db->where('url', $urlAddr);
        $result = $this->db->get('vendors');
        return $result->row_array();
    }

    public function saveNewVendorDetails($post, $vendor_id)
    {
        if (!$this->db->where('id', $vendor_id)->update('vendors', array(
                    'name' => $post['vendor_name'],
                    'url' => $post['vendor_url']
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
    }

    public function isVendorUrlFree($vendorUrl)
    {
        $this->db->where('url', $vendorUrl);
        $num = $this->db->count_all_results('vendors');
        if ($num > 0) {
            return false;
        } else {
            return true;
        }
    }
    public function ordersCount($onlyNew = false, $vendor_id)
    {
        if ($onlyNew == true) {
            $this->db->where('viewed', 0);
        }
        $this->db->where('vendor_id', $vendor_id);         
        return $this->db->count_all_results('vendors_orders');
    }
    
    public function getTotalAmount($vendor_id)
    {
        $this->db->where('vendor_id', $vendor_id);   
        $this->db->where('order_status', self::COMPLETED);
        $this->db->where('order_source !=', 20);
        $this->db->select_sum('total_amount', 'total');
        $query = $this->db->get('vendors_orders');
        $result = $query->row_array();
        return $result['total'] > 0 ?$result['total']:0.0;
    }

    
    public function getTotalVendorShare($vendor_id)
    {
        $this->db->where('vendor_id', $vendor_id);
        $this->db->where('order_status', self::COMPLETED);
        $this->db->select_sum('vendor_share', 'total');
        $query = $this->db->get('vendors_orders');
        $result = $query->row_array();
        return $result['total'] > 0 ?$result['total']:0.0;
    }

    public function getTotalCommission($vendor_id)
    {
        $this->db->where('vendor_id', $vendor_id);        
        $this->db->where('order_status', self::COMPLETED);
        $this->db->select_sum('commission', 'total');
        $query = $this->db->get('vendors_orders');
        $result = $query->row_array();
        return $result['total'] > 0 ?$result['total']:0.0;
    }

    public function updateVendorStatus($vendor_id, $status)
    {
        $this->db->where('id', $vendor_id);
        if (!$this->db->update('vendors', array(
                    'vendor_status' => $status)
                )) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
    }
    
    public function updateVendorInfo($post)
    {
        $this->db->where('id', $post['vendor_id']);
        if (!$this->db->update('vendors', array(
                    'vendor_alipay_account' => $_POST['vendor_alipay_account'],
                    'vendor_real_name' => $_POST['vendor_real_name'],
                    'vendor_phone' => $_POST['vendor_phone'],
                    'vendor_IDCard' => $_POST['vendor_IDCard'],
                    'email' => $_POST['email'], 
                    'vendor_weixin' => $_POST['vendor_weixin']            
        ))) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error')); 
            return false;
        }
        return true;
    }
    
    public function getOrdersByMonth($vendor_id)
    {
        $vendor_id = (int)$vendor_id;
        $result = $this->db->query("SELECT YEAR(FROM_UNIXTIME(date)) as year, MONTH(FROM_UNIXTIME(date)) as month, COUNT(id) as num FROM vendors_orders WHERE vendor_id = $vendor_id GROUP BY YEAR(FROM_UNIXTIME(date)), MONTH(FROM_UNIXTIME(date)) ORDER BY year, month ASC");
        $result = $result->result_array();
        $orders = array();
        $years = array();
        if(!empty($result)) {
            foreach ($result as $res) {
                if (!isset($orders[$res['year']])) {
                    for ($i = 1; $i <= 12; $i++) {
                        $orders[$res['year']][$i] = 0;
                    }
                }
                $years[] = $res['year'];
                $orders[$res['year']][$res['month']] = $res['num'];
            }
        }
        return array(
            'years' => count($years) > 0 ? array_unique($years): [],
            'orders' => count($orders) > 0 ? $orders : [],
        );
    }

}
