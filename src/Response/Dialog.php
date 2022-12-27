<?php

namespace KDuma\WebPrintClient\Response;

use DateTimeImmutable;

class Dialog
{
    private string             $ulid;
    private string             $status;
    private bool               $auto_print;
    private ?string            $redirect_url;
    private ?string            $restricted_ip;
    private string             $link;
    private \DateTimeImmutable $created_at;
    private \DateTimeImmutable $updated_at;

    public function __construct(string $ulid, string $status, bool $auto_print, ?string $redirect_url, ?string $restricted_ip, string $link, \DateTimeImmutable $created_at, \DateTimeImmutable $updated_at)
    {
        $this->ulid = $ulid;
        $this->status = $status;
        $this->auto_print = $auto_print;
        $this->redirect_url = $redirect_url;
        $this->restricted_ip = $restricted_ip;
        $this->link = $link;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    /**
     * @return string
     */
    public function getUlid(): string
    {
        return $this->ulid;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isAutoPrint(): bool
    {
        return $this->auto_print;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirect_url;
    }

    /**
     * @return string|null
     */
    public function getRestrictedIp(): ?string
    {
        return $this->restricted_ip;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updated_at;
    }

    public static function fromResponse($response): Dialog
    {
        $body = is_array($response) ? $response : json_decode($response, true);

        if (isset($body['data'])) {
            $body = $body['data'];
        }

        return new Dialog(
            $body['ulid'],
            $body['status'],
            $body['auto_print'],
            $body['redirect_url'],
            $body['restricted_ip'],
            $body['link'],
            new DateTimeImmutable($body['created_at']),
            new DateTimeImmutable($body['updated_at'])
        );
    }
}
