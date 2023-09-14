<?php

declare(strict_types=1);

namespace Deloz\Adapay\Sdk;

use Deloz\Adapay\Core\AdaPay;

final class Payment extends AdaPay
{
    public string $endpoint = '/v1/payments';

    public function __construct()
    {
        parent::__construct();
        //  $this->sdk_tools = SDKTools::getInstance();
    }

    /**
     * 关闭支付对象
     *
     * @Author   Kelly
     *
     * @DateTime 2020-10-22
     *
     * @version  V1.1.4
     */
    public function close(array $params = []): void
    {
        $id = $params['payment_id'] ?? '';
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint . '/' . $id . '/close';
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
        // $this->result = $this->sdk_tools->post($params, $this->endpoint."/". $id. "/close");
    }

    // =============支付对象

    /**
     * 创建支付对象
     *
     * @Author   Kelly
     *
     * @DateTime 2020-10-22
     *
     * @version  V1.1.4
     */
    public function create(array $params = []): void
    {
        $params['currency'] = 'cny';
        $params['sign_type'] = 'RSA2';
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
        // $this->result = $this->sdk_tools->post($params, $this->endpoint);
    }

    /**
     * 查询支付对象
     *
     * @Author   Kelly
     *
     * @DateTime 2020-10-22
     *
     * @param mixed $params
     * @param array
     *
     * @return  [type]
     *
     * @version  V1.1.4
     */
    public function query(array $params = []): void
    {
        \ksort($params);
        $id = $params['payment_id'] ?? '';
        $request_params = $params;
        $req_url = $this->gateWayUrl . $this->endpoint . '/' . $id;
        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
        // $this->result = $this->sdk_tools->get($params, $this->endpoint."/".$id);
    }

    /**
     * 查询支付对象列表.
     *
     * @Author   Kelly
     *
     * @DateTime 2020-10-22
     *
     * @version  V1.1.4
     */
    public function queryList(array $params = []): void
    {
        \ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . '/list';
        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
        // $this->result = $this->sdk_tools->get($params, $this->endpoint. "/list");
    }
}
