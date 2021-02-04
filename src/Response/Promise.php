<?php


namespace KDuma\WebPrintClient\Response;


class Promise
{
    private string             $uuid;
    private string             $status;
    private string             $name;
    private string             $type;
    private ?array             $ppd_options;
    private bool               $content_available;
    private ?string            $file_name;
    private ?int               $size;
    private ?array             $meta;
    private \DateTimeImmutable $created_at;
    private \DateTimeImmutable $updated_at;
    private ?Printer           $selected_printer;
    /**
     * @var array|Printer[]|null
     */
    private ?array $available_printers;

    public function __construct(string $uuid, string $status, string $name, string $type, ?array $ppd_options, bool $content_available, ?string $file_name, ?int $size, ?array $meta, \DateTimeImmutable $created_at, \DateTimeImmutable $updated_at, ?Printer $selected_printer, ?array $available_printers = null)
    {
        $this->uuid = $uuid;
        $this->status = $status;
        $this->name = $name;
        $this->type = $type;
        $this->ppd_options = $ppd_options;
        $this->content_available = $content_available;
        $this->file_name = $file_name;
        $this->size = $size;
        $this->meta = $meta;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->selected_printer = $selected_printer;
        $this->available_printers = $available_printers;
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array|null
     */
    public function getPpdOptions(): ?array
    {
        return $this->ppd_options;
    }

    /**
     * @return bool
     */
    public function isContentAvailable(): bool
    {
        return $this->content_available;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return array|null
     */
    public function getMeta(): ?array
    {
        return $this->meta;
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

    /**
     * @return ?Printer
     */
    public function getSelectedPrinter(): ?Printer
    {
        return $this->selected_printer;
    }

    /**
     * @return Printer[]|array|null
     */
    public function getAvailablePrinters(): ?array
    {
        return $this->available_printers;
    }

}
