<?php

declare(strict_types=1);

namespace Deloz\Adapay\Sdk;

use Deloz\AdaPay\Core\AdaPay;

final class Wallet extends AdaPay
{
    public string $endpoint = '/v1/walletLogin';

    public function __construct()
    {
        $this->gateWayType = 'page';
        parent::__construct();
        // $this->sdk_tools = SDKTools::getInstance();
    }

    /**
     * 钱包登录.
     *
     * @Author   Kelly
     *
     * @DateTime 2020-10-23
     *
     * @version  V1.1.4
     */
    public function login(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
        // $this->result = $this->sdk_tools->post($params, $this->endpoint);
    }
}
