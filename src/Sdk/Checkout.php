<?php

declare(strict_types=1);

namespace Deloz\Adapay\Sdk;

use Deloz\AdaPay\Core\AdaPay;

final class Checkout extends AdaPay
{
    public string $endpoint = '/v1/checkout';
    private static $instance;

    public function __construct()
    {
        $this->gateWayType = 'page';
        parent::__construct();
        // $this->sdk_tools = SDKTools::getInstance();
    }

    // =============收银台对象

    /**
     * 创建收银台对象
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
    public function create($params = [])
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
        // $this->result = $this->sdk_tools->post($params, $this->endpoint);
    }

    /**
     * 查询收银台对象列表.
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
    public function queryList($params = [])
    {
        \ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . '/list';
        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
    }
}
