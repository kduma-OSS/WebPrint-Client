<?php

namespace KDuma\WebPrintClient\Laravel;

use Illuminate\Support\LazyCollection;
use KDuma\WebPrintClient\WebPrintApiInterface;

interface LaravelWebPrintApiInterface extends WebPrintApiInterface
{
    public function GetPromisesLazy(): LazyCollection;
}
