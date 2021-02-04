<?php


namespace KDuma\WebPrintClient\HttpClient;


use GuzzleHttp\Client;

class GuzzleHttp7Client implements HttpClientInterface
{
    protected Client $client;

    public function __construct(string $endpoint, string $key)
    {
        $this->client = new Client([
            'base_uri' => sprintf("%s/", rtrim($endpoint, '/')),
            'timeout'  => 15,
            'headers'  => [
                'User-Agent'    => 'web-print-api-php-client/1.0',
                'Accept'        => 'application/json',
                'Authorization' => sprintf("Bearer %s", $key),
            ],
        ]);
    }

    public function get(string $path, array $query = [], array $headers = [])
    {
        return $this->request('GET', $path, $query, $headers);
    }

    public function delete(string $path, array $query = [], array $headers = []): void
    {
        $this->request('DELETE', $path, $query, $headers);
    }

    public function post(string $path, array $body, array $query = [], array $headers = [])
    {
        return $this->request('POST', $path, $query, $headers, $body);
    }

    public function put(string $path, array $body, array $query = [], array $headers = [])
    {
        return $this->request('PUT', $path, $query, $headers, $body);
    }

    public function rawPost(string $path, $body, array $query = [], array $headers = [])
    {
        return $this->request('PUT', $path, $query, $headers, null, $body);
    }


    protected function request(string $method, string $path, array $query = [], array $headers = [], ?array $body = null, $raw_body = null)
    {
        return $this->client->request($method, $path, [
            'query' => $query,
            'headers' => $headers,
        ] + ($raw_body !== null ? ['body' => $raw_body] : ['json' => $body]))->getBody();
    }
}
