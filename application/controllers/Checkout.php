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
    private $realShippingAmount;
    private $alipay_config;

    protected $proudct_config = [
        'alipay' => [
            'default' => [
                //正式环境
                'app_id' => '2021004106649020',            
                'app_secret_cert' => 'MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQCrMAwj9mzYsRjs1XUkSCIHIY0ElA1hMhZnb+/hfQf3SWlwbzIoD/rgnFKaPWdGuB0OB36PSBOeqEuv/T1GlL/GpDsz/8eeMu5Jw73aKf0lGpEYc4iSyB1p3ts9MF3nMzaNvolzehHNgQ2n2zag37Ao4gTW/Nl+svCSsj5zF0m/oP8LtuliXIySM0/5aBOgOOyQG6NWhV+LwrNz6/6cRI2PQ4u9LsNYHM0gBm8GR6rPaOtBi4MmyPI7ObqJtKITPDJWYW07C98EXJhuEDDFadoq/u0zcCqQ86/sz63c/iBXHs8Z5jRbTz+uTLOCg5BM3e16l49kSIBNlw4YPN+u7DtDAgMBAAECggEBAINHG2hQx/P9C9JDd8vVDVNOpWgHaaNJ1+iG7PyM95jp0VQJ0frrFkc9WhMyV4riElX55VJXwcP/59sUZvNDizX4J/aehiSJhjdHRsaRQLI9h1uq7ecyU2wnHRX1i22L1qAoqBvIVvKzrxc0gtYn9F3FxlRgHyKMcvTsf/uetk2fFTHvSIpRWXlaJFTckZlZIZhooflarRV1dUUxLLEeniGwgcxvBxid6NMXy4eccwAZbkjV0C5iOd0/fNm+zKierFUETVZKSOkPTcnpYGrCQwGfkJCuJgoDE7yFf0I9C71kNOVBQRbL2WF0A5YIao+MPVGo/Sb2aUVN4QcDhvtuVkECgYEA4gQaB11TyyJjUt8hZnfLLcIpLy3sFh1cMisxPD3msGQN9Hd7VH90YR+Sq/BKkla9QtlnAXIPins2SsdDaqqQ2XvjUQGFqpfWA9Y1tdUqgFnipxtC4txtPZuM9CZoEIdK9a4pL6xEblJlADc8ufd3dqtE2GInrptxcC12dpevMpECgYEAweXhOltfjbjG3MM65bqqYuVTehZ2OsqNnGEdsKQXzHCAQdiJ0dl/krgWc8Jmn8STLOxvpNydvCU6lz/72E/oNk86KISWUK/stx9UhiTaFsYgH6qL2KvpPtzc6dHTaRzVqUSBey8ISlk3+3OOzFmfu8JIGG5UxhqiMoGl6aGEEpMCgYEArWcqNSZESKBchdNNQ9l61+OUR287J4hlGNSMlMSFPiW1ky8sPxr+Rhm8JRBZlkbYM/aqEbDZ/YwkjmCs96RfN4zWTWsWi1isyQrK8HPYhNrxivXebkFhypeSICtrQesa9r0lOj83zVCHzw+SFrenPzONwVolSdBWyxMGRVyA/RECgYEAjLSK2tRP5QI/nRg3d1ocJyQPjbsbFNLELMT0zKhndL329NF0Qco5n3jjIiHiYvI9cw4oflRySoQhnnyZ/4ENG8wmghylD+x6NPERXz8C3B/uU8xpK1SlMC8KSMsxRUfdbLX/2CprM7jGvTxAVd574b68nq4B6riNF2Wpxn6k3u0CgYEAp+McEhcrY9B1nYrkRgv4WEaL+Rl3Zmkq2fWjk3+vgZ9HhRZxaeKTqXjAvCkYeij9Mytbj6GlENpml5KrNi/zT4ouVBsAtN0+H+ICj4Pk9cLnjq6L0QfvD8M/BGgbTiipq5NMFOod1lZvvHE8Md1I4K38h4jq4uavt2Xj5ZWGYy4=',
                // 必填-应用公钥证书 路径
                // 设置应用私钥后，即可下载得到以下3个证书
                'app_public_cert_path' => '/home/lighthouse/aplipay_cert_product/appCertPublicKey_2021004106649020.crt',
                // 必填-支付宝公钥证书 路径
                'alipay_public_cert_path' => '/home/lighthouse/aplipay_cert_product/alipayCertPublicKey_RSA2.crt',
                // 必填-支付宝根证书 路径
                'alipay_root_cert_path' => '/home/lighthouse/aplipay_cert_product/alipayRootCert.crt',            
                'return_url' => 'https://买买买.cn/checkout/returnCallback',
                'notify_url' => 'https://159.75.179.165/checkout/notifyCallback',
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
            'enable' => false,
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
                'app_id' => '9021000123610640',
                 //必填-应用私钥 字符串或路径
                 //在 https://open.alipay.com/develop/manage 《应用详情->开发设置->接口加签方式》中设置
                'app_secret_cert' => 'MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQCrMAwj9mzYsRjs1XUkSCIHIY0ElA1hMhZnb+/hfQf3SWlwbzIoD/rgnFKaPWdGuB0OB36PSBOeqEuv/T1GlL/GpDsz/8eeMu5Jw73aKf0lGpEYc4iSyB1p3ts9MF3nMzaNvolzehHNgQ2n2zag37Ao4gTW/Nl+svCSsj5zF0m/oP8LtuliXIySM0/5aBOgOOyQG6NWhV+LwrNz6/6cRI2PQ4u9LsNYHM0gBm8GR6rPaOtBi4MmyPI7ObqJtKITPDJWYW07C98EXJhuEDDFadoq/u0zcCqQ86/sz63c/iBXHs8Z5jRbTz+uTLOCg5BM3e16l49kSIBNlw4YPN+u7DtDAgMBAAECggEBAINHG2hQx/P9C9JDd8vVDVNOpWgHaaNJ1+iG7PyM95jp0VQJ0frrFkc9WhMyV4riElX55VJXwcP/59sUZvNDizX4J/aehiSJhjdHRsaRQLI9h1uq7ecyU2wnHRX1i22L1qAoqBvIVvKzrxc0gtYn9F3FxlRgHyKMcvTsf/uetk2fFTHvSIpRWXlaJFTckZlZIZhooflarRV1dUUxLLEeniGwgcxvBxid6NMXy4eccwAZbkjV0C5iOd0/fNm+zKierFUETVZKSOkPTcnpYGrCQwGfkJCuJgoDE7yFf0I9C71kNOVBQRbL2WF0A5YIao+MPVGo/Sb2aUVN4QcDhvtuVkECgYEA4gQaB11TyyJjUt8hZnfLLcIpLy3sFh1cMisxPD3msGQN9Hd7VH90YR+Sq/BKkla9QtlnAXIPins2SsdDaqqQ2XvjUQGFqpfWA9Y1tdUqgFnipxtC4txtPZuM9CZoEIdK9a4pL6xEblJlADc8ufd3dqtE2GInrptxcC12dpevMpECgYEAweXhOltfjbjG3MM65bqqYuVTehZ2OsqNnGEdsKQXzHCAQdiJ0dl/krgWc8Jmn8STLOxvpNydvCU6lz/72E/oNk86KISWUK/stx9UhiTaFsYgH6qL2KvpPtzc6dHTaRzVqUSBey8ISlk3+3OOzFmfu8JIGG5UxhqiMoGl6aGEEpMCgYEArWcqNSZESKBchdNNQ9l61+OUR287J4hlGNSMlMSFPiW1ky8sPxr+Rhm8JRBZlkbYM/aqEbDZ/YwkjmCs96RfN4zWTWsWi1isyQrK8HPYhNrxivXebkFhypeSICtrQesa9r0lOj83zVCHzw+SFrenPzONwVolSdBWyxMGRVyA/RECgYEAjLSK2tRP5QI/nRg3d1ocJyQPjbsbFNLELMT0zKhndL329NF0Qco5n3jjIiHiYvI9cw4oflRySoQhnnyZ/4ENG8wmghylD+x6NPERXz8C3B/uU8xpK1SlMC8KSMsxRUfdbLX/2CprM7jGvTxAVd574b68nq4B6riNF2Wpxn6k3u0CgYEAp+McEhcrY9B1nYrkRgv4WEaL+Rl3Zmkq2fWjk3+vgZ9HhRZxaeKTqXjAvCkYeij9Mytbj6GlENpml5KrNi/zT4ouVBsAtN0+H+ICj4Pk9cLnjq6L0QfvD8M/BGgbTiipq5NMFOod1lZvvHE8Md1I4K38h4jq4uavt2Xj5ZWGYy4=',
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
        
	if(!(isset($_SESSION['logged_user']) || isset($_SESSION['logged_vendor']))){
	    $this->session->set_flashdata('userErorr', 'you must login before purchase');  
            if(!isset($_SESSION['logged_user'])){
                redirect(LANG_URL . '/login');                
            }
            else if(!isset($_SESSION['logged_vendor'])){
                redirect(LANG_URL . '/vendor/login');                
            }
	}
        $result_online_status = $this->Public_model->getUserLoginStatus($_SESSION['logged_user']);
        if($result_online_status['online_status'] == 0){
            log_message("debug", "user login out by timeout, unset session logged_user");
            unset($_SESSION['logged_user']);
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
                    $_POST['user_id'] = isset($_SESSION['logged_user']) ? $_SESSION['logged_user'] : 0;                    
                }
                $this->countPayAmount($_POST);
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

    private function countPayAmount($post)
    {
        $_POST['realShippingAmount'] = 0.0;
        if($post['final_amount'] < $this->Home_admin_model->getValueStore('shippingOrder')){    
            $_POST['realShippingAmount'] = $this->Home_admin_model->getValueStore('shippingAmount');
        }
        $_POST['payAmount'] = $_POST['final_amount'] + $_POST['realShippingAmount'];
        if($this->Home_admin_model->getValueStore('alipay_sandbox') != 0){
            $_POST['payAmount'] = 0.01;
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
            if($_POST['order_source'] == 20){
                $this->Public_model->updateOrderAmount($this->orderId, $_POST['final_amount'],  $_POST['vendor_share'],  $_POST['commission'],  $_POST['realShippingAmount']);                   
            }               
            else{
                $total_amount = $_POST['final_amount']*1.0;            
                $commission = $_POST['final_amount']*($this->Home_admin_model->getValueStore('commissonRate')/100);
                $vendor_share = $total_amount-$commission;
                $total_amount = number_format( $total_amount, 6);
                $vendor_share = number_format( $vendor_share, 6);
                $commission = number_format( $commission, 6);
                $this->Public_model->updateOrderAmount($this->orderId, $total_amount, $vendor_share, $commission,  $_POST['realShippingAmount']);                   
            }         
            $this->web();          
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
    
    public function web()
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
            $result = $this->Public_model->getOrderSource($data->out_trade_no);
            if(empty($result)){
                log_message('error', "can not find the order,order id:".$data->out_trade_no);
                return;
            }
              
            if($result['order_source'] == 20){
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
        
    public function handleNotifyCallback($data)
    {
        if($this->notifyDataValidate($data)){
            log_message("debug", "verify notify callback data success");
            $this->shoppingcart->clearShoppingCart();
            log_message("debug", "notify callback clearShoppingCart");
            $result = $this->Public_model->changeAlipayPayStatus($data->out_trade_no, self::PAYSTATUS_SUCCESS, $data->trade_no);
            if ($result == true){            
                log_message("debug", "change alipay pay status success");
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
