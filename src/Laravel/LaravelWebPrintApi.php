<?php

namespace KDuma\WebPrintClient\Laravel;

use KDuma\WebPrintClient\Response\Printer;
use KDuma\WebPrintClient\Response\Promise;
use KDuma\WebPrintClient\WebPrintApi;
use KDuma\WebPrintClient\WebPrintApiInterface;

class LaravelWebPrintApi extends WebPrintApi implements WebPrintApiInterface
{
    public function GetPrinter(string $uuid): Printer
    {
        return parent::GetPrinter($uuid);
    }

    public function UpdatePromise(
        string  $uuid,
        ?string $name = null,
        ?string $printer_uuid = null,
        ?array  $meta = null,
        ?array  $ppd_options = null,
        ?string $status = null
    ): void
    {
        $printer_uuid = config(sprintf("webprint.printers.%s", $printer_uuid)) ?? $printer_uuid;

        parent::UpdatePromise($uuid, $name, $printer_uuid, $meta, $ppd_options, $status);
    }

    public function CreatePromise(
        string  $name,
        string  $type,
        ?array  $meta = null,
        ?string $printer_uuid = null,
        ?array  $available_printers = null,
        ?array  $ppd_options = null,
        ?string $content = null,
        ?string $file_name = null,
        ?bool   $headless = null
    ): Promise
    {
        $printer_uuid = config(sprintf("webprint.printers.%s", $printer_uuid)) ?? $printer_uuid;

        return parent::CreatePromise($name, $type, $meta, $printer_uuid, $available_printers, $ppd_options, $content, $file_name, $headless);
    }
}
