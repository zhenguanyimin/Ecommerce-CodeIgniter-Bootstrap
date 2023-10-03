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
    
    private $num_rows = 20;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Orders_model', 'Products_model'));
    }

    public function index($page = 0)
    {

        $data = array();
        $head = array();
        $head['title'] = lang('vendor_orders');
        $head['description'] = lang('vendor_orders');
        $head['keywords'] = '';
        if (isset($_POST['express_no'])) {
            $this->orderDelivery();
        }
        if ($this->session->flashdata('post')) {
            $_POST = $this->session->flashdata('post');
        }        
        $rowscount = $this->Orders_model->ordersCount($this->vendor_id);
        $data['orders'] = $this->Orders_model->orders($this->num_rows, $page, $_GET, $this->vendor_id);
        $data['expresses'] = $this->Public_model->getAllExpress();
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
            $_POST["delivery_status"] = DELIVERED;
            $this->Orders_model->updateOrderDeliveryStatus($_POST);
            $this->session->set_flashdata('success', 'Changes are saved');
        } else {
            $this->session->set_flashdata('error', $isValid);
            $this->session->set_flashdata('post', $_POST);
        }
        redirect('vendor/orders');        
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
