<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Orders extends VENDOR_Controller
{
    // 待支付
    const PAYSTATUS_PENDING = 10;

    // 支付成功
    const PAYSTATUS_SUCCESS = 20;   

    // 未发货
    const NOT_DELIVERED = 10;

    // 已发货
    const DELIVERED = 20;
    
    // 未收货
    const NOT_RECEIVED = 10;

    // 已收货
    const RECEIVED = 20;    
    
    const QueryOrderTypeDesc = array(
        self::QUERY_ORDER_TYPE_ALL => "所有订单" ,
        self::QUERY_ORDER_TYPE_DELIVERY => "待发货" ,
        self::QUERY_ORDER_TYPE_RECEIPT => "待收货" ,
        self::QUERY_ORDER_TYPE_UNPAY => "未支付" ,
        self::QUERY_ORDER_TYPE_COMPLETED => "已完成" ,                        
        self::QUERY_ORDER_TYPE_CANCELED => "已取消" ,
        self::QUERY_ORDER_TYPE_AFTERSALES => "售后管理",     
    );
    
    private $num_rows = 10;

    public function __construct()
    {
        parent::__construct();
    }

    public function index($page = 0)
    {

        $data = array();
        $head = array();
        $queryOrderType = isset($_GET["queryOrderType"])? $_GET["queryOrderType"]:"-1";
        $head['title'] = self::QueryOrderTypeDesc[$queryOrderType];
        $head['description'] = self::QueryOrderTypeDesc[$queryOrderType];
        $head['keywords'] = '';
        if (isset($_POST['express_no'])) {
            $this->orderDelivery();
        }
        if ($this->session->flashdata('post')) {
            $_POST = $this->session->flashdata('post');
        }      
        $rowscount = $this->Orders_model->ordersCount($_GET, $this->vendor_id);
        $data['page'] = $page;        
        $data['orders'] = $this->Orders_model->orders($this->num_rows, $page, $_GET, $this->vendor_id);
        $data['links_pagination'] = pagination('vendor/orders', $rowscount, $this->num_rows, 3);        
        $data['expresses'] = $this->Public_model->getAllExpress();
        $data['queryOrderType'] = $queryOrderType;
        $this->load->view('_parts/header', $head);
        $this->load->view('orders', $data);
        $this->load->view('_parts/footer');
    }

    public function getProductInfo($product_id, $vendor_id)
    {
        return $this->Products_model->getOneProduct($product_id, $vendor_id);
    }

    public function changeOrdersOrderStatus()
    {
        $result = $this->Orders_model->changeOrderStatus($_POST['the_id'], $_POST['to_status']);
        if ($result == false) {
            echo '0';
        } else {
            echo '1';
        }
    }
    
    public function orderDelivery()
    {
        $isValid = $this->validateExpress();
        if ($isValid === true) {
            $_POST["delivery_status"] = self::DELIVERED;
            $this->Orders_model->updateOrderDeliveryStatus($_POST);
            $this->session->set_flashdata('success', 'Changes are saved');
        } else {
            $this->session->set_flashdata('error', $isValid);
            $this->session->set_flashdata('post', $_POST);
        }
        redirect('vendor/orders');        
    }

    public function orderReceipt()
    {
        $_POST["receipt_status"] = self::RECEIVED;
        $_POST["order_id"] = $_GET["order_id"];
        $pay_status_array =  $this->Orders_model->getOrderPayStatus($_POST["order_id"]);
        $delivery_status_array = $this->Orders_model->getOrderDeliveryStatus($_POST["order_id"]);
        if($pay_status_array['pay_status'] != self::PAYSTATUS_SUCCESS || $delivery_status_array['delivery_status'] != self::RECEIVED){
            $this->session->set_flashdata('error', "订单未付款或未发货，确认收货有误");           
        }
        else{
            $this->Orders_model->updateOrderReceiptStatus($_POST);                
        }
        redirect('userorders?queryOrderType='.$_GET["queryOrderType"]); 
    }
    
    private function validateExpress()
    {
        $errors = array();
        if (empty($_POST['express_id'])) {
            $errors[] = '物流公司为空';
        }
        if (empty($_POST['express_no'])) {
            $errors[] = '物流单号为空';
        }
        if (empty($_POST['order_id'])) {
            $errors[] = '订单号为空';
        }
        else{
            $result = $this->Orders_model->getOrderPayStatus($_POST['order_id']);
            if($result["pay_status"] == self::PAYSTATUS_PENDING){
                $errors[] = '订单未付款，不能发货';                            
            }
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }    
}
