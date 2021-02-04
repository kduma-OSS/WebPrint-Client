<?php


namespace KDuma\WebPrintClient\Response;


class Printer
{
    private string  $uuid;
    private ?Server $server;
    private string  $name;
    private bool   $ppd_support;
    private array $raw_languages_supported;
    private ?array $ppd_options;
    private ?array $ppd_options_layout;

    public function __construct(string $uuid, string $name, bool $ppd_support, array $raw_languages_supported, ?Server $server = null, array $ppd_options = null, array $ppd_options_layout = null)
    {
        $this->uuid = $uuid;
        $this->server = $server;
        $this->name = $name;
        $this->ppd_support = $ppd_support;
        $this->raw_languages_supported = $raw_languages_supported;
        $this->ppd_options = $ppd_options;
        $this->ppd_options_layout = $ppd_options_layout;
    }

    public function __toString(): string
    {
        return $this->uuid;
    }


    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return Server|null
     */
    public function getServer(): ?Server
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isPpdSupport(): bool
    {
        return $this->ppd_support;
    }

    /**
     * @return array
     */
    public function getRawLanguagesSupported(): array
    {
        return $this->raw_languages_supported;
    }

    /**
     * @return array|null
     */
    public function getPpdOptions(): ?array
    {
        return $this->ppd_options;
    }

    /**
     * @return array|null
     */
    public function getPpdOptionsLayout(): ?array
    {
        return $this->ppd_options_layout;
    }

    public static function fromResponse($response)
    {
        $body = is_array($response) ? $response : json_decode($response, true);

        if (isset($body['data']))
            $body = $body['data'];

        return new Printer(
            $body['uuid'],
            $body['name'],
            $body['ppd_support'],
            $body['raw_languages_supported'],
            isset($body['server']) ? Server::fromResponse($body['server']) : null,
            $body['ppd_options'] ?? null,
            $body['ppd_options_layout'] ?? null,
        );
    }
}
