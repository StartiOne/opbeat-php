<?php namespace Opbeat;

use ErrorException;
use Exception;
use Opbeat\Log\Entry;
use GuzzleHttp\Client as Guzzle;

class Client
{
    const API_HTTP_METHOD     = 'POST';
    const API_HOST            = 'https://opbeat.com/api/v1';
    const API_ERRORS_ENDPOINT = '/organizations/%s/apps/%s/errors/';

    protected $organization_id;
    protected $app_id;
    protected $access_token;

    public function __construct(array $config)
    {
        $this->organization_id  = $config['organization_id'];
        $this->app_id           = $config['app_id'];
        $this->access_token     = $config['access_token'];
        $enableExceptionHandler = isset($config['enable_exception_handler'])
            ? $config['enable_exception_handler']
            : true;
        $enableErrorHandler     = isset($config['enable_error_handler'])
            ? $config['enable_error_handler']
            : true;

        if ($enableExceptionHandler) {
            $this->enableExceptionHandler();
        }

        if ($enableErrorHandler) {
            $this->enableErrorHandler();
        }
    }

    public function catchException(Exception $exception)
    {
        static $client;
        if (!isset($client)) {
            $client = new Guzzle();
        }

        $client->post($this->errorsEndpoint(), [
            'future' => true,
            'headers' => [
                'Authorization' => 'Bearer '.$this->access_token,
            ],
            'json' => Entry::create($exception),
        ]);
    }

    public function handleError($no, $message, $filename = null, $lineNumber = null, $context = null)
    {
        throw new ErrorException($message, 0, $no, $filename, $lineNumber);
    }

    public function enableExceptionHandler()
    {
        set_exception_handler($this->exceptionHandler());
    }

    public function enableErrorHandler()
    {
        set_error_handler($this->errorHandler());
    }

    public function exceptionHandler()
    {
        return [$this, 'catchException'];
    }

    public function errorHandler()
    {
        return [$this, 'handleError'];
    }

    protected function errorsEndpoint()
    {
        static $endpoint;
        if (!isset($endpoint)) {
            $endpoint = self::API_HOST.sprintf(self::API_ERRORS_ENDPOINT, $this->organization_id, $this->app_id);
        }

        return $endpoint;
    }
}
