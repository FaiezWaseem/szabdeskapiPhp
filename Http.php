<?php

class HTTP
{

    protected $authorization = null;

    protected $cookie = null;

    public function setCookie(string $_ck)
    {
        $this->cookie = $_ck;
    }
    public function setAuthorization(string $_auth)
    {
        $this->authorization = $_auth;
    }
    public function get($url = null)
    {
        if ($this->authorization || $this->cookie) {
            $_auth = $this->authorization;
            $ck = $this->cookie;
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Accept: */*",
                    "User-Agent: Thunder Client (https://www.thunderclient.com)",
                    "authorization: $_auth",
                    "cookie: $ck",
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                return $err;
            }
            curl_close($curl);
            return $response;
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            return $output;
        }
    }
    public function post($url = null)
    {
        $_auth = $this->authorization;
        $ck = $this->cookie;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "Accept: */*",
                "User-Agent: Thunder Client (https://www.thunderclient.com)",
                "authorization: $_auth",
                "cookie: $ck",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            return $err;
        }
        curl_close($curl);
        return $response;

    }
    function postWithParams($url, $params) {
        $ch = curl_init($url);
        
        $encodedParams = http_build_query($params);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        

        curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        
        $response = curl_exec($ch);
        
        if(curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("POST request failed: " . $error);
        }
        
        curl_close($ch);
        
        return $response;
    }

}
