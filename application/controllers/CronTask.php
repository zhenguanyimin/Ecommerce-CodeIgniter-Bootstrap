<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use Yansongda\Pay\Pay;
use Yansongda\Pay\Contract\HttpClientInterface;

class CronTask extends MY_Controller
{
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
        $this->alipay_config = $this->proudct_config;
        if($this->Home_admin_model->getValueStore('alipay_sandbox') != 0)
        {
            $this->alipay_config = $this->sandbox_config;
        }        
    }
    
    /*
     * 运行定时任务，比如检查买家、卖家是否离线
     * 
     */
    public function runCronTask()
    {
        log_message("debug", "runCronTask");
        $this->handleUserOffline();
        $this->handleVendorOffline();
        $this->handleAutoReceiptProduct();        
        $this->handleVendorTransfer();
        $this->handleParentOrderStatus();
    }
    
    public function handleUserOffline()
    {
        log_message("debug", "handleUserOffline");
        $this->Public_model->handleUserOffline();
    }

    public function handleVendorOffline()
    {
        log_message("debug", "handleVendorOffline");
        $this->Public_model->handleVendorOffline();
    }

    public function excuteVendorTransfer($transferOrders)
    {
        if(empty($transferOrders)){
            log_message("debug", "transferOrders empty");
            return;
        }

        foreach ($transferOrders as $order){
            $vendor_payment_log = $this->Public_model->getSuccVendorPaymentLog($order['order_id']);
            if(!empty($vendor_payment_log)){
                log_message('error', "order has beed transferred, skipped, order_id:".$order['order_id']);
                continue;       
            }     
            $transferAmount = $order['vendor_share']+$order['shipping_amount'];
            log_message("debug", "transfer to vendor:".$order['name'].", vendor_id:".$order['vendor_id'].", order_id:".$order['order_id'].", real name:".$order['vendor_real_name'].", alipay account:".$order['vendor_alipay_account'].", amount:".$transferAmount);
            $balances = $this->Public_model->getVendorsBalances($order['vendor_id']);
            if(empty($balances)){
                log_message('error', "can not find the vendor balances, vendor_id:".$order['vendor_id']);
                continue;            
            }
            if($balances['total_amount'] < $transferAmount || $balances['balances'] < $transferAmount){
                log_message('error', "transfer_amount out of range, total_amount:".$balances['total_amount'].", balances:".$balances['balances'].", transfer_amount:".$transferAmount);
                continue;                 
            }
            $order['transfer_amount'] = number_format($transferAmount-0.005, 2); 
            log_message("debug", "transfer real amount:".$order['transfer_amount']);
            $result = Pay::alipay()->transfer([
                'out_biz_no' => $order['order_id'],
                'trans_amount' => $order['transfer_amount'],
                'product_code' => 'TRANS_ACCOUNT_NO_PWD',
                'biz_scene' => 'DIRECT_TRANSFER',
                'payee_info' => [
                    'identity' => $order['vendor_alipay_account'],
                    'identity_type' => 'ALIPAY_LOGON_ID',
                    'name' => $order['vendor_real_name']
                ],
            ]); 
            log_message("debug", "transfer result:".$result);        
            $response = json_decode($result, true);
            if($response['code'] == 10000){
                log_message("debug", "transfer success");
                $this->Public_model->updateVendorOrderStatus($order, 30);
            }
            else{
                log_message("debug", "transfer fail, code:".$response['code'].", sub_code:".$response['sub_code'].", sub_msg:".$response['sub_msg']);
                $response['status'] = "FAIL";
            }
            $this->Public_model->updateVendorPaymentLog($order, $response);            
        }        
    }
    
    public function handleVendorTransfer()
    {
        log_message("debug", "handleVendorTransfer");
        Pay::config($this->alipay_config);

        log_message("debug", "transfer share to vendor ");        
        $transferOrders = $this->Public_model->getVendorTransferOrders();
        $this->excuteVendorTransfer($transferOrders);

        log_message("debug", "return bond to vendor ");         
        $vendorBondOrders = $this->Public_model->getVendorBondOrders();
        $this->excuteVendorTransfer($vendorBondOrders);        
    }

    public function handleParentOrderStatus()
    {
        log_message("debug", "handleParentOrderStatus");
        $this->Public_model->handleParentOrderStatus();
    }

    public function handleAutoReceiptProduct()
    {
        log_message("debug", "handleAutoReceiptProduct");
        $orders = $this->Public_model->getAutoReceiptOrders();
        if(empty($orders)){
            log_message("debug", "no auto receipt order");
            return;
        }        
        foreach ($orders as $order){
            if(time() - $order['delivery_time'] > AUTO_RECEIPT_PRODUCTS_TIME){
                log_message("debug", "auto receipt product, order_id:".$order['order_id']);                
                $this->Public_model->updateOrderCompleted($order);                
            }
        }
    }    
}

