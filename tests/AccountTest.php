<?php

declare(strict_types=1);

use Deloz\Adapay\Core\AdaPay;
use Deloz\Adapay\Sdk\Account;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    // public function testOne()
    // {
    //     $this->assertTrue(false);
    // }

    public function testPayment(): void
    {
        // 查询账户余额
        $adaPay = new AdaPay();
        $adaPay->gateWayType = 'page';
        $obj = new Account();
        $account_params = [
            // 商户的应用 id
            'app_id' => 'app_7d87c043-aae3-4357-9b2c-269349a980d6',
            // 用户ID
            'order_no' => 'WL_' . date('YmdHis') . mt_rand(100000, 999999),
            // 订单总金额（必须大于0）
            'pay_amt' => '0.10',
            // 3 位 ISO 货币代码，小写字母
            'currency' => 'cny',
            // 商品标题
            'goods_title' => '12314',
            // 商品描述信息
            'goods_desc' => '123122123',
        ];
        $obj->payment($account_params);
        // var_dump($account->result);
        echo $obj->isError() . '=>' . json_encode($obj->result);
        self::assertEquals('succeeded', $obj->result['status']);
        // $this->assertTrue($account->isError());
    }
}
