<?php


namespace KDuma\WebPrintClient\Response;


class Server
{
    private string $name;
    private string $ulid;

    public function __construct(string $name, string $ulid)
    {
        $this->name = $name;
        $this->ulid = $ulid;
    }

    public function __toString(): string
    {
        return $this->ulid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getulid(): string
    {
        return $this->ulid;
    }

    public static function fromResponse($response)
    {
        $body = is_array($response) ? $response : json_decode($response, true);

        if (isset($body['data']))
            $body = $body['data'];

        return new Server(
            $body['name'],
            $body['ulid'],
        );
    }
}
