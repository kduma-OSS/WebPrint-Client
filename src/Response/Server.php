<?php


namespace KDuma\WebPrintClient\Response;


class Server
{
    private string $name;
    private string $uuid;

    public function __construct(string $name, string $uuid)
    {
        $this->name = $name;
        $this->uuid = $uuid;
    }

    public function __toString(): string
    {
        return $this->uuid;
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
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
