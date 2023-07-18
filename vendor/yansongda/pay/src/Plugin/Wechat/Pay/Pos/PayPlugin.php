<?php

declare(strict_types=1);

namespace Yansongda\Pay\Plugin\Wechat\Pay\Pos;

use Yansongda\Pay\Plugin\Wechat\GeneralV2Plugin;
use Yansongda\Pay\Rocket;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_10&index=1
 */
class PayPlugin extends GeneralV2Plugin
{
    protected function getUri(Rocket $rocket): string
    {
        return 'pay/micropay';
    }
}
