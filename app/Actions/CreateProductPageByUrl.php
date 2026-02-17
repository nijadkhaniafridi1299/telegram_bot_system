<?php

namespace App\Actions;

use App\Models\Image;
use App\Models\ProductPage;
use Storage;
use Str;

class CreateProductPageByUrl
{

    public function createByUrl(string $url): ProductPage
    {
        $ad = app(GrabKlazAd::class)->grab($url);

        $totalPrice = $ad->price + 0.35 + $ad->price * (4.5 / 100);
        $fees = ($totalPrice / $ad->price) - 1;

        $productPage = ProductPage::create([
            'pin_code' => Str::random(5),
            'title' => $ad->title,
            'price' => $ad->price,
            'shipping' => $ad->shippingEnabled,
            'shipping_price' => $ad->shippingPrice,
            'postal_code' => $ad->zip,
            'city' => $ad->city,
            'description' => $ad->description,
            'seller_name' => $ad->sellerName,
            'seller_klaz_user_id' => $ad->sellerKlazId,
            'klaz_url' => $url,
            'iban_name' => 'Kleinanzeigen GmbH',
            'iban' => '** CHANGE ME **',
            'bic' => '** CHANGE ME **',
            'fees' => $fees,
        ]);

        $order = 1;
        foreach ($ad->imageLinks as $imageLink) {

            $parts = explode('.', $imageLink);
            $extension = end($parts);

            $prefix = date('Y-m');
            $path = $prefix . '/' . $ad->adId . '_' . uniqid() . '.' . $extension;
            Storage::disk('public')->makeDirectory($prefix);
            $fullPath = Storage::disk('public')->path($path);

            copy($imageLink, $fullPath);

            Image::create([
                'product_page_id' => $productPage->id,
                'path' => $path,
                'order' => $order,
            ]);

            $order++;
        }

        return $productPage;
    }

}
