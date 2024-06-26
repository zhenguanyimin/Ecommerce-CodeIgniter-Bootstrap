<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class VENDOR_Controller extends MX_Controller
{

    protected $allowed_img_types;
    public $vendor_id;
    public $vendor_name;
    public $vendor_url;
    
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
        $this->load->model(array('Orders_model', 'Products_model', 'Vendorprofile_model'));        
        $this->loginCheck();
        $this->setVendorInfo();
        $this->allowed_img_types = $this->config->item('allowed_img_types');
        $numNotHandleDeliveryOrders = $this->Orders_model->newOrdersCheck($this->vendor_id, self::QUERY_ORDER_TYPE_DELIVERY);
        $numNotHandleUnPayOrders = $this->Orders_model->newOrdersCheck($this->vendor_id, self::QUERY_ORDER_TYPE_UNPAY);
        $numNotHandleAfterSalesOrders = $this->Orders_model->newOrdersCheck($this->vendor_id, self::QUERY_ORDER_TYPE_AFTERSALES);
        $vars = array();
        $vars['vendor_name'] = $this->vendor_name;
        $vars['vendor_url'] = $this->vendor_url;        
        $vars['numNotHandleDeliveryOrders'] = $numNotHandleDeliveryOrders; 
        $vars['numNotHandleUnPayOrders'] = $numNotHandleUnPayOrders; 
        $vars['numNotHandleAfterSalesOrders'] = $numNotHandleAfterSalesOrders;         
        $this->load->vars($vars);
        if (isset($_POST['saveVendorDetails'])) {
            $this->saveNewVendorDetails();
        }
    }

    protected function loginCheck()
    {
        if (!isset($_SESSION['logged_vendor']) && get_cookie('logged_vendor') != null) {
            $_SESSION['logged_vendor'] = get_cookie('logged_vendor');
        }
        if(isset($_SESSION['logged_vendor'])){           
            $result_online_status = $this->Vendorprofile_model->getVendorInfoFromEmail($_SESSION['logged_vendor']);
            if($result_online_status['online_status'] == 0){
                log_message("debug", "vendor login out by timeout, unset session logged_vendor");
                unset($_SESSION['logged_vendor']);
                delete_cookie('logged_vendor');                
            }            
        }        
        $authPages = array(
            'vendor/login',
            'vendor/register',
            'vendor/forgotten-password'
        );
        $urlString = uri_string();
        if (preg_match('/[a-zA-Z]{2}/', $urlString)) {
            $urlString = preg_replace('/^[a-zA-Z]{2}\//', '', $urlString);
        }
        if (!isset($_SESSION['logged_vendor']) && !in_array($urlString, $authPages)) {
            redirect(LANG_URL . '/vendor/login');
        } else if (isset($_SESSION['logged_vendor']) && in_array($urlString, $authPages)) {
            redirect(LANG_URL . '/vendor/me');
        }
    }

    protected function setLoginSession($email, $remember_me)
    {
        if ($remember_me == true) {
            set_cookie('logged_vendor', $email, 2678400);
        }
        $_SESSION['logged_vendor'] = $email;
    }

    private function setVendorInfo()
    {
        if (isset($_SESSION['logged_vendor'])) {
            $array = $this->Vendorprofile_model->getVendorInfoFromEmail($_SESSION['logged_vendor']);
            $this->vendor_id = $array['id'];
            $this->vendor_name = $array['name'];
            $this->vendor_url = $array['url'];
        }
    }

    private function saveNewVendorDetails()
    {
        $errors = array();
        if (mb_strlen(trim($_POST['vendor_name'])) == 0) {
            $errors[] = lang('enter_vendor_name');
        }
        if (mb_strlen(trim($_POST['vendor_url'])) == 0) {
            $errors[] = lang('enter_vendor_url');
        }
        if (!$this->Vendorprofile_model->isVendorUrlFree($_POST['vendor_url'])) {
            $errors[] = lang('vendor_url_taken');
        }
        if (empty($errors)) {
            log_message('debug', "saveNewVendorDetails");
            $this->session->set_flashdata('update_vend_details', lang('vendor_details_updated'));
            $this->Vendorprofile_model->saveNewVendorDetails($_POST, $this->vendor_id);
        } else {
            $this->session->set_flashdata('update_vend_err', $errors);
        }
        redirect(LANG_URL . '/vendor/me');
    }

}
