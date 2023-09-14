<?php

declare(strict_types=1);

namespace Deloz\Adapay\Core;

use Workerman\Mqtt\Client;
use Workerman\Worker;

final class AdaSubscribe extends AdaPay
{
    public $accessKey = '';
    public $callbackFunc = '';
    public $client_address = '';
    public $clientId = '';
    public $groupId = '';
    public $instanceId = '';
    public $mq_token;
    public $password;
    public $token = '';
    public $topic = '';
    public $username;
    public $worker;

    public function __construct()
    {
        parent::__construct();
        $this->_init();
        $this->mq_token = new AdaMqttToken();
    }

    public function mqttCallBack($content, $callback, $topic): void
    {
        $callback($content, $topic);
    }

    public function workerStart($workerMsg, $callback, $apiKey = '', $client_id = ''): void
    {
        $this->worker = new Worker();
        $this->_setting($apiKey, $client_id);
        $topic = $this->topic;
        $this->worker->onWorkerStart = function () use ($topic, $workerMsg, $callback): void {
            $options = [
                'keepalive' => 5,
                'username' => $this->username,
                'password' => $this->_get_password(),
                'client_id' => $this->clientId,
                'clean_session' => false,
                'debug' => self::$isDebug,
            ];

            $client = new Client('mqtt://' . $this->client_address, $options);
            $client->onConnect = static function ($client) use ($topic): void {
                $client->subscribe($topic, ['qos' => 1]);
            };
            $client->onError = function ($exception) use ($options, $client): void {
                $this->worker->stopAll();
                echo 'execute before password:---------------------------';
                \var_dump($options['password']);
                $options['password'] = $this->_get_password(); // 重新获取token
                echo 'execute after password:---------------------------';
                \var_dump($options['password']);
                $client->onConnectionClose(); // 断开重新连接
            };
            $client->onMessage = function ($topic, $content) use ($options, $workerMsg, $callback, $client): void {
                if ('$SYS/tokenExpireNotice' === $topic) {
                    echo 'execute before password:---------------------------';
                    \var_dump($options['password']);
                    $options['password'] = $this->_get_password(); // 重新获取token
                    echo 'execute OnMessage password:---------------------------';
                    \var_dump($options['password']);
                    $client->onConnectionClose(); // 断开重新连接
                } else {
                    \call_user_func([$workerMsg, 'mqttCallBack'], $content, $callback, $topic);
                }
            };
            $client->connect();
        };
        $this->worker->runAll();
    }

    private function _get_password()
    {
        $token = $this->mq_token->getToken();

        return 'R|' . $token;
    }

    private function _init(): void
    {
        $this->accessKey = self::$mqttAccessKey;
        $this->instanceId = self::$mqttInstanceId;
        $this->groupId = self::$mqttGroupId;
        $this->client_address = self::$mqttAddress;
    }

    private function _setting($apiKey, $client_id): void
    {
        $_apiKey = empty($apiKey) ? parent::$api_key : $apiKey;
        $client_id = empty($client_id) ? $_apiKey : $_apiKey . $client_id;
        $this->username = 'Token|' . $this->accessKey . '|' . $this->instanceId;
        $this->clientId = $this->groupId . '@@@' . \md5($client_id);
        $this->topic = 'topic_crhs_sender/' . $_apiKey;
    }
}
