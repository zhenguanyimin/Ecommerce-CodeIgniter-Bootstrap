<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Orders extends ADMIN_Controller
{

    private $num_rows = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('SendMail');
        $this->load->model(array('Orders_model', 'Home_admin_model'));
    }

    public function index($page = 0)
    {
        $this->login_check();
        $data = array();
        $head = array();
        $head['title'] = 'Administration - Orders';
        $head['description'] = '!';
        $head['keywords'] = '';

        $order_by = null;
        if (isset($_GET['order_by'])) {
            $order_by = $_GET['order_by'];
        }
        $rowscount = $this->Orders_model->ordersCount($_GET, $order_by);
        $data['page'] = $page;
        $data['orders'] = $this->Orders_model->orders($this->num_rows, $page, $_GET, $order_by);
        $data['links_pagination'] = pagination('admin/orders', $rowscount, $this->num_rows, 3);
        
        if (isset($_POST['express_no'])) {
            $this->orderDelivery();
        }
        
        if (isset($_POST['paypal_sandbox'])) {
            $this->Home_admin_model->setValueStore('paypal_sandbox', $_POST['paypal_sandbox']);
            if ($_POST['paypal_sandbox'] == 1) {
                $msg = 'Paypal sandbox mode activated';
            } else {
                $msg = 'Paypal sandbox mode disabled';
            }
            $this->session->set_flashdata('paypal_sandbox', $msg);
            $this->saveHistory($msg);
            redirect('admin/orders?settings');
        }
        if (isset($_POST['paypal_email'])) {
            $this->Home_admin_model->setValueStore('paypal_email', $_POST['paypal_email']);
            $this->session->set_flashdata('paypal_email', 'Public quantity visibility changed');
            $this->saveHistory('Change paypal business email to: ' . $_POST['paypal_email']);
            redirect('admin/orders?settings');
        }
        if (isset($_POST['cashondelivery_visibility'])) {
            $this->Home_admin_model->setValueStore('cashondelivery_visibility', $_POST['cashondelivery_visibility']);
            $this->session->set_flashdata('cashondelivery_visibility', 'Cash On Delivery Visibility Changed');
            $this->saveHistory('Change Cash On Delivery Visibility - ' . $_POST['cashondelivery_visibility']);
            redirect('admin/orders?settings');
        }
        if (isset($_POST['alipay_visibility'])) {
            $this->Home_admin_model->setValueStore('alipay_visibility', $_POST['alipay_visibility']);
            $this->session->set_flashdata('alipay_visibility', 'Alipay Visibility Changed');
            $this->saveHistory('Change Alipay Visibility - ' . $_POST['alipay_visibility']);
            redirect('admin/orders?settings');
        }
        if (isset($_POST['alipay_sandbox'])) {
            $this->Home_admin_model->setValueStore('alipay_sandbox', $_POST['alipay_sandbox']);
            $this->session->set_flashdata('alipay_sandbox', 'Alipay Sandbox Changed');
            $this->saveHistory('Change Alipay Sandbox - ' . $_POST['alipay_sandbox']);
            redirect('admin/orders?settings');
        }         
        if (isset($_POST['iban'])) {
            $this->Orders_model->setBankAccountSettings($_POST);
            $this->session->set_flashdata('bank_account', 'Bank account settings saved');
            $this->saveHistory('Bank account settings saved for : ' . $_POST['name']);
            redirect('admin/orders?settings');
        }
        $data['paypal_sandbox'] = $this->Home_admin_model->getValueStore('paypal_sandbox');
        $data['paypal_email'] = $this->Home_admin_model->getValueStore('paypal_email'); 
        $data['shippingAmount'] = $this->Home_admin_model->getValueStore('shippingAmount');
        $data['shippingOrder'] = $this->Home_admin_model->getValueStore('shippingOrder');
        $data['cashondelivery_visibility'] = $this->Home_admin_model->getValueStore('cashondelivery_visibility');
        $data['alipay_visibility'] = $this->Home_admin_model->getValueStore('alipay_visibility');
        $data['alipay_sandbox'] = $this->Home_admin_model->getValueStore('alipay_sandbox');
        $data['bank_account'] = $this->Orders_model->getBankAccountSettings();
        $data['expresses'] = $this->Public_model->getAllExpress();        
        $this->load->view('_parts/header', $head);
        $this->load->view('ecommerce/orders', $data);
        $this->load->view('_parts/footer');
        if ($page == 0) {
            $this->saveHistory('Go to orders page');
        }
    }

    public function deleteOrder($id)
    {
        $id = (int) $id;
        if($id == 0) {
            redirect('admin/orders');
        }
        
        $this->Orders_model->deleteOrder($id);
        redirect('admin/orders');
    }

    public function changeOrdersOrderStatus()
    {
        $this->login_check();

        $result = false;
        $sendedVirtualProducts = true;
        $virtualProducts = $this->Home_admin_model->getValueStore('virtualProducts');
        /*
         * If we want to use Virtual Products
         * Lets send email with download links to user email
         * In error logs will be saved if cant send email from PhpMailer
         */
        if ($virtualProducts == 1) {
            if ($_POST['to_status'] == 1) {
                $sendedVirtualProducts = $this->sendVirtualProducts();
            }
        }

        if ($sendedVirtualProducts == true) {
            $result = $this->Orders_model->changeOrderStatus($_POST['the_id'], $_POST['to_status']);
        }

        if ($result == true && $sendedVirtualProducts == true) {
            echo 1;
        } else {
            echo 0;
        }
        $this->saveHistory('Change status of Order Id ' . $_POST['the_id'] . ' to status ' . $_POST['to_status']);
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
        redirect('admin/orders');        
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
    
    private function sendVirtualProducts()
    {
        if(isset($_POST['products']) && $_POST['products'] != '') {
            $products = unserialize(html_entity_decode($_POST['products']));
            foreach ($products as $product_id => $product_quantity) {
                $productInfo = modules::run('admin/ecommerce/products/getProductInfo', $product_id);
                /*
                 * If is virtual product, lets send email to user
                 */
                if ($productInfo['virtual_products'] != null) {
                    if (!filter_var($_POST['userEmail'], FILTER_VALIDATE_EMAIL)) {
                        log_message('error', 'Ivalid customer email address! Cant send him virtual products!');
                        return false;
                    }
                    $result = $this->sendmail->sendTo($_POST['userEmail'], 'Dear Customer', 'Virtual products', $productInfo['virtual_products']);
                    return $result;
                }
            }
            return true;
        }
    }

}
