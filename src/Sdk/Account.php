<?php

declare(strict_types=1);

namespace Deloz\Adapay\Sdk;

use Deloz\Adapay\Core\AdaPay;

final class Account extends AdaPay
{
    public string $endpoint = '/v1/account';
    private static $instance;

    public function __construct()
    {
        $this->gateWayType = 'page';
        parent::__construct();
        // $this->sdk_tools = SDKTools::getInstance();
    }

    // =============账户对象

    /**
     * 创建钱包支付对象
     *
     * @Author   Kelly
     *
     * @DateTime 2020-10-23
     *
     * @param mixed $params
     * @param array
     *
     * @return array
     *
     * @version  V1.1.4
     */
    public function payment($params = [])
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint . '/payment';
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
    }
}
