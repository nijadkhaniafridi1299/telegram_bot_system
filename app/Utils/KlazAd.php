<?php

namespace App\Utils;

use Carbon\Carbon;

class KlazAd
{
    public function __construct(
        public int        $adId,
        public string     $title,
        public int        $price,
        public bool       $shippingEnabled,
        public float|null $shippingPrice,
        public string     $zip,
        public string     $city,
        public string     $description,
        public string     $sellerName,
        public string     $sellerKlazId,
        public array      $imageLinks,
        public Carbon     $creationDate,
//        public string $views,
    )
    {
        //
    }
}
