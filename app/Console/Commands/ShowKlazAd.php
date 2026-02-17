<?php

namespace App\Console\Commands;

use App\Actions\GrabKlazAd;
use Illuminate\Console\Command;
use function app;

class ShowKlazAd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:show-klaz-ad {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $ad = app(GrabKlazAd::class)->grab($this->argument('url'));

        $shipping = $ad->shippingEnabled ? 'true' : 'false';
        $shippingPrice = $ad->shippingPrice ?? '-/-';

        $this->info("Id: {$ad->adId}");
        $this->info("Title: {$ad->title}");
        $this->info("Price: {$ad->price}");
        $this->info("Shipping: {$shipping}");
        $this->info("Shippinh price: {$shippingPrice}");
        $this->info("Zip: {$ad->zip}");
        $this->info("City: {$ad->city}");
        $this->info("Created at: {$ad->creationDate->format('d.m.Y')}");
        $this->info("Seller name: {$ad->sellerName}");
        $this->info("Seller id: {$ad->sellerKlazId}");
        $this->newLine();
        $this->info("Image links:");
        foreach ($ad->imageLinks as $imageLink) {
            $this->line($imageLink);
        }
        $this->newLine();
        $this->info("Description:");
        $this->line($ad->description);
    }
}
