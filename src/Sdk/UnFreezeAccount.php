<?php

declare(strict_types=1);

namespace Deloz\Adapay\Sdk;

use Deloz\AdaPay\Core\AdaPay;

final class UnFreezeAccount extends AdaPay
{
    public $endpoint = '/v1/settle_accounts/unfreeze';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 创建账户解冻对象
     *
     * @param mixed $params
     */
    public function create($params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
    }

    /**
     * 查询账户解冻对象
     *
     * @param mixed $params
     */
    public function queryList($params = []): void
    {
        \ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . '/list';
        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
    }
}
