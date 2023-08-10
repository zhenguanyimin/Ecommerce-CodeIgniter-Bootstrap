<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use Yansongda\Pay\Pay;
use Yansongda\Pay\Contract\HttpClientInterface;
if (get_cookie('alipay') == null ) {
  redirect(LANG_URL . '/checkout');
}

if (!isset($_SESSION['final_amount'])) {
  $this->session->set_flashdata('amountErorr', '总金额为空');
  redirect(LANG_URL . '/checkout');
}

$total_amount = $_SESSION['final_amount']+$_SESSION['realShippingAmount'];
$orderId = get_cookie('alipay');
$config = [
    'alipay' => [
        'default' => [
            // 必填-支付宝分配的 app_id
            'app_id' => '9021000123610640',
            // 必填-应用私钥 字符串或路径
	    // 在 https://open.alipay.com/develop/manage 《应用详情->开发设置->接口加签方式》中设置
	    'app_secret_cert' => 'MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQCrMAwj9mzYsRjs1XUkSCIHIY0ElA1hMhZnb+/hfQf3SWlwbzIoD/rgnFKaPWdGuB0OB36PSBOeqEuv/T1GlL/GpDsz/8eeMu5Jw73aKf0lGpEYc4iSyB1p3ts9MF3nMzaNvolzehHNgQ2n2zag37Ao4gTW/Nl+svCSsj5zF0m/oP8LtuliXIySM0/5aBOgOOyQG6NWhV+LwrNz6/6cRI2PQ4u9LsNYHM0gBm8GR6rPaOtBi4MmyPI7ObqJtKITPDJWYW07C98EXJhuEDDFadoq/u0zcCqQ86/sz63c/iBXHs8Z5jRbTz+uTLOCg5BM3e16l49kSIBNlw4YPN+u7DtDAgMBAAECggEBAINHG2hQx/P9C9JDd8vVDVNOpWgHaaNJ1+iG7PyM95jp0VQJ0frrFkc9WhMyV4riElX55VJXwcP/59sUZvNDizX4J/aehiSJhjdHRsaRQLI9h1uq7ecyU2wnHRX1i22L1qAoqBvIVvKzrxc0gtYn9F3FxlRgHyKMcvTsf/uetk2fFTHvSIpRWXlaJFTckZlZIZhooflarRV1dUUxLLEeniGwgcxvBxid6NMXy4eccwAZbkjV0C5iOd0/fNm+zKierFUETVZKSOkPTcnpYGrCQwGfkJCuJgoDE7yFf0I9C71kNOVBQRbL2WF0A5YIao+MPVGo/Sb2aUVN4QcDhvtuVkECgYEA4gQaB11TyyJjUt8hZnfLLcIpLy3sFh1cMisxPD3msGQN9Hd7VH90YR+Sq/BKkla9QtlnAXIPins2SsdDaqqQ2XvjUQGFqpfWA9Y1tdUqgFnipxtC4txtPZuM9CZoEIdK9a4pL6xEblJlADc8ufd3dqtE2GInrptxcC12dpevMpECgYEAweXhOltfjbjG3MM65bqqYuVTehZ2OsqNnGEdsKQXzHCAQdiJ0dl/krgWc8Jmn8STLOxvpNydvCU6lz/72E/oNk86KISWUK/stx9UhiTaFsYgH6qL2KvpPtzc6dHTaRzVqUSBey8ISlk3+3OOzFmfu8JIGG5UxhqiMoGl6aGEEpMCgYEArWcqNSZESKBchdNNQ9l61+OUR287J4hlGNSMlMSFPiW1ky8sPxr+Rhm8JRBZlkbYM/aqEbDZ/YwkjmCs96RfN4zWTWsWi1isyQrK8HPYhNrxivXebkFhypeSICtrQesa9r0lOj83zVCHzw+SFrenPzONwVolSdBWyxMGRVyA/RECgYEAjLSK2tRP5QI/nRg3d1ocJyQPjbsbFNLELMT0zKhndL329NF0Qco5n3jjIiHiYvI9cw4oflRySoQhnnyZ/4ENG8wmghylD+x6NPERXz8C3B/uU8xpK1SlMC8KSMsxRUfdbLX/2CprM7jGvTxAVd574b68nq4B6riNF2Wpxn6k3u0CgYEAp+McEhcrY9B1nYrkRgv4WEaL+Rl3Zmkq2fWjk3+vgZ9HhRZxaeKTqXjAvCkYeij9Mytbj6GlENpml5KrNi/zT4ouVBsAtN0+H+ICj4Pk9cLnjq6L0QfvD8M/BGgbTiipq5NMFOod1lZvvHE8Md1I4K38h4jq4uavt2Xj5ZWGYy4=',
            // 必填-应用公钥证书 路径
            // 设置应用私钥后，即可下载得到以下3个证书
            'app_public_cert_path' => '/home/lighthouse/alipay_cert/appPublicCert.crt',
            // 必填-支付宝公钥证书 路径
            'alipay_public_cert_path' => '/home/lighthouse/alipay_cert/alipayPublicCert.crt',
            // 必填-支付宝根证书 路径
            'alipay_root_cert_path' => '/home/lighthouse/alipay_cert/alipayRootCert.crt',
            'return_url' => 'http://159.75.179.165/checkout/alipay_return',
            'notify_url' => 'http://159.75.179.165/checkout/successbank',
            // 选填-第三方应用授权token
            'app_auth_token' => '',
            // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => '',
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // 必填-商户号，服务商模式下为服务商商户号
            // 可在 https://pay.weixin.qq.com/ 账户中心->商户信息 查看
            'mch_id' => '',
            // 选填-v2商户私钥
            'mch_secret_key_v2' => '',
            // 必填-v3 商户秘钥
            // 即 API v3 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置
            'mch_secret_key' => '',
            // 必填-商户私钥 字符串或路径
            // 即 API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_key.pem
            'mch_secret_cert' => '',
            // 必填-商户公钥证书路径
            // 即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_cert.pem
            'mch_public_cert_path' => '',
            // 必填-微信回调url
            // 不能有参数，如?号，空格等，否则会无法正确回调
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // 选填-公众号 的 app_id
            // 可在 mp.weixin.qq.com 设置与开发->基本配置->开发者ID(AppID) 查看
            'mp_app_id' => '2016082000291234',
            // 选填-小程序 的 app_id
            'mini_app_id' => '',
            // 选填-app 的 app_id
            'app_id' => '',
            // 选填-合单 app_id
            'combine_app_id' => '',
            // 选填-合单商户号
            'combine_mch_id' => '',
            // 选填-服务商模式下，子公众号 的 app_id
            'sub_mp_app_id' => '',
            // 选填-服务商模式下，子 app 的 app_id
            'sub_app_id' => '',
            // 选填-服务商模式下，子小程序 的 app_id
            'sub_mini_app_id' => '',
            // 选填-服务商模式下，子商户id
            'sub_mch_id' => '',
            // 选填-微信平台公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
            'mode' => Pay::MODE_NORMAL,
        ]
    ],
    'unipay' => [
        'default' => [
            // 必填-商户号
            'mch_id' => '777290058167151',
            // 必填-商户公私钥
            'mch_cert_path' => __DIR__.'/Cert/unipayAppCert.pfx',
            // 必填-商户公私钥密码
            'mch_cert_password' => '000000',
            // 必填-银联公钥证书路径
            'unipay_public_cert_path' => __DIR__.'/Cert/unipayCertPublicKey.cer',
            // 必填
            'return_url' => 'https://yansongda.cn/unipay/return',
            // 必填
            'notify_url' => 'https://yansongda.cn/unipay/notify',
        ],
    ],
    'logger' => [
        'enable' => false,
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
Pay::config($config);

// 注意返回类型为 Response，具体见详细文档
$response = Pay::alipay()->web([
    'out_trade_no' => $orderId,
//    'total_amount' => '0.01',
    'total_amount' => $total_amount,
    'subject' => '购买书籍',
]);
$content = $response->getBody()->getContents();
echo "$content";
//return $response;
?>

