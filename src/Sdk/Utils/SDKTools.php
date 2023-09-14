<?php

declare(strict_types=1);

namespace Deloz\Adapay\Sdk\Utils;

use Deloz\Adapay\Core\AdaPay;

final class SDKTools extends AdaPay
{
    // 创建静态私有的变量保存该类对象
    private static $instance;

    public function __construct()
    {
        parent::__construct();
    }

    private function __clone()
    {
    }

    public function get($params, $endpoint): array
    {
        \ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $endpoint;
        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);

        return $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
    }

    public static function getInstance(): self
    {
        // 判断$instance是否是Singleton的对象，不是则创建
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function isError()
    {
        return $this->isError();
    }

    public function post($params, $endpoint): array
    {
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $endpoint;
        $header = $this->get_request_header($req_url, $request_params, self::$header);

        return $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
    }
}
