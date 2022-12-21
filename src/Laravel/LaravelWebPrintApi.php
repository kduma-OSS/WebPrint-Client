<?php

namespace KDuma\WebPrintClient\Laravel;

use KDuma\WebPrintClient\Response\Printer;
use KDuma\WebPrintClient\Response\Promise;
use KDuma\WebPrintClient\WebPrintApi;
use KDuma\WebPrintClient\WebPrintApiInterface;

class LaravelWebPrintApi extends WebPrintApi implements WebPrintApiInterface
{
    public function GetPrinter(string $ulid): Printer
    {
        return parent::GetPrinter($ulid);
    }

    public function UpdatePromise(
        string  $ulid,
        ?string $name = null,
        ?string $printer_ulid = null,
        ?array  $meta = null,
        ?array  $ppd_options = null,
        ?string $status = null
    ): void {
        $printer_ulid = config(sprintf("webprint.printers.%s", $printer_ulid)) ?? $printer_ulid;

        parent::UpdatePromise($ulid, $name, $printer_ulid, $meta, $ppd_options, $status);
    }

    public function CreatePromise(
        string  $name,
        string  $type,
        ?array  $meta = null,
        ?string $printer_ulid = null,
        ?array  $available_printers = null,
        ?array  $ppd_options = null,
        ?string $content = null,
        ?string $file_name = null,
        ?bool   $headless = null
    ): Promise {
        $printer_ulid = config(sprintf("webprint.printers.%s", $printer_ulid)) ?? $printer_ulid;

        return parent::CreatePromise($name, $type, $meta, $printer_ulid, $available_printers, $ppd_options, $content, $file_name, $headless);
    }
}
