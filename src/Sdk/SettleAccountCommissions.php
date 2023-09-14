<?php

declare(strict_types=1);

namespace Deloz\Adapay\Sdk;

use Deloz\AdaPay\Core\AdaPay;

final class SettleAccountCommissions extends AdaPay
{
    public string $endpoint = '/v1/settle_accounts/commissions';

    public function __construct()
    {
        parent::__construct();
    }

    public function create(array $params = []): void
    {
        $request_params = $params;
        $request_params = $this->do_empty_data($request_params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
    }

    public function queryList(array $params = []): void
    {
        \ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint . '/list';
        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
    }
}
