# WebPrint Api Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kduma/webprint-client.svg?style=flat-square)](https://packagist.org/packages/kduma/webprint-client)
[![Build Status](https://img.shields.io/travis/kduma/webprint-client/master.svg?style=flat-square)](https://travis-ci.org/kduma/webprint-client)
[![Quality Score](https://img.shields.io/scrutinizer/g/kduma/webprint-client.svg?style=flat-square)](https://scrutinizer-ci.com/g/kduma/webprint-client)
[![Total Downloads](https://img.shields.io/packagist/dt/kduma/webprint-client.svg?style=flat-square)](https://packagist.org/packages/kduma/webprint-client)

API client for WebPrint Server

## Installation

You can install the package via composer:

```bash
composer require kduma/webprint-client
```

## Usage

Available Methods:

```php
//Create Api Client Instance
$api = new \KDuma\WebPrintClient\WebPrintApi(
    endpoint: 'https://print.server.local/api/web-print', 
    key: 'API_KEY'
);
    
// Get List of all available printers
$printers = $api->GetPrinters();

// Get List of all printers capable processing ZPL language
$printers = $api->GetPrinters(type_filter: 'zpl');

// Get Printer Details
$printer = $api->GetPrinter(
    uuid: '00000000-0000-0000-0000-000000000000'
);

// Get List of recently submitted Promises
$promises = $api->GetPromises(
    page: 1, 
    total_pages: &$total_pages // passed by reference
);

// Get Promise Details
$promise = $api->GetPromise(
    uuid: '00000000-0000-0000-0000-000000000000'
);

// Create Promise
$promise = $api->CreatePromise(
    name: 'Print Job', 
    type: 'zpl',
    meta: ['format' => '4x6"']
);

// Create and Immediately print Promise
$promise = $api->CreatePromiseAndPrint(
    name: 'Print Job',
    type: 'zpl',
    printer_uuid: '00000000-0000-0000-0000-000000000000',
    file_name: 'label.zpl',
    content: '^XA...'
);

// Get Promise Details
$promise = $api->GetPromise(
    uuid: '00000000-0000-0000-0000-000000000000'
);

// Update Promise
$api->UpdatePromise(
    uuid: '00000000-0000-0000-0000-000000000000', 
    name: 'Updated', 
    printer_uuid: '00000000-0000-0000-0000-000000000000', 
    meta: ['dupa' => 123],
    ppd_options: [], 
    status: 'ready'
);

// Delete Promise
$api->DeletePromise(
    uuid: '00000000-0000-0000-0000-000000000000'
);

// Get Promise Content
$content = $api->GetPromiseContent(
    uuid: '00000000-0000-0000-0000-000000000000'
);

// Upload Promise Content
$api->SetPromiseContent(
    uuid: '00000000-0000-0000-0000-000000000000', 
    content: fopen('myfile.txt', 'r'), 
    file_name: 'myfile.txt'
);

// Send Promise to Print Queue
$api->PrintPromise(
    uuid: '00000000-0000-0000-0000-000000000000'
);

// Create Print Dialog
$dialog = $api->CreateDialog(
    uuid: '00000000-0000-0000-0000-000000000000',
    auto_print: true,
    redirect_url: 'http://example.com/',
    restricted_ip: '127.0.0.1'
);

// Get Print Dialog
$dialog = $api->GetDialog(
    uuid: '00000000-0000-0000-0000-000000000000'
);
```

Sample: Use create print Promise and redirect user to print Dialog; Content upload after response termination
```php
public function PrintDocument()
{
    $api = new \KDuma\WebPrintClient\WebPrintApi(
        endpoint: 'https://print.server.local/api/web-print', 
        key: 'API_KEY'
    );
    
    // Create Promise
    $promise = $api->CreatePromise(
        name: 'Test Document', 
        type: 'ppd',
        meta: [
            'pages' => '2',
            'date' => '2021-09-12'
        ]
    );
    
    // Create Print Dialog
    $dialog = $api->CreateDialog(
        uuid: $promise,
        auto_print: true,
        redirect_url: 'http://example.com/return-url',
    );
    
    App::terminating(function () use ($promise, $api) {
        $pdf = PDF::loadView('documents.test');
    
        // Upload Promise Content
        $api->SetPromiseContent(
            uuid: $promise, 
            content: $pdf->output(), 
            file_name: 'test.pdf'
        );
    });
    
    return redirect($dialog->getLink());
}
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email git@krystian.duma.sh instead of using the issue tracker.

## Credits

- [Krystian Duma](https://github.com/kduma)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## PHP Package Boilerplate

This package was generated using the [PHP Package Boilerplate](https://laravelpackageboilerplate.com).
