<?php

namespace KDuma\WebPrintClient;

use KDuma\WebPrintClient\HttpClient\HttpClientInterface;
use KDuma\WebPrintClient\Response\Dialog;
use KDuma\WebPrintClient\Response\Printer;
use KDuma\WebPrintClient\Response\Promise;
use Psr\Http\Message\StreamInterface;

class WebPrintApi implements WebPrintApiInterface
{
    protected HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function GetPrinters(string $type_filter = null, bool $with_ppd_options = false): array
    {
        $query = [];

        if ($type_filter) {
            $query['type'] = $type_filter;
        }

        if ($with_ppd_options) {
            $query['ppd_options'] = 1;
        }

        $response = $this->client->get('printers', $query);
        $body = json_decode($response, true);

        return array_map(fn ($row) => Printer::fromResponse($row), $body['data']);
    }

    public function GetPrinter(string $uuid): Printer
    {
        $response = $this->client->get(sprintf("printers/%s", urlencode($uuid)));

        return Printer::fromResponse($response);
    }

    public function GetPromises(int $page = 1, int &$total_pages = null): array
    {
        $response = $this->client->get('promises', ['page' => $page]);
        $body = json_decode($response, true);

        $total_pages = $body['meta']['last_page'];

        return array_map(fn ($row) => Promise::fromResponse($row), $body['data']);
    }

    public function GetPromise(string $uuid): Promise
    {
        $response = $this->client->get(sprintf("promises/%s", urlencode($uuid)));

        return Promise::fromResponse($response);
    }

    public function DeletePromise(string $uuid): void
    {
        $this->client->delete(sprintf("promises/%s", urlencode($uuid)));
    }

    public function PrintPromise(string $uuid)
    {
        $this->client->post("jobs", [
            'promise' => $uuid,
        ]);
    }

    public function UpdatePromise(
        string $uuid,
        ?string $name = null,
        ?string $printer_uuid = null,
        ?array $meta = null,
        ?array $ppd_options = null,
        ?string $status = null
    ): void {
        $this->client->put(sprintf("promises/%s", urlencode($uuid)), [
            'name' => $name,
            'printer' => $printer_uuid,
            'meta' => $meta,
            'ppd_options' => $ppd_options,
            'status' => $status,
        ]);
    }

    public function CreatePromise(
        string $name,
        string $type,
        ?array $meta = null,
        ?string $printer_uuid = null,
        ?array $available_printers = null,
        ?array $ppd_options = null,
        ?string $content = null,
        ?string $file_name = null,
        ?bool $headless = null
    ): Promise {
        $response = $this->client->post('promises', [
            'name' => $name,
            'type' => $type,
            'printer' => $printer_uuid,
            'meta' => $meta,
            'available_printers' => $available_printers,
            'ppd_options' => $ppd_options,
            'content' => $content,
            'file_name' => $file_name,
            'headless' => $headless,
        ]);

        return Promise::fromResponse($response);
    }

    public function CreatePromiseAndPrint(
        string $name,
        string $type,
        string $printer_uuid,
        string $file_name,
        string $content,
        ?array $ppd_options = null
    ): Promise {
        return $this->CreatePromise(
            $name,
            $type,
            null,
            $printer_uuid,
            null,
            $ppd_options,
            $content,
            $file_name,
            true
        );
    }


    public function CreateDialog(string $uuid, bool $auto_print, string $redirect_url, string $restricted_ip = null): Dialog
    {
        $response = $this->client->post(sprintf("promises/%s/dialog", urlencode($uuid)), [
            'restricted_ip' => $restricted_ip,
            'redirect_url' => $redirect_url,
            'auto_print' => $auto_print,
        ]);

        return Dialog::fromResponse($response);
    }

    public function GetDialog(string $uuid): Dialog
    {
        $response = $this->client->get(sprintf("promises/%s/dialog", urlencode($uuid)));

        return Dialog::fromResponse($response);
    }


    public function GetPromiseContent(string $uuid): StreamInterface
    {
        $response = $this->client->get(sprintf("promises/%s/content", urlencode($uuid)));

        return $response;
    }

    public function SetPromiseContent(string $uuid, $content, ?string $file_name = null)
    {
        $this->client->rawPost(
            sprintf("promises/%s/content", urlencode($uuid)),
            $content,
            $file_name ? ['X-File-Name' => $file_name] : []
        );
    }
}
