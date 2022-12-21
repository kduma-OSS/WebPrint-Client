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

    public function GetPrinter(string $ulid): Printer
    {
        $response = $this->client->get(sprintf("printers/%s", urlencode($ulid)));

        return Printer::fromResponse($response);
    }

    public function GetPromises(int $page = 1, int &$total_pages = null): array
    {
        $response = $this->client->get('promises', ['page' => $page]);
        $body = json_decode($response, true);

        $total_pages = $body['meta']['last_page'];

        return array_map(fn ($row) => Promise::fromResponse($row), $body['data']);
    }

    public function GetPromise(string $ulid): Promise
    {
        $response = $this->client->get(sprintf("promises/%s", urlencode($ulid)));

        return Promise::fromResponse($response);
    }

    public function DeletePromise(string $ulid): void
    {
        $this->client->delete(sprintf("promises/%s", urlencode($ulid)));
    }

    public function PrintPromise(string $ulid)
    {
        $this->client->post("jobs", [
            'promise' => $ulid,
        ]);
    }

    public function UpdatePromise(
        string $ulid,
        ?string $name = null,
        ?string $printer_ulid = null,
        ?array $meta = null,
        ?array $ppd_options = null,
        ?string $status = null
    ): void {
        $this->client->put(sprintf("promises/%s", urlencode($ulid)), [
            'name' => $name,
            'printer' => $printer_ulid,
            'meta' => $meta,
            'ppd_options' => $ppd_options,
            'status' => $status,
        ]);
    }

    public function CreatePromise(
        string $name,
        string $type,
        ?array $meta = null,
        ?string $printer_ulid = null,
        ?array $available_printers = null,
        ?array $ppd_options = null,
        ?string $content = null,
        ?string $file_name = null,
        ?bool $headless = null
    ): Promise {
        $response = $this->client->post('promises', [
            'name' => $name,
            'type' => $type,
            'printer' => $printer_ulid,
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
        string $printer_ulid,
        string $file_name,
        string $content,
        ?array $ppd_options = null
    ): Promise {
        return $this->CreatePromise(
            $name,
            $type,
            null,
            $printer_ulid,
            null,
            $ppd_options,
            $content,
            $file_name,
            true
        );
    }


    public function CreateDialog(string $ulid, bool $auto_print, string $redirect_url, string $restricted_ip = null): Dialog
    {
        $response = $this->client->post(sprintf("promises/%s/dialog", urlencode($ulid)), [
            'restricted_ip' => $restricted_ip,
            'redirect_url' => $redirect_url,
            'auto_print' => $auto_print,
        ]);

        return Dialog::fromResponse($response);
    }

    public function GetDialog(string $ulid): Dialog
    {
        $response = $this->client->get(sprintf("promises/%s/dialog", urlencode($ulid)));

        return Dialog::fromResponse($response);
    }


    public function GetPromiseContent(string $ulid): StreamInterface
    {
        $response = $this->client->get(sprintf("promises/%s/content", urlencode($ulid)));

        return $response;
    }

    public function SetPromiseContent(string $ulid, $content, ?string $file_name = null)
    {
        $this->client->rawPost(
            sprintf("promises/%s/content", urlencode($ulid)),
            $content,
            $file_name ? ['X-File-Name' => $file_name] : []
        );
    }
}
