<?php


namespace KDuma\WebPrintClient\HttpClient;


interface HttpClientInterface
{
    public function get(string $path, array $query = [], array $headers = []);

    public function delete(string $path, array $query = [], array $headers = []): void;

    public function post(string $path, array $body, array $query = [], array $headers = []);

    public function put(string $path, array $body, array $query = [], array $headers = []);

    public function rawPost(string $path, $body, array $query = [], array $headers = []);
}
