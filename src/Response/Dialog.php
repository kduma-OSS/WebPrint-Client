<?php


namespace KDuma\WebPrintClient\Response;


class Dialog
{
    private string             $uuid;
    private string             $status;
    private bool               $auto_print;
    private ?string            $redirect_url;
    private ?string            $restricted_ip;
    private string             $link;
    private \DateTimeImmutable $created_at;
    private \DateTimeImmutable $updated_at;

    public function __construct(string $uuid, string $status, bool $auto_print, ?string $redirect_url, ?string $restricted_ip, string $link, \DateTimeImmutable $created_at, \DateTimeImmutable $updated_at)
    {
        $this->uuid = $uuid;
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
    public function getUuid(): string
    {
        return $this->uuid;
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
}
