<?php

namespace App\Services;


class Filemaker
{
    private $domain;
    private $version;
    private $database;
    private $account;
    private $password;
    private $layout;
    private $record_id;
    private $response;

    public function __construct()
    {
        $this->domain = env('APP_FILEMAKER_DOMAIN', 'https://rdp2.citolab.cl');
        $this->version = env('APP_FILEMAKER_VERSION', 'vLatest');
        $this->database = env('APP_FILEMAKER_DATABASE', 'DEV');
        $this->account = env('APP_FILEMAKER_ACCOUNT', 'fullstack');
        $this->password = env('APP_FILEMAKER_PASSWORD', 'laravel');
        $this->layout = env('APP_FILEMAKER_LAYOUT_NAME', 'contacto');
        $this->record_id = env('APP_FILEMAKER_RECORD_ID', '1');
    }

    protected function buildUrlConnect()
    {
        return sprintf('%s/fmi/data/%s/databases/%s/sessions', ltrim($this->domain, '/'), $this->version, $this->database);
    }

    protected function buildUrlSingleRecord()
    {
        return sprintf('%s/fmi/data/%s/databases/%s/layouts/%s/records/%s', ltrim($this->domain, '/'), $this->version, $this->database, $this->layout, $this->record_id);
    }

    /**
     * https://help.claris.com/en/data-api-guide/content/log-in-database-session.html
     */
    public function connect()
    {
       $curl = new \Curl\Curl();
       $curl->setBasicAuthentication($this->account, $this->password);
       $curl->setHeader('Content-Type', 'application/json');

       $curl->post($this->buildUrlConnect());

       if ($curl->error) {
           throw new \Exception($curl->error_code);
       }

       $response = json_decode($curl->response);

       if (!isset($response->messages[0]->code) || $response->messages[0]->code != 0 || !isset($response->response->token)) {
            throw new \Exception('Error inesperado');
       }

       $this->token = $response->response->token;

       return true;
    }

    /**
     * https://help.claris.com/en/data-api-guide/content/get-single-record.html
     */
    public function getSingleRecord()
    {
        if (!$this->token) {
            throw new \Exception('Conexión no valida.');
        }

        $curl = new \Curl\Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->token);

        $curl->get($this->buildUrlSingleRecord());

        if ($curl->error) {
            throw new \Exception($curl->error_code);
        }

        dump($curl->response);
    }
}
