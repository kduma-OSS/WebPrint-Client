<?php

namespace KDuma\WebPrintClient\Laravel;

use Illuminate\Support\LazyCollection;
use KDuma\WebPrintClient\Response\Printer;
use KDuma\WebPrintClient\Response\Promise;
use KDuma\WebPrintClient\WebPrintApi;

class LaravelWebPrintApi extends WebPrintApi implements LaravelWebPrintApiInterface
{
    public function GetPrinter(string $ulid): Printer
    {
        $ulid = config(sprintf("webprint.printers.%s", $ulid)) ?? $ulid;

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

    public function GetPromisesLazy(): LazyCollection
    {
        return new LazyCollection(function () {
            $page = 1;
            $total_pages = 1;

            while ($page <= $total_pages) {
                var_dump("Loading page $page/$total_pages");
                $promises = $this->getPromises($page, $total_pages);

                if (empty($promises)) {
                    break;
                }

                foreach ($promises as $promise) {
                    yield $promise;
                }

                $page++;
            }
        });
    }
}
