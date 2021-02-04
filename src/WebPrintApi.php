<?php


namespace KDuma\WebPrintClient;


use Exception;
use GuzzleHttp\Exception\GuzzleException;
use KDuma\WebPrintClient\HttpClient\HttpClientInterface;
use KDuma\WebPrintClient\Response\Dialog;
use KDuma\WebPrintClient\Response\Printer;
use KDuma\WebPrintClient\Response\Promise;
use KDuma\WebPrintClient\Response\Server;
use DateTimeImmutable;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class WebPrintApi
{
    protected HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string|null $type_filter
     * @param bool        $with_ppd_options
     *
     * @return array|Printer[]
     * @throws GuzzleException
     */
    public function GetPrinters(string $type_filter = null, bool $with_ppd_options = false): array
    {
        $query = [];

        if($type_filter)
            $query['type'] = $type_filter;

        if($with_ppd_options)
            $query['ppd_options'] = 1;

        $response = $this->client->get('printers', $query);
        $body = json_decode($response, true);

        return array_map(fn($row) => new Printer(
            $row['uuid'],
            $row['name'],
            $row['ppd_support'],
            $row['raw_languages_supported'],
            new Server(
                $row['server']['name'],
                $row['server']['uuid'],
            ),
            $row['ppd_options'] ?? null,
            $row['ppd_options_layout'] ?? null,
        ), $body['data']);
    }

    public function GetPrinter(string $uuid): Printer
    {
        $response = $this->client->get(sprintf("printers/%s", urlencode($uuid)));
        $body = json_decode($response, true);

        return new Printer(
            $body['data']['uuid'],
            $body['data']['name'],
            $body['data']['ppd_support'],
            $body['data']['raw_languages_supported'],
            new Server(
                $body['data']['server']['name'],
                $body['data']['server']['uuid'],
            )
        );
    }

    /**
     * @param int      $page
     * @param int|null $total_pages
     *
     * @return array|Promise[]
     * @throws GuzzleException
     */
    public function GetPromises(int $page = 1, int &$total_pages = null): array
    {
        $response = $this->client->get('promises', ['page' => $page]);
        $body = json_decode($response, true);

        $total_pages = $body['meta']['last_page'];

        return array_map(fn($row) => new Promise(
            $row['uuid'],
            $row['status'],
            $row['name'],
            $row['type'],
            $row['ppd_options'],
            $row['content_available'],
            $row['file_name'],
            $row['size'],
            $row['meta'],
            new DateTimeImmutable($row['created_at']),
            new DateTimeImmutable($row['updated_at']),
            new Printer(
                $row['selected_printer']['uuid'],
                $row['selected_printer']['name'],
                $row['selected_printer']['ppd_support'],
                $row['selected_printer']['raw_languages_supported']
            )
        ), $body['data']);
    }

    public function GetPromise(string $uuid): Promise
    {
        $response = $this->client->get(sprintf("promises/%s", urlencode($uuid)));

        return $this->_parsePromiseResponse($response);
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
        string $uuid, ?string $name = null, ?string $printer_uuid = null, ?array $meta = null, ?array $ppd_options = null, ?string $status = null
    ): void
    {
        $this->client->put(sprintf("promises/%s", urlencode($uuid)), [
            'name' => $name,
            'printer' => $printer_uuid,
            'meta' => $meta,
            'ppd_options' => $ppd_options,
            'status' => $status,
        ]);
    }

    public function CreatePromise(
        string $name, string $type, ?array $meta = null, ?string $printer_uuid = null, ?array $available_printers = null,
        ?array $ppd_options = null, ?string $content = null, ?string $file_name = null, ?bool $headless = null
    ): Promise
    {
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

        return $this->_parsePromiseResponse($response);
    }

    public function CreatePromiseAndPrint(
        string $name, string $type, string $printer_uuid, string $file_name, string $content, ?array $ppd_options = null
    ):Promise
    {
        return $this->CreatePromise(
            $name, $type, null, $printer_uuid, null, $ppd_options, $content, $file_name, true
        );
    }


    public function CreateDialog(string $uuid, bool $auto_print, string $redirect_url, string $restricted_ip = null): Dialog
    {
        $response = $this->client->post(sprintf("promises/%s/dialog", urlencode($uuid)), [
            'restricted_ip' => $restricted_ip,
            'redirect_url' => $redirect_url,
            'auto_print' => $auto_print,
        ]);

        return $this->_parseDialogResponse($response);
    }

    public function GetDialog(string $uuid): Dialog
    {
        $response = $this->client->get(sprintf("promises/%s/dialog", urlencode($uuid)));

        return $this->_parseDialogResponse($response);
    }


    public function GetPromiseContent(string $uuid): StreamInterface
    {
        $response = $this->client->get(sprintf("promises/%s/content", urlencode($uuid)));

        return $response->getBody();
    }

    public function SetPromiseContent(string $uuid, $content, ?string $file_name = null)
    {
        $this->client->rawPost(
            sprintf("promises/%s/content", urlencode($uuid)),
            $content,
            $file_name ? ['X-File-Name' => $file_name] : []
        );
    }



    /**
     * @param $response
     *
     * @return Promise
     * @throws Exception
     */
    private function _parsePromiseResponse($response): Promise
    {
        $body = json_decode($response, true);

        return new Promise(
            $body['data']['uuid'],
            $body['data']['status'],
            $body['data']['name'],
            $body['data']['type'],
            $body['data']['ppd_options'],
            $body['data']['content_available'],
            $body['data']['file_name'],
            $body['data']['size'],
            $body['data']['meta'],
            new DateTimeImmutable($body['data']['created_at']),
            new DateTimeImmutable($body['data']['updated_at']),
            $body['data']['selected_printer'] ? new Printer(
                $body['data']['selected_printer']['uuid'],
                $body['data']['selected_printer']['name'],
                $body['data']['selected_printer']['ppd_support'],
                $body['data']['selected_printer']['raw_languages_supported']
            ) : null,
            array_map(fn($row) => new Printer(
                $row['uuid'],
                $row['name'],
                $row['ppd_support'],
                $row['raw_languages_supported'],
            ), $body['data']['available_printers'])
        );
    }

    /**
     * @param $response
     *
     * @return Dialog
     * @throws Exception
     */
    private function _parseDialogResponse($response): Dialog
    {
        $body = json_decode($response, true);

        return new Dialog(
            $body['data']['uuid'],
            $body['data']['status'],
            $body['data']['auto_print'],
            $body['data']['redirect_url'],
            $body['data']['restricted_ip'],
            $body['data']['link'],
            new DateTimeImmutable($body['data']['created_at']),
            new DateTimeImmutable($body['data']['updated_at'])
        );
    }
}
