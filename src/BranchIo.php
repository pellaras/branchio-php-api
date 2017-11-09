<?php

namespace BranchIo;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use BranchIo\Exception\BranchIoException;

/**
 * Class BranchIo
 *
 * @author Nikolay Ivlev <nikolay.kotovsky@gmail.com>
 */
class BranchIo
{
    /**
     * Base API url
     */
    const API_URL = 'https://api.branch.io';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * Constructor.
     *
     * @param Config $config
     * @param Client $client
     */
    public function __construct(Config $config = null, Client $client = null)
    {
        $this->config = ($config ?: new Config());
        $this->client = ($client ?: new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
        ]));
    }

    /**
     * Set config.
     *
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get config.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set client.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Make a custom api request.
     *
     * @param string $method  HTTP Method
     * @param string $uri     URI template
     * @param array  $options Array of request options to apply.
     *
     * @throws BranchIoException
     *
     * @return []
     */
    public function request($method, $uri, array $options = [])
    {
        try {
            $response = $this->client->request($method, self::API_URL . $uri, $options);

            return json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response) {
                $headers = $response->getHeaders();

                if (!empty($headers['Content-Type']) && false !== strpos($headers['Content-Type'][0], 'application/json')) {
                    $body = json_decode($response->getBody()->getContents());
                    $errors = ($body->error ? [$body->error->message] : []);

                    if (404 === $response->getStatusCode()) {
                        $errors[] = 'Not Found';
                    }

                    throw new BranchIoException($response->getStatusCode(), $errors, $e->getMessage(), $e->getCode(), $e);
                }
            }

            throw $e;
        }
    }

    /**
     * Create required services on the fly.
     *
     * @param string $name
     *
     * @return object
     */
    public function __get($name)
    {
        if (in_array($name, ['url', 'app', 'credits'], true)) {
            if (isset($this->services[$name])) {
                return $this->services[$name];
            }

            $serviceName = __NAMESPACE__ . '\\' . ucfirst($name);

            $this->services[$name] = new $serviceName($this);

            return $this->services[$name];
        }

        $trace = debug_backtrace();

        $error = 'Undefined property via __get(): %s in %s on line %u';

        trigger_error(sprintf($error, $name, $trace[0]['file'], $trace[0]['line']), E_USER_NOTICE);
    }
}
