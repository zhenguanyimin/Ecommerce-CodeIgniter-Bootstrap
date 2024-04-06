<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use Yansongda\Pay\Pay;
use Yansongda\Pay\Contract\HttpClientInterface;

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
    private $alipay_config;

    protected $proudct_config = [
        'alipay' => [
            'default' => [
                //正式环境
                'app_id' => '2021004133678034',            
                'app_secret_cert' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCOaLn8cBgNRp5U6QmnjrAoh0S79Z2hXZJMEp2ITipVrq5Nc88OoQkFqZIMIbK8LRfaJBmjusTb7RUjakvZogNOSp7icYFzgZsaJXJIR087DDBuJ/9LeCz9InDy666in15IKL0T52ob4H4q8wuAdFT1a3CFxC31+0mjiI58qYdTyCCVQO19Ikj1lHVrWrX+4Ei4eLj+JY6hfglo0p0bhLZob2bmx4khmUbtz/K+sXsjzvWrKkPHNFfAUVtJtVNmtbzA4lx70kO85n3hTQ/+ypmwWb+NIDDkGlTQEZi1/YcbYfY2hgnuYCIHtjKk1vLOrvc9dWO8YAnNnZ3v+S+LQds9AgMBAAECggEANXYVVDJUpS88o0205SVI9n4JjLJhUcHJ+SsH3rLRa8cfAk6WjbxgobKN9GX2LnSTPr5Mrc2gt5tiUyBFh1ct/IdA7GuGGHGwY3lpVL8IfHdP+xqi2Zcs9H6oflmB/uTrRRWeHcnDaOG3G7KQP9HsTxHddSo3wt9qPq02KJCQECBCEG4T0zVfFAd6Apf0c+wCKl5yS1+oqdFfHYTypv9gBrGQoA8H6tURpy3dDWUZB9tjWOMtjJlZBcrZL2/z4w7I6vDU1gxF1fr8xNjcZH9ifwOeZJdtMfxusv1wCE0j5AqYIh2MwP8r1MerAzam94+oyaWXpgZiO3Bu6n0wMxCudQKBgQDT3NxvWLilWPHjsqNZAwmbScQksduKQQQSs9Jsoc8mJHFNlkbdq9K5Kowk8IX7tKmCFQlz/c53Omxgx9GB73BC/ExGBSYKFcabwYS8QZ5yFJpMkUIHSuugmUdG+UAlTKLhjUAeezEI4EbrvtTYvaDPziuZeOH7CXtIJlXFxOOuxwKBgQCsE70XNe7atrxqWO+6Vz5oBo2S1S5OvPUxj2ftggXRoWzpTFgW7+mSnpIUgZiiRXgOedS0ByIfQTX5Htn4tDU2aENjpp5zK5sYI7LhIchzqqBKqVCHVbgBGrSFJE/KyLzPgCpTjxzGxBHkYggdOkOPe9/nQbElC6xwqklJSX7x2wKBgQDIFtQdWPZyOtGgkYsSOvssrPtRCKTmKsc0/p3iOOOSC2LyutXM70UB2fwnuv1fHl3k3Adkg7UmB4hp0u28QK2OpgdHLf+iELTVT2wT/AkvhuO/IoTwrEJjF8AoeKirUXiXerau0vwZfO+eIEmXNWiWOgoVVQRf+bF0D7h8IppB6wKBgHOJ/VjsWah5MiGl/bD8i2aFn+GLSkCMF4ZjZ8DNoOKUpPAw1qTwCcDsv+EM81Nhma7+lpcagwrBWmAfGvQm6+PQNg9e/N0P1l9q+Ny5NkKTunTnIq78G0SCjdsn+nuKNVyODd11Jjk/xVO3jwMw79QTtM8uCKd7Ixmy/Oo8cwQpAoGAPCNFowSEDXdPd8SY/pDpvVjGscGnaS2ecJMCzW7WhLGEL+CzuaKPqk9RhsQyoidbM5+e9vfdkM+JEIYRsVREHpzrp6KcSIZuZVKv0LRe7e1QaxRr7hAIpAbngN2At3pZctiaKqFHwX2mRZePgW5WrVVT9H4XXtbIAdg6onepLSs=',
                // 必填-应用公钥证书 路径
                // 设置应用私钥后，即可下载得到以下3个证书
                'app_public_cert_path' => '/home/lighthouse/aplipay_cert_product/appCertPublicKey_2021004133678034.crt',
                // 必填-支付宝公钥证书 路径
                'alipay_public_cert_path' => '/home/lighthouse/aplipay_cert_product/alipayCertPublicKey_RSA2.crt',
                // 必填-支付宝根证书 路径
                'alipay_root_cert_path' => '/home/lighthouse/aplipay_cert_product/alipayRootCert.crt',            
                'return_url' => 'http://买买买.cn:8080/checkout/returnCallback',
                'notify_url' => 'http://159.75.179.165:8080/checkout/notifyCallback',
                // 选填-第三方应用授权token
                'app_auth_token' => '',
                // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
                'service_provider_id' => '',
                // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
    //            'mode' => Pay::MODE_SANDBOX,
                'mode' => Pay::MODE_NORMAL,            
            ]
        ],  
        'logger' => [ // optional
            'enable' => true,
            'file' => './logs/alipay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
    ];
    
    protected $sandbox_config = [
        'alipay' => [
            'default' => [
                  // 沙箱环境
                // 必填-支付宝分配的 app_id
                'app_id' => '9021000134635811',
                 //必填-应用私钥 字符串或路径
                 //在 https://open.alipay.com/develop/manage 《应用详情->开发设置->接口加签方式》中设置
                'app_secret_cert' => 'MIIEowIBAAKCAQEAnEeW4XWY2gEM6kDMTvpH+HVOV9fWEyg8/+QlLLyY83dVQNs4BDNtdFnvLfjIEh9Sm8cwXqIQnZB4RZq/SRi7+v65jaKtJN8ZedlZXO71a+XBeF2tV2u4m58nd/ZhBPmr6bIp68qeCZohTNjpz6XzZ11s7g8cVAamQF4Z+ZOLenMCcsh17U0z1Wj7HlbAWBgj/FETCDeIz/qM1bWvnnZ7zlFdI6x+OHFCFCK7hEne9CmX5Q+8N4NlElHbVmxc29AR/JHQRCJFlBtOt75e56j7D0DdSruPpbtfBuhP/Qr9CdCp3v2TXe6zLYbJsSTfsnDkEQgEUOmRlPow+T592t6hDQIDAQABAoIBAG6ODmSsplcCizpkYKQ2VhekFKn73EttGcoEgW/mc2U4tCzPaA9AulunC5a/+fkoA26EOOmZSJvOiebjlBKH1uO2s1lJDaeZ1BHo+ljOCvwravRVgLzpTY15x5gLyZKVdVI7YYCWs7ojOQ9+G9lzkn87DkZSlj2y/oVmjIWMJQ2Xl70LeUJnHPeMthAbvgiBJ6159ayDoz3WESjjl1hD1gQRAwi6+p2BP5hfMLRi8vdP9I1LL7IxjpEfsKdxEBddN4xRMqZsNp5DbLlQ+XyRVobNfboR6NJFAi+ys0SaReG6GgTSv/On83xRSMuuNn1yTp7cPmjj5s6DztXFScmAIsECgYEAzOzA2dTqfVV8ZWABC7VyTSbFWyp3TAesxjmPv8Qw3zatPzBNV10Q+KYvlRU+mDyLYF4lHPGuzA7HY34hEGT7ktId70ypYPSN5tXd6sqg66lfYqhVG6N+XNJvspkp9QQkZXp15Bku3EIqdZ57a964LIAfd2yLnYcIV+nCJTc2FVUCgYEAwzsGxM1otod3+n0zffJJg6lwn0I05xDDo0xruxC7aVQueasoch2r0YgyKJbvZ+x3ntq6LgZbPHce+5OuqO3k5wK8axnpiAJ1PLKzU0XBZcprw16Ve4zsgoQLwOB5gbMiOXctpHoOmH0kWooA9d3PmU+2tDNkjEmFakNbQyTsXNkCgYA5flchBnZ/kYNkIcpJUa/u62jFiiWMRD76Il4tTEr15S44I0Ift7GyQVXqOtqj9aCY+fDprPkAsUjJpjJ6mgpnB+J0KAsBc7t4PxqS7CS32X40fMvcBEPIoRXLguNdpbrcab43r8UQ8NNeyocQHZ2Ihq3NYLvPB3qUx7W7oUDgPQKBgQCu1jaqmpXTGSCuV998RDXnzh9I0J7V66J4pKC55zMfEb+JQGm5QK81t3XL+deuxwsdXR5sx05/qOI7RLefG2TqKP4aIBuOrTzWveZwhcC76vp0/Uh7W+oWvDBWa/EE4SqeMgfTC2f00eVzm7FefmVDFCs31+qM4+6yCl45lGCEiQKBgHh1kVbOrFvAnutIYhMtHmPtL4vyjyALhAUrKVqrSexhZPrsWNBPXgRAveNPJB/TzKD99vywOxQ8W4iBV0fUPXR57iugHYSKCelpoP/yDX0IE1xYmOjre4mynkXNUiS/HSBSGFdoVso2kcnZUw63wzQGpi0Q3mrT/b8udFa1VUae',
                // 必填-应用公钥证书 路径
                // 设置应用私钥后，即可下载得到以下3个证书
                'app_public_cert_path' => '/home/lighthouse/alipay_cert/appPublicCert.crt',
                // 必填-支付宝公钥证书 路径
                'alipay_public_cert_path' => '/home/lighthouse/alipay_cert/alipayPublicCert.crt',
                // 必填-支付宝根证书 路径
                'alipay_root_cert_path' => '/home/lighthouse/alipay_cert/alipayRootCert.crt',           
                'return_url' => 'http://买买买.cn:8080/checkout/returnCallback',
                'notify_url' => 'http://159.75.179.165:8080/checkout/notifyCallback',
                // 选填-第三方应用授权token
                'app_auth_token' => '',
                // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
                'service_provider_id' => '',
                // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
                'mode' => Pay::MODE_SANDBOX,        
            ]
        ],
        'logger' => [
            'enable' => true,
            'file' => './logs/pay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
    ];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/Orders_model');
        $visit_history = array();
        $visit_history['remote_addr'] = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'';
        $visit_history['request_uri'] = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']:'';
        $visit_history['remote_location'] = $this->ip_address($visit_history['remote_addr']);
        $visit_history['http_referer'] = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:'';
        $visit_history['user_name'] = '';
        $visit_history['email'] = '';
        $this->Public_model->setVisitHistory($visit_history);
        $this->alipay_config = $this->proudct_config;
        if($this->Home_admin_model->getValueStore('alipay_sandbox') != 0)
        {
            $this->alipay_config = $this->sandbox_config;
        }                    
    }

     /**
     *  调用淘宝API根据IP查询地址
     */
    public function ip_address($ip)
    {
        $url = @file_get_contents("http://whois.pconline.com.cn/ipJson.jsp?ip=$ip&json=true");
        $UTF8_RESP= iconv("GBK", "UTF-8", $url);         
        $res1 = json_decode($UTF8_RESP,true);
        $data =$res1;       
        if ($data) {
            return array_key_exists('addr', $data)? $data['addr']: 'unknown';
        } else {
            return 'unknown';
        }
    }
 
    public function index()
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('checkout');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = str_replace(" ", ",", $head['title']);
        
	if(!isset($_SESSION['logged_user']) && !isset($_SESSION['logged_vendor'])){
	    $this->session->set_flashdata('userErorr', 'you must login before purchase');  
            if(!isset($_SESSION['logged_user'])){
                redirect(LANG_URL . '/login');                
            }
            else if(!isset($_SESSION['logged_vendor'])){
                redirect(LANG_URL . '/vendor/login');                
            }
	}
        if(isset($_SESSION['logged_user'])){
            $result_online_status = $this->Public_model->getUserLoginStatus($_SESSION['logged_user']);
            if($result_online_status['online_status'] == 0){
                log_message("debug", "user login out by timeout, unset session logged_user");
                unset($_SESSION['logged_user']);
            } 
        }            
        if(isset($_SESSION["pay_bond_data"])){
            $_POST = $_SESSION["pay_bond_data"];
            unset($_SESSION["pay_bond_data"]);
        }
        if (isset($_POST['payment_type'])) {
            $errors = $this->userInfoValidate($_POST);
            if (!empty($errors)) {
                $this->session->set_flashdata('submit_error', $errors);
            } else {
                $_POST['referrer'] = $this->session->userdata('referrer');
                $_POST['clean_referrer'] = cleanReferral($_POST['referrer']);
                if(!isset($_POST['user_id'])){
                    if(!isset($_SESSION['logged_user'])){
                        redirect(LANG_URL . '/login');                
                    }                    
                    else {
                        $_POST['user_id'] = $_SESSION['logged_user'];
                    }                    
                    
                }
                $this->countPayAmount();
                $orderId = $this->Public_model->setOrder($_POST);
                if ($orderId != false) {
                    /*
                     * Save product orders in vendors profiles
                     */
                    $_POST['parent_order_id'] =  $orderId;
                    $this->setVendorOrders();
                    $this->orderId = $orderId;
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
     * Send notifications to vendor associated with the order
     */

    private function sendNotificationsToVendor($vendor, $order_id)
    {
        $myDomain = $this->config->item('base_url');
        $this->sendmail->clearAddresses();
        log_message("debug", "send order payed notifications to vendor:".$vendor['name']);
        $this->sendmail->sendTo($vendor['email'], $vendor['name'], '订单已付款通知', '客户已购买您的商品,订单号:'.$order_id.'，请登录商户管理系统及时发货呦'.$myDomain);
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
                log_message("debug", "send order payed notifications to Admin");
                $this->sendmail->sendTo($user, 'Admin', '订单已付款通知', '新增一个已付款订单，请登录平台管理系统查看！'.$myDomain);
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

    private function countPayAmount()
    {
//        if($this->Home_admin_model->getValueStore('alipay_sandbox') != 0){
//            $_POST['payAmount'] = 0.01;
//        }        
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
            $this->webPay();          
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

    public function alipay_fail()
    {
        $data = array();
        $head = array();
        $arrSeo = $this->Public_model->getSeo('checkout');
        $head['title'] = @$arrSeo['title'];
        $head['description'] = @$arrSeo['description'];
        $head['keywords'] = str_replace(" ", ",", $head['title']);
        $this->render('checkout_parts/alipay_fail', $head, $data);        
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

    public function alipay_success()
    {
        $data = array();
        $head = array();
        $head['title'] = '';
        $head['description'] = '';
	$head['keywords'] = '';
	$this->render('checkout_parts/alipay_success', $head, $data);
    }    

    public function bond_success()
    {
        $data = array();
        $head = array();
        $head['title'] = '';
        $head['description'] = '';
	$head['keywords'] = '';
	$this->render('checkout_parts/bond_success', $head, $data);
    }   
    
    public function webPay()
    {
        if($this->Home_admin_model->getValueStore('alipay_sandbox') == 0){
            Pay::config($this->proudct_config);
        }
        else{
            Pay::config($this->sandbox_config);
        }

        // 注意返回类型为 Response，具体见详细文档
        $response = Pay::alipay()->web([
            'out_trade_no' => $this->orderId,
            'total_amount' => $_POST['payAmount'],
            'subject' => "购买商品"
        ]);
        $content = $response->getBody()->getContents();
        echo "$content";              
    }
    
    public function returnDataValidate($data)
    {
        if(empty($data)){
            log_message("debug", "verify return callback sign fail.");
            return false;
        }
        log_message("debug", "verify return callback sign success,"."out_trade_no:".$data->out_trade_no.", trade_no:".$data->trade_no.", total_amount:".$data->total_amount);
        
        //校验支付同步回调关键数据
        $payment_log = $this->Public_model->getUserPaymentLog($data->out_trade_no);
        if(empty($payment_log)){
            log_message("debug", "verify return callback out_trade_no fail"."return out_trade_no:".$data->out_trade_no.", total_amount:".$data->total_amount);
            return false;
        }
        log_message("debug", "verify return callback out_trade_no success");
            
        if($data->total_amount != $payment_log['amount']){
            log_message("debug", "verify return callback amount error, total_amount:[".$data->total_amount.", ".$payment_log['amount']."]");
            return false;
        }
        log_message("debug", "verify return callback amount success"); 
        
        return true;       
    }
     
    public function handleReturnCallback($data)
    {   
        if($this->returnDataValidate($data)){
            log_message("debug", "verify return callback data success");
            $orderInfo = $this->Public_model->getOrderInfo($data->out_trade_no);
            if(empty($orderInfo)){
                log_message('error', "can not find the order,order id:".$data->out_trade_no);
                return;
            }
              
            if($orderInfo['order_source'] == ORDER_SOURCE_BOND){
                redirect(LANG_URL . '/checkout/bond_success');
            }
            else{
                //清空购物车
                $this->shoppingcart->clearShoppingCart();
                log_message("debug", "return callback clearShoppingCart");                
                redirect(LANG_URL . '/checkout/alipay_success');
            }          
        }
        else{
           log_message("debug", "verify return callback data fail"); 
           redirect(LANG_URL . '/checkout/alipay_fail');             
        }
    }
    
    public function returnCallback()
    {            
        if(!isset($_GET['trade_no'])){
            log_message("debug", "return callback data invalid.");
            redirect(LANG_URL . '/checkout/alipay_fail');
        }
        else{
            $data = Pay::alipay($this->alipay_config)->callback(); //verify return callback sign  
            $this->handleReturnCallback($data);
        }
    }

    public function notifyDataValidate($data)
    {
        if(empty($data)){
            log_message("debug", "verify notify callback sign fail.");
            return false;
        }
        log_message("debug", "verify notify callback sign success,"."out_trade_no:".$data->out_trade_no.", trade_no:".$data->trade_no.", total_amount:".$data->total_amount);
        
        //校验支付异步通知关键数据
        $payment_log = $this->Public_model->getUserPaymentLog($data->out_trade_no);
        if(empty($payment_log)){
            log_message("debug", "verify notify callback out_trade_no fail"."return out_trade_no:".$data->out_trade_no.", total_amount:".$data->total_amount);
            return false;
        }
        log_message("debug", "verify notify callback out_trade_no success");
        
        //记录支付宝交易流水
        $this->Public_model->updateUserPaymentLog($data);
        
        if($data->total_amount != $payment_log['amount']){
            log_message("debug", "verify notify callback amount error, total_amount:[".$data->total_amount.", ".$payment_log['amount']."]");
            return false;
        }
        log_message("debug", "verify notify callback amount success"); 

        if($data->app_id != $this->alipay_config['alipay']['default']['app_id']){
            log_message("debug", "verify notify callback app_id error, total_amount:[".$data->app_id.", ".$this->alipay_config['alipay']['default']['app_id']."]");
            return false;
        }
        log_message("debug", "verify notify callback app_id success"); 
        
        if(!("TRADE_SUCCESS" == $data->trade_status || "TRADE_FINISHED" == $data->trade_status)){
            log_message("debug", "alipay notify pay fail");
            return false;            
        }
        log_message("debug", "alipay notify pay success");
        
        return true;       
    }
    
    public function sendNotificationsToVendors($data) {
        $venders_order = $this->Public_model->queryChildOrders($data->out_trade_no);
        if(empty($venders_order)){
            log_message("debug", "notify vendors empty");
            return;
        }        
        foreach($venders_order as $order){
            $vendorInfo = $this->Public_model->getVendorInfo($order['vendor_id']);
            if(empty($vendorInfo)){
                log_message("debug", "vendors info empty, vendor_id:".$order['vendor_id']);
                continue;
            }
            $this->sendNotificationsToVendor($vendorInfo, $order['order_id']);
        }  
    }
    
    public function handleNotifyCallback($data)
    {
        if($this->notifyDataValidate($data)){
            log_message("debug", "verify notify callback data success");
            $this->shoppingcart->clearShoppingCart();
            log_message("debug", "notify callback clearShoppingCart");
            $result = $this->Public_model->changeAlipayPayStatus($data, self::PAYSTATUS_SUCCESS);
            if ($result == true){            
                log_message("debug", "change alipay pay status success");
                //发送邮件通知给商户及管理员
                $this->sendNotificationsToVendors($data);
                $this->sendNotifications();
            }
            else{
                log_message("debug", "change alipay pay status fail");
            }            
        }
        else{
           log_message("debug", "verify notify callback data fail"); 
        }
    }
     
    public function notifyCallback()
    {     
        $alipay = Pay::alipay($this->alipay_config);
        try{
            $data = $alipay->callback(); ////verify notify callback sign  
            $this->handleNotifyCallback($data);
        } catch (\Exception $e) {
        }

        return $alipay->success();
    }    
}
