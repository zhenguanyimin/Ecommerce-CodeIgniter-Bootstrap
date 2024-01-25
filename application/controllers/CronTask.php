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
                'app_id' => '2021004106649020',            
                'app_secret_cert' => 'MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQCrMAwj9mzYsRjs1XUkSCIHIY0ElA1hMhZnb+/hfQf3SWlwbzIoD/rgnFKaPWdGuB0OB36PSBOeqEuv/T1GlL/GpDsz/8eeMu5Jw73aKf0lGpEYc4iSyB1p3ts9MF3nMzaNvolzehHNgQ2n2zag37Ao4gTW/Nl+svCSsj5zF0m/oP8LtuliXIySM0/5aBOgOOyQG6NWhV+LwrNz6/6cRI2PQ4u9LsNYHM0gBm8GR6rPaOtBi4MmyPI7ObqJtKITPDJWYW07C98EXJhuEDDFadoq/u0zcCqQ86/sz63c/iBXHs8Z5jRbTz+uTLOCg5BM3e16l49kSIBNlw4YPN+u7DtDAgMBAAECggEBAINHG2hQx/P9C9JDd8vVDVNOpWgHaaNJ1+iG7PyM95jp0VQJ0frrFkc9WhMyV4riElX55VJXwcP/59sUZvNDizX4J/aehiSJhjdHRsaRQLI9h1uq7ecyU2wnHRX1i22L1qAoqBvIVvKzrxc0gtYn9F3FxlRgHyKMcvTsf/uetk2fFTHvSIpRWXlaJFTckZlZIZhooflarRV1dUUxLLEeniGwgcxvBxid6NMXy4eccwAZbkjV0C5iOd0/fNm+zKierFUETVZKSOkPTcnpYGrCQwGfkJCuJgoDE7yFf0I9C71kNOVBQRbL2WF0A5YIao+MPVGo/Sb2aUVN4QcDhvtuVkECgYEA4gQaB11TyyJjUt8hZnfLLcIpLy3sFh1cMisxPD3msGQN9Hd7VH90YR+Sq/BKkla9QtlnAXIPins2SsdDaqqQ2XvjUQGFqpfWA9Y1tdUqgFnipxtC4txtPZuM9CZoEIdK9a4pL6xEblJlADc8ufd3dqtE2GInrptxcC12dpevMpECgYEAweXhOltfjbjG3MM65bqqYuVTehZ2OsqNnGEdsKQXzHCAQdiJ0dl/krgWc8Jmn8STLOxvpNydvCU6lz/72E/oNk86KISWUK/stx9UhiTaFsYgH6qL2KvpPtzc6dHTaRzVqUSBey8ISlk3+3OOzFmfu8JIGG5UxhqiMoGl6aGEEpMCgYEArWcqNSZESKBchdNNQ9l61+OUR287J4hlGNSMlMSFPiW1ky8sPxr+Rhm8JRBZlkbYM/aqEbDZ/YwkjmCs96RfN4zWTWsWi1isyQrK8HPYhNrxivXebkFhypeSICtrQesa9r0lOj83zVCHzw+SFrenPzONwVolSdBWyxMGRVyA/RECgYEAjLSK2tRP5QI/nRg3d1ocJyQPjbsbFNLELMT0zKhndL329NF0Qco5n3jjIiHiYvI9cw4oflRySoQhnnyZ/4ENG8wmghylD+x6NPERXz8C3B/uU8xpK1SlMC8KSMsxRUfdbLX/2CprM7jGvTxAVd574b68nq4B6riNF2Wpxn6k3u0CgYEAp+McEhcrY9B1nYrkRgv4WEaL+Rl3Zmkq2fWjk3+vgZ9HhRZxaeKTqXjAvCkYeij9Mytbj6GlENpml5KrNi/zT4ouVBsAtN0+H+ICj4Pk9cLnjq6L0QfvD8M/BGgbTiipq5NMFOod1lZvvHE8Md1I4K38h4jq4uavt2Xj5ZWGYy4=',
                // 必填-应用公钥证书 路径
                // 设置应用私钥后，即可下载得到以下3个证书
                'app_public_cert_path' => '/home/lighthouse/aplipay_cert_product/appCertPublicKey_2021004106649020.crt',
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
                $this->Public_model->updateOrderReceiptStatus($order);                
            }
        }
    }    
}

