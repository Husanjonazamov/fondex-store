<?php

namespace App\Http\Services\Sms;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SendService
{
    private const GET = 'GET';
    private const POST = 'POST';
    private const PATCH = 'PATCH';

    private $api_url;
    private $email;
    private $password;
    private $callback_url;
    private $headers;
    private $methods;

    public function __construct($api_url = null, $email = null, $password = null, $callback_url = null)
    {
        $this->api_url = $api_url ?? config("sms.api_url");
        $this->email = $email ?? config("sms.login");
        $this->password = $password ?? config("sms.password");
        $this->callback_url = $callback_url;
        $this->headers = [];

        $this->methods = [
            "auth_login"   => "auth/login",
            "auth_refresh" => "auth/refresh",
            "send_message" => "message/sms/send",
        ];
    }

    function request($api_path, $data = null, $method, $headers = null)
    {
        $incoming_data = ["status" => "error"];
        $req_data = [
            "form_params" => $data,
            "headers"     => $headers,
        ];

        try {
            $clientConfig = ['base_uri' => $this->api_url, 'timeout' => 10, 'verify' => false];
            if ($proxy = config('sms.proxy')) {
                $clientConfig['proxy'] = $proxy;
            }
            $client   = new Client($clientConfig);
            $response = $client->request($method, $api_path, $req_data);

            if ($api_path == $this->methods['auth_refresh']) {
                if ($response->getStatusCode() == 200) {
                    $incoming_data["status"] = "success";
                }
            } else {
                $incoming_data = json_decode($response->getBody()->getContents(), true);
            }
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }

        return $incoming_data;
    }

    function auth()
    {
        $data = [
            "email"    => $this->email,
            "password" => $this->password
        ];

        return $this->request($this->methods["auth_login"], $data, self::POST);
    }

    function sendSms($phone_number, $message)
    {
        $token = $this->auth()['data']['token'];
        $this->headers["Authorization"] = "Bearer " . $token;

        $data = [
            "from"         => config('sms.sender_name', '4546'),
            "mobile_phone" => ltrim($phone_number, '+'),
            "callback_url" => $this->callback_url,
            "message"      => $message,
        ];

        $result = $this->request(
            $this->methods["send_message"],
            $data,
            self::POST,
            $this->headers
        );

        if (isset($result['status']) && $result['status'] === 'error') {
            throw new Exception($result['message'] ?? 'SMS yuborishda xatolik');
        }

        return $result;
    }
}
