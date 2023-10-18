<?php

class Orders_model extends CI_Model
{
    // 进行中
    const NORMAL = 10;

    // 已取消
    const CANCELLED = 20;

    // 待取消
    const APPLY_CANCEL = 21;

    // 已完成
    const COMPLETED = 30;

    //订单类型   
    const QUERY_ORDER_TYPE_ALL = -1;
    const QUERY_ORDER_TYPE_DELIVERY = 10;
    const QUERY_ORDER_TYPE_RECEIPT = 20;
    const QUERY_ORDER_TYPE_UNPAY = 30;
    const QUERY_ORDER_TYPE_COMPLETED = 40;
    const QUERY_ORDER_TYPE_CANCELED = 50;
    const QUERY_ORDER_TYPE_AFTERSALES = 60;    
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
    }

    public function ordersCount($onlyNew = false)
    {
        if ($onlyNew == true) {
            $this->db->where('viewed', 0);
        }
        return $this->db->count_all_results('orders');
    }

    public function getTotalAmount()
    {
        $this->db->where('order_status', self::COMPLETED);
        $this->db->where('order_source !=', 20);
        $this->db->select_sum('total_amount', 'total');
        $query = $this->db->get('orders');
        $result = $query->row_array();
        return $result['total'] > 0 ?$result['total']:0.0;
    }
    
    public function getTotalVendorShare()
    {
        $this->db->where('order_status', self::COMPLETED);
        $this->db->select_sum('vendor_share', 'total');
        $query = $this->db->get('orders');
        $result = $query->row_array();
        return $result['total'] > 0 ?$result['total']:0.0;
    }

    public function getTotalCommission()
    {
        $this->db->where('order_status', self::COMPLETED);
        $this->db->select_sum('commission', 'total');
        $query = $this->db->get('orders');
        $result = $query->row_array();
        return $result['total'] > 0 ?$result['total']:0.0;
    }
    
    public function orders($limit, $page, $big_get = [], $order_by)
    {
        if ($order_by != null) {
            $this->db->order_by($order_by, 'DESC');
        } else {
            $this->db->order_by('id', 'DESC');
        }
        $this->db->select('orders.*, orders_clients.name,'
                . 'orders_clients.email, orders_clients.phone, '
                . 'orders_clients.address, orders_clients.city, orders_clients.post_code,'
                . 'orders_clients.notes, discount_codes.type as discount_type, discount_codes.amount as discount_amount');
        $this->db->join('orders_clients', 'orders_clients.for_id = orders.id', 'inner');
        $this->db->join('users_public', 'users_public.id = orders.user_id', 'inner');        
        $this->db->join('discount_codes', 'discount_codes.code = orders.discount_code', 'left');
        // 检索查询条件
        $query = $big_get;        
        $this->queryFilter($query);        
        $result = $this->db->get('orders', $limit, $page);
        $result = $result->result_array();
        if(!count($result)) return $result;
        
        foreach($result as $k => $v) {
            $result[$k] = array_map(function($v) {
                $d = $this->encryption->decrypt($v);
                return $d !== false ? $d : $v;
            }, $v);
        }

        return $result;
    }

    /**
     * 设置默认的检索数据
     * @param array $query
     * @param array $default
     * @return array
     */
    protected function setQueryDefaultValue(array $query, array $default = []): array
    {
        $data = array_merge($default, $query);
        foreach ($query as $field => $value) {
            // 不存在默认值跳出循环
            if (!isset($default[$field])) continue;
            // 如果传参为空, 设置默认值
            if (empty($value) && $value !== '0') {
                $data[$field] = $default[$field];
            }
        }
        return $data;
    }
    
    /**
     * 设置检索查询条件
     * @param array $param
     * @return 
     */
    private function queryFilter(array $param)
    {
        // 默认参数
        $params = $this->setQueryDefaultValue($param, [
            'searchType' => '',     // 关键词类型 (10订单号 20客户姓名 30客户手机号 40客户邮箱 50收货人姓名 60收货人手机号 70收货人邮箱)
            'searchValue' => '',    // 关键词内容
            'orderSource' => -1,    // 订单来源
            'payType' => -1,        // 支付方式
            'deliveryType' => -1,   // 配送方式
            'start_time' => '',     // 起始时间
            'end_time' => '',       // 截止时间
            'queryOrderType' => -1,   // 订单类型(-1所有订单 10待发货 20待收货 30未支付 40已完成 50已取消 60售后管理)
        ]);
        // 检索查询条件
        $filter = [];
        // 关键词
        if (!empty($params['searchValue'])) {
            $searchWhere = [
                10 => ['orders.order_id like', "%{$params['searchValue']}%"],
                20 => ['users_public.name like', "%{$params['searchValue']}%"],
                30 => ['users_public.phone =', (int)$params['searchValue']],
                40 => ['users_public.email like', "%{$params['searchValue']}%"],                        
                50 => ['orders_clients.receiptor_name like', "%{$params['searchValue']}%"],
                60 => ['orders_clients.phone=', (int)$params['searchValue']],
                70 => ['orders_clients.email like', "%{$params['searchValue']}%"],                
            ];
            array_key_exists($params['searchType'], $searchWhere) && $filter[] = $searchWhere[$params['searchType']];
        }
        // 起止时间
        if ($params['start_time'] != '') {
            $start_time = \DateTime::createFromFormat('Y-m-d', $params['start_time']);
            if($start_time) {
                $time = $start_time->getTimestamp();
                $filter[] = ['vendors_orders.date >=', $time];
            }
        }        
        if ($params['end_time'] != '') {
            $end_time = \DateTime::createFromFormat('Y-m-d', $params['end_time']);
            if($end_time) {
                $time = $end_time->getTimestamp();
                $filter[] = ['vendors_orders.date <', $time];
            }
        }
        
        // 订单来源
        $params['orderSource'] > -1 && $filter[] = ['order_source =', (int)$params['orderSource']];
        // 支付方式
        $params['payType'] > -1 && $filter[] = ['pay_type =', (int)$params['payType']];
        // 配送方式
        $params['deliveryType'] > -1 && $filter[] = ['delivery_type =', (int)$params['deliveryType']];
        
        //订单查询类型
        if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_DELIVERY){
            $filter[] = ['pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['delivery_status =', self::NOT_DELIVERED];
        }
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_RECEIPT){
            $filter[] = ['pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['delivery_status =', self::DELIVERED];
            $filter[] = ['receipt_status =', self::NOT_RECEIVED];
        }
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_UNPAY){
            $filter[] = ['pay_status =', self::PAYSTATUS_PENDING];
            $filter[] = ['delivery_status =', self::NOT_DELIVERED];
            $filter[] = ['receipt_status =', self::NOT_RECEIVED];
            $filter[] = ['order_status =', self::NORMAL];
        }        
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_COMPLETED){
            $filter[] = ['pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['delivery_status =', self::DELIVERED];
            $filter[] = ['receipt_status =', self::RECEIVED];
            $filter[] = ['order_status =', self::COMPLETED];
        }        
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_CANCELED){
            $filter[] = ['pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['delivery_status =', self::DELIVERED];
            $filter[] = ['receipt_status =', self::RECEIVED];
            $filter[] = ['order_status =', self::CANCELLED];
        }
        
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_AFTERSALES){
            $filter[] = ['pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['delivery_status =', self::DELIVERED];
            $filter[] = ['receipt_status =', self::RECEIVED];
            $filter[] = ['order_status =', self::APPLY_CANCEL];
        }
        
        foreach($filter as $v) {
            $this->db->where($v[0], $v[1]);
        }
    }
    
    public function changeOrderStatus($id, $to_status)
    {
        $this->db->where('id', $id);
        $this->db->select('processed');
        $result1 = $this->db->get('orders');
        $res = $result1->row_array();

        $result = true;
        if ($res['processed'] != $to_status) {
            $this->db->where('id', $id);
            $result = $this->db->update('orders', array('processed' => $to_status, 'viewed' => '1'));
            if ($result == true) {
                $this->manageQuantitiesAndProcurement($id, $to_status, $res['processed']);
            }
        }
        return $result;
    }

    public function getOrderPayStatus($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->select('pay_status');
        $this->db->limit(1);
        $result1 = $this->db->get('orders');
        return $result1->row_array();
    }
    
    private function manageQuantitiesAndProcurement($id, $to_status, $current)
    {
        if (($to_status == 0 || $to_status == 2) && $current == 1) {
            $operator = '+';
            $operator_pro = '-';
        }
        if ($to_status == 1) {
            $operator = '-';
            $operator_pro = '+';
        }
        $this->db->select('products');
        $this->db->where('id', $id);
        $result = $this->db->get('orders');
        $arr = $result->row_array();
        $products = unserialize($arr['products']);
        foreach ($products as $product) {
                if (isset($operator)) {
                    if (!$this->db->query('UPDATE products SET quantity=quantity' . $operator . $product['product_quantity'] . ' WHERE id = ' . $product['product_info']['id'])) {
                        log_message('error', print_r($this->db->error(), true));
                        show_error(lang('database_error'));
                    }
                }
                if (isset($operator_pro)) {
                    if (!$this->db->query('UPDATE products SET procurement=procurement' . $operator_pro . $product['product_quantity'] . ' WHERE id = ' . $product['product_info']['id'])) {
                        log_message('error', print_r($this->db->error(), true));
                        show_error(lang('database_error'));
                    }
                } 
        }
    }

    public function setBankAccountSettings($post)
    {
        $query = $this->db->query('SELECT id FROM bank_accounts');
        if ($query->num_rows() == 0) {
            $id = 1;
        } else {
            $result = $query->row_array();
            $id = $result['id'];
        }
        $post['id'] = $id;
        if (!$this->db->replace('bank_accounts', $post)) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
    }

    public function getBankAccountSettings()
    {
        $result = $this->db->query("SELECT * FROM bank_accounts LIMIT 1");
        return $result->row_array();
    }

    public function deleteOrder($id)
    {
        $id = (int) $id;
        $this->db->trans_begin();
        $this->db->where('id', $id)->delete('orders');
        $this->db->where('for_id', $id)->delete('orders_clients');
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            show_error(lang('database_error'));
        } else {
            $this->db->trans_commit();
        }
    }
}
