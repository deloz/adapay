<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: leilei.yang
 * Date: 2021/4/20
 * Time: 13:34.
 */

namespace Deloz\Adapay\Sdk;

use Deloz\AdaPay\Core\AdaPay;
use Exception;

final class AdaPayCommon extends AdaPay
{
    public function array_remove($arr, $key)
    {
        if (!\array_key_exists($key, $arr)) {
            return $arr;
        }

        $keys = \array_keys($arr);
        $index = \array_search($key, $keys, true);

        if (false !== $index) {
            \array_splice($arr, $index, 1);
        }

        return $arr;
    }

    public function format_request_params($request_params): array
    {
        $request_params = $this->array_remove($request_params, 'adapay_func_code');
        $request_params = $this->array_remove($request_params, 'adapay_api_version');

        return $this->do_empty_data($request_params);
    }

    public function packageRequestUrl($requestParams = []): string
    {
        $adapayFuncCode = $requestParams['adapay_func_code'];

        if (empty($adapayFuncCode)) {
            try {
                throw new Exception('adapay_func_code不能为空');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        $adapayApiVersion = $requestParams['adapay_api_version'] ?? 'v1';

        $this->getGateWayUrl($this->gateWayType);

        return $this->gateWayUrl . '/' . $adapayApiVersion . '/' . \str_replace('.', '/', $adapayFuncCode);
    }

    /**
     * 通用查询接口 - GET.
     *
     * @param array  $params
     * @param string $merchantKey 传了则为多商户模式
     */
    public function queryAdapay($params = [], $merchantKey = ''): void
    {
        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }

        \ksort($params);
        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
    }

    public function queryAdapayUits($params = [], $merchantKey = ''): void
    {
        $this->gateWayType = 'page';

        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }
        \ksort($params);
        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        $header = $this->get_request_header($req_url, \http_build_query($request_params), self::$headerText);
        $this->result = $this->ada_request->curl_request($req_url . '?' . \http_build_query($request_params), '', $header, false);
    }

    /**
     * 通用请求接口 - POST - 多商户模式.
     *
     * @param array  $params      请求参数
     * @param string $merchantKey 如果传了则为多商户，否则为单商户
     */
    public function requestAdapay($params = [], $merchantKey = ''): void
    {
        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }

        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
    }

    /**
     * 通用请求接口 - POST - 多商户模式.
     *
     * @param array $params
     * @param mixed $merchantKey
     */
    public function requestAdapayUits($params = [], $merchantKey = ''): void
    {
        $this->gateWayType = 'page';

        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            $this->ada_tools->rsaPrivateKey = $merchantKey;
        }

        $request_params = $params;
        $req_url = $this->packageRequestUrl($request_params);
        $request_params = $this->format_request_params($request_params);

        echo $req_url;

        $header = $this->get_request_header($req_url, $request_params, self::$header);
        $this->result = $this->ada_request->curl_request($req_url, $request_params, $header, $is_json = true);
    }
}
