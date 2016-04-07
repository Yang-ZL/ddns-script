<?php

class RPI_DDNS {
    // define loggin_token and packet type
    private $login_token = ""; // here is your login_tokin(id,token)
    private $format      = "json";
    private $domain;
    private $sub_domain;

    public function __construct($domain, $sub_domain) {
        $this->domain     = $domain;
        $this->sub_domain = $sub_domain;
    } 

    /**
    *   Get pbulic IP address
    *
    *   @param none
    *   @return the public IP address if getting success.
    */
    public function getPublicIP() {
        // post content and post api url
        $data = array("type" => "ip");
        $url  = "https://api.hooowl.com/getIP.php";
        // post action and then decode json packet to object
        $response = $this->post($data, $url);
        $response = json_decode($response);
        // detect the response code 
        $result[] = $response->code;
        $result[] = $response->value;

        return $result;
    }

    /**
    *   Get domain id
    *
    *   @param none
    *   @return get domain id by posting essential parameters to dnspod api.
    */
    public function getDomainID() {
        // define data array for post
        $data = array(
            "login_token" => $this->login_token, 
            "format"      => $this->format
        );
        $url = 'https://dnsapi.cn/Domain.List';

        $response = $this->post($data, $url);
        $response = json_decode($response);
        // var_dump($response);
        if ($response->status->code != 1) {
            $result[] = false;
            $result[] = $response->status->code;
            $result[] = $response->status->message;
            return $result;
        }
        // search ID in the domains part of domain list
        foreach ($response->domains as $key) {
            if ($key->name == $this->domain) {
                $result[] = true;
                $result[] = $response->status->code;
                $result[] = $key->id;
                return $result;
            }
        }
        $result[] = false;
        $result[] = -1;
        $result[] = "Your specific <b>domain</b> is not found. Please check it again!";
        return $result; 
    }

    /**
    *   Get the specific record id or ip address
    *
    *   @param string $domain_id
    *   @param string $type - get ip or record id
    *   @return the public IP address if getting success.
    */
    public function getOneRecord($domain_id, $type='id') {
        // define data array for post
        $data = array(
            'login_token' => $this->login_token, 
            'format'      => $this->format,
            'domain_id'   => $domain_id
        );
        $url = "https://dnsapi.cn/Record.List";
        // var_dump($data);
        $response = $this->post($data, $url);
        $response = json_decode($response);
        // var_dump($response); 
        if ($response->status->code != 1) {
            $result[] = false;
            $result[] = $response->status->code;
            $result[] = $response->status->message;
            return $result;
        }
        foreach ($response->records as $key) {
            if ($key->name == $this->sub_domain) {
                // detect the request type
                $result[] = true;
                $result[] = $response->status->code;
                switch ($type) {
                    case 'ip':
                        $result[] = $key->value;
                        break;
                    case 'id':
                        $result[] = $key->id;
                        break;
                    default:
                        $result[] = "Please select a type to fetch record id or ip.";
                        break;
                }
                return $result;
            }
        }

        $result[] = false;
        $result[] = -1;
        $result[] = "Your specific <b>sub domain</b> is not found. Please check it again!";
        return $result; 
    }
    /**
    *   Update the specific record ip address (DDNS)
    *
    *   @param string $domain_id
    *   @param string $record_id
    *   @param string $ip - the $ip variable is the ip address which you want to 
    *                       replace the existence of ip in the specific record .
    *   @param the request status.
    */
    public function updateIP($domain_id, $record_id, $ip) {
        $data = array(
            'login_token' => $this->login_token,
            'format'      => $this->format,
            'domain_id'   => $domain_id,
            'record_id'   => $record_id,
            'sub_domain'  => $this->sub_domain,
            'record_line' => "默认",
            'value' => $ip
        );
        $url = 'https://dnsapi.cn/Record.Ddns';

        $response = $this->post($data, $url);
        $response = json_decode($response);
        // var_dump($response);
        if ($response->status->code != 1) {
            $result[] = false;
            $result[] = $response->status->code;
            $result[] = $response->status->message;
            return $result;
        }

        $result[] = true;
        $result[] = ' ';
        $result[] = ' ';
        return $result;
    }

    /**
    *   POST function
    *
    *   @param $data - the content which you want to post
    *   @param $url - the api url
    *   @return the transfer from the remote api server.
    */
    private function post($data, $url) {

        // $header = array('User_Agent' => "RPI Client/1.0.0 (zlyang65@gmail.com)");

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $url);
        // curl_setopt($handler, CURLOPT_HEADER, $header);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handler, CURLOPT_TIMEOUT, 60);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($handler, CURLOPT_POST, 1);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($handler);

        return $data;
    }

    public static function send_ms($message) {
        echo $message;
    }
}