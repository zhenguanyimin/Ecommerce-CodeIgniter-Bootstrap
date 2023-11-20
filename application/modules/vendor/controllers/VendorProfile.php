<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class VendorProfile extends VENDOR_Controller
{
    CONST USER_STATUS_NORMAL = 1;
    CONST USER_STATUS_INVALID = 2;

    CONST VENDOR_STATUS_OFFLINE = 0;    
    CONST VENDOR_STATUS_ONLINE = 1;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vendorprofile_model');
        $visit_history = array();
        $visit_history['remote_addr'] = $_SERVER['REMOTE_ADDR'];
        $visit_history['request_uri'] = $_SERVER['REQUEST_URI'];
        $visit_history['user_name'] = $this->vendor_name? $this->vendor_name:'';
        $visit_history['email'] = '';
        $this->Public_model->setVisitHistory($visit_history);          
    }

    public function index()
    {

        $data = array();
        $head = array();
        $head['title'] = lang('vendor_dashboard');
        $head['description'] = lang('vendor_home_page');
        $head['keywords'] = '';
        $data['newOrdersCount'] = $this->Vendorprofile_model->ordersCount(true, $this->vendor_id);        
        $data['ordersByMonth'] = $this->Vendorprofile_model->getOrdersByMonth($this->vendor_id);
        $data['total_amount'] = $this->Vendorprofile_model->getTotalAmount($this->vendor_id);
        $data['total_vendor_share'] = $this->Vendorprofile_model->getTotalVendorShare($this->vendor_id);
        $data['total_commission'] = $this->Vendorprofile_model->getTotalCommission($this->vendor_id);
        $data['total_login_users'] = $this->Vendorprofile_model->getLoginUsers();
        $data['total_login_vendors'] = $this->Vendorprofile_model->getLoginVendors();
        $data['total_users'] = $this->Vendorprofile_model->getTotalUsers(self::USER_STATUS_NORMAL);
        $data['total_vendors'] = $this->Vendorprofile_model->getTotalVendors(self::USER_STATUS_NORMAL);
        $data['payed_orders'] = $this->Vendorprofile_model->getPayedOrdersCount($this->vendor_id);
        $data['unpay_orders'] = $this->Vendorprofile_model->getUnPayOrdersCount($this->vendor_id);
        $data['all_orders'] = $this->Vendorprofile_model->getOrdersCount($this->vendor_id);
        $this->load->view('_parts/header', $head);
        $this->load->view('home', $data);
        $this->load->view('_parts/footer');
    }

    public function logout()
    {
        $_POST['id'] = $_SESSION['logged_vendor'];
        $_POST['online_status'] = self::VENDOR_STATUS_OFFLINE;
        $_POST['logout_at'] = time();
        $this->Vendorprofile_model->updateVendorLogoutStatus($_POST);
            
        unset($_SESSION['logged_vendor']);
        delete_cookie('logged_vendor');
        redirect(LANG_URL . '/vendor/login');
    }
    
    public function logoff()
    {
        $this->Vendorprofile_model->updateVendorStatus($this->vendor_id, 2);
        unset($_SESSION['logged_vendor']);
        delete_cookie('logged_vendor');
        redirect(LANG_URL . '/vendor/login');
    }
}
