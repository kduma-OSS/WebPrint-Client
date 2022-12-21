<?php

namespace KDuma\WebPrintClient;

use KDuma\WebPrintClient\Response\Dialog;
use KDuma\WebPrintClient\Response\Printer;
use KDuma\WebPrintClient\Response\Promise;
use Psr\Http\Message\StreamInterface;

interface WebPrintApiInterface
{
    /**
     * @param string|null $type_filter
     * @param bool        $with_ppd_options
     *
     * @return array|Printer[]
     */
    public function GetPrinters(string $type_filter = null, bool $with_ppd_options = false): array;

    public function GetPrinter(string $uuid): Printer;

    /**
     * @param int      $page
     * @param int|null $total_pages
     *
     * @return array|Promise[]
     */
    public function GetPromises(int $page = 1, int &$total_pages = null): array;

    public function GetPromise(string $uuid): Promise;

    public function DeletePromise(string $uuid): void;

    public function PrintPromise(string $uuid);

    public function UpdatePromise(string $uuid, ?string $name = null, ?string $printer_uuid = null, ?array $meta = null, ?array $ppd_options = null, ?string $status = null): void;

    public function CreatePromise(string $name, string $type, ?array $meta = null, ?string $printer_uuid = null, ?array $available_printers = null, ?array $ppd_options = null, ?string $content = null, ?string $file_name = null, ?bool $headless = null): Promise;

    public function CreatePromiseAndPrint(string $name, string $type, string $printer_uuid, string $file_name, string $content, ?array $ppd_options = null): Promise;

    public function CreateDialog(string $uuid, bool $auto_print, string $redirect_url, string $restricted_ip = null): Dialog;

    public function GetDialog(string $uuid): Dialog;

    public function GetPromiseContent(string $uuid): StreamInterface;

    public function SetPromiseContent(string $uuid, $content, ?string $file_name = null);
}
