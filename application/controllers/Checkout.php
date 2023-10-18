<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends MY_Controller
{
    // 待支付
    const PAYSTATUS_PENDING = 10;

    // 支付成功
    const PAYSTATUS_SUCCESS = 20;
    
    // 商户未支付保证金
    const VENDOR_BOND_UNPAY = 0;

    // 商户已支付保证金
    const VENDOR_BOND_PAYED = 1; 
    
    private $orderId;
    private $realShippingAmount;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/Orders_model');
    }

    public function index()
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('checkout');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = str_replace(" ", ",", $head['title']);

	if(!isset($_SESSION['logged_user'])){
	    $this->session->set_flashdata('userErorr', 'you must register user before purchase');
	    redirect(LANG_URL . '/login');
	}
        if (isset($_POST['payment_type'])) {
            $errors = $this->userInfoValidate($_POST);
            if (!empty($errors)) {
                $this->session->set_flashdata('submit_error', $errors);
            } else {
                $_POST['referrer'] = $this->session->userdata('referrer');
                $_POST['clean_referrer'] = cleanReferral($_POST['referrer']);
                $_POST['user_id'] = isset($_SESSION['logged_user']) ? $_SESSION['logged_user'] : 0;
                $orderId = $this->Public_model->setOrder($_POST);
                if ($orderId != false) {
                    /*
                     * Save product orders in vendors profiles
                     */
                    $_POST['parent_order_id'] =  $orderId;
                    $this->setVendorOrders();
                    $this->orderId = $orderId;
                    $this->setActivationLink();
                    $this->sendNotifications();
                    $this->goToDestination();
                } else {
                    log_message('error', 'Cant save order!! ' . implode('::', $_POST));
                    $this->session->set_flashdata('order_error', true);
                    redirect(LANG_URL . '/checkout/order-error');
                }
            }
        }
        $data['bank_account'] = $this->Orders_model->getBankAccountSettings();
        $data['cashondelivery_visibility'] = $this->Home_admin_model->getValueStore('cashondelivery_visibility');
        $data['alipay_visibility'] = $this->Home_admin_model->getValueStore('alipay_visibility');        
        $data['paypal_email'] = $this->Home_admin_model->getValueStore('paypal_email');
        $data['shippingAmount'] = $this->Home_admin_model->getValueStore('shippingAmount');
        $data['bestSellers'] = $this->Public_model->getbestSellers();
        $this->render('checkout', $head, $data);
    }

    private function setVendorOrders()
    {
        $this->Public_model->setVendorOrder($_POST);
    }

    /*
     * Send notifications to users that have nofify=1 in /admin/adminusers
     */

    private function sendNotifications()
    {
        $users = $this->Public_model->getNotifyUsers();
        $myDomain = $this->config->item('base_url');
        if (!empty($users)) {
            $this->sendmail->clearAddresses();
            foreach ($users as $user) {
                $this->sendmail->sendTo($user, 'Admin', 'New order in ' . $myDomain, 'Hello, you have new order. Can check it in /admin/orders');
            }
        }
    }

    private function setActivationLink()
    {
        if ($this->config->item('send_confirm_link') === true) {
            $link = md5($this->orderId . time());
            $result = $this->Public_model->setActivationLink($link, $this->orderId);
            if ($result == true) {
                $url = parse_url(base_url());
                $msg = lang('please_confirm') . base_url('confirm/' . $link);
                $this->sendmail->sendTo($_POST['email'], $_POST['name'], lang('confirm_order_subj') . $url['host'], $msg);
            }
        }
    }

    private function goToDestination()
    {
        if ($_POST['payment_type'] == 'cashOnDelivery' || $_POST['payment_type'] == 'Bank') {
            $this->shoppingcart->clearShoppingCart();
            $this->session->set_flashdata('success_order', true);
        }
        if ($_POST['payment_type'] == 'Bank') {
            $_SESSION['order_id'] = $this->orderId;
            $_SESSION['final_amount'] = $_POST['final_amount'] . $_POST['amount_currency'];
            redirect(LANG_URL . '/checkout/successbank');
        }
        if ($_POST['payment_type'] == 'cashOnDelivery') {
            redirect(LANG_URL . '/checkout/successcash');
        }
        if ($_POST['payment_type'] == 'PayPal') {
            @set_cookie('paypal', $this->orderId, 2678400);
            $_SESSION['discountAmount'] = $_POST['discountAmount'];
            redirect(LANG_URL . '/checkout/paypalpayment');
        }
        if ($_POST['payment_type'] == 'alipay') {
            @set_cookie('alipay', $this->orderId, 2678400);
            $_SESSION['discountAmount'] = $_POST['discountAmount'];
            $_SESSION['final_amount'] = $_POST['final_amount'];
            $_SESSION['alipay_sandbox'] = $this->Home_admin_model->getValueStore('alipay_sandbox');
            $total_amount = $_POST['final_amount']*1.0;
            $commission = $_POST['final_amount']*($this->Home_admin_model->getValueStore('commissonRate')/100);
            $vendor_share = $total_amount-$commission;
            $total_amount = number_format( $total_amount, 6);
            $vendor_share = number_format( $vendor_share, 6);
            $commission = number_format( $commission, 6);
            $this->realShippingAmount = 0.0;
            if($total_amount < $this->Home_admin_model->getValueStore('shippingOrder')){    
                $this->realShippingAmount = $this->Home_admin_model->getValueStore('shippingAmount');
            }
            $_SESSION['realShippingAmount'] = $this->realShippingAmount;
            $_SESSION['order_desc'] = "购买商品";
            $this->Public_model->updateOrderAmount($this->orderId, $total_amount, $vendor_share, $commission, $this->realShippingAmount);
//            $this->Public_model->updateVendorOrderAmount($total_amount, $vendor_share, $commission, $this->realShippingAmount);            
            redirect(LANG_URL . '/checkout/alipay');          
        }        
    }
    
    private function userInfoValidate($post)
    {
        $errors = array();
        if (mb_strlen(trim($post['name'])) == 0) {
            $errors[] = lang('name_empty');
        }
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang('invalid_email');
        }
        $post['phone'] = preg_replace("/[^0-9]/", '', $post['phone']);
        if (mb_strlen(trim($post['phone'])) == 0) {
            $errors[] = lang('invalid_phone');
        }
        if (mb_strlen(trim($post['address'])) == 0) {
            $errors[] = lang('address_empty');
        }
        if (mb_strlen(trim($post['city'])) == 0) {
            $errors[] = lang('invalid_city');
        }
        return $errors;
    }

    public function orderError()
    {
        if ($this->session->flashdata('order_error')) {
            $data = array();
            $head = array();
            $arrSeo = $this->Public_model->getSeo('checkout');
            $head['title'] = @$arrSeo['title'];
            $head['description'] = @$arrSeo['description'];
            $head['keywords'] = str_replace(" ", ",", $head['title']);
            $this->render('checkout_parts/order_error', $head, $data);
        } else {
            redirect(LANG_URL . '/checkout');
        }
    }

    public function paypalPayment()
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('checkout');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = str_replace(" ", ",", $head['title']);
        $data['paypal_sandbox'] = $this->Home_admin_model->getValueStore('paypal_sandbox');
        $data['paypal_email'] = $this->Home_admin_model->getValueStore('paypal_email');
        $this->render('checkout_parts/paypal_payment', $head, $data);
    }

    public function Alipay()
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('checkout');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = str_replace(" ", ",", $head['title']);
//        $data['paypal_sandbox'] = $this->Home_admin_model->getValueStore('paypal_sandbox');
//        $data['paypal_email'] = $this->Home_admin_model->getValueStore('paypal_email');
        $this->render('checkout_parts/alipay', $head, $data);
    }
    
    public function successPaymentCashOnD()
    {
        if ($this->session->flashdata('success_order')) {
            $data = array();
            $head = array();
            $arrSeo = $this->Public_model->getSeo('checkout');
            $head['title'] = @$arrSeo['title'];
            $head['description'] = @$arrSeo['description'];
            $head['keywords'] = str_replace(" ", ",", $head['title']);
            $this->render('checkout_parts/payment_success_cash', $head, $data);
        } else {
            redirect(LANG_URL . '/checkout');
        }
    }

    public function successPaymentBank()
    {
        if ($this->session->flashdata('success_order')) {
            $data = array();
            $head = array();
            $arrSeo = $this->Public_model->getSeo('checkout');
            $head['title'] = @$arrSeo['title'];
            $head['description'] = @$arrSeo['description'];
            $head['keywords'] = str_replace(" ", ",", $head['title']);
            $data['bank_account'] = $this->Orders_model->getBankAccountSettings();
            $this->render('checkout_parts/payment_success_bank', $head, $data);
        } else {
            redirect(LANG_URL . '/checkout');
        }
    }

    public function paypal_cancel()
    {
        if (get_cookie('paypal') == null) {
            redirect(base_url());
        }
        @delete_cookie('paypal');
        $orderId = get_cookie('paypal');
        $this->Public_model->changePaypalOrderStatus($orderId, 'canceled');
        $data = array();
        $head = array();
        $head['title'] = '';
        $head['description'] = '';
        $head['keywords'] = '';
        $this->render('checkout_parts/paypal_cancel', $head, $data);
    }

    public function paypal_success()
    {
        if (get_cookie('paypal') == null) {
            redirect(base_url());
        }
        @delete_cookie('paypal');
        $this->shoppingcart->clearShoppingCart();
        $orderId = get_cookie('paypal');
        $this->Public_model->changePaypalOrderStatus($orderId, 'payed');
        $data = array();
        $head = array();
        $head['title'] = '';
        $head['description'] = '';
        $head['keywords'] = '';
        $this->render('checkout_parts/paypal_success', $head, $data);
    }

    public function alipay_return(){
        if (get_cookie('alipay') == null) {
            redirect(base_url());
        }
	@delete_cookie('alipay');
        @delete_cookie('ordertype');
        @delete_cookie('vendorBond');
	$this->shoppingcart->clearShoppingCart();
        $orderId = get_cookie('alipay');
        $result = $this->Public_model->changeAlipayPayStatus($orderId, self::PAYSTATUS_SUCCESS);
        if ($result == true) {            
            if (get_cookie('vendorBond') != null) {
                $this->Public_model->changeAlipayOrderStatus($orderId, 30);
                $this->Public_model->updateBondPayStatus(get_cookie('vendorBond'), self::VENDOR_BOND_PAYED);
                redirect(LANG_URL . '/vendor/me');
            }
            else{
                $this->Public_model->manageQuantitiesAndProcurement($orderId);
                redirect(LANG_URL . '/checkout/alipay_success');
            }
        }
        else{
            redirect(LANG_URL . '/checkout/order-error');
        }
    }
    public function alipay_success()
    {
        $data = array();
        $head = array();
        $head['title'] = '';
        $head['description'] = '';
	$head['keywords'] = '';
	$this->render('checkout_parts/alipay_success', $head, $data);
    }    

}
