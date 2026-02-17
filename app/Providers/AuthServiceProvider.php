<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\DeliveryAddress;
use App\Models\Image;
use App\Models\PaymentSession;
use App\Models\ProductPage;
use App\Models\Proxy;
use App\Models\User;
use App\Models\VonageAccount;
use App\Policies\DeliveryAddressPolicy;
use App\Policies\ImagePolicy;
use App\Policies\PaymentSessionPolicy;
use App\Policies\ProductPagePolicy;
use App\Policies\ProxyPolicy;
use App\Policies\UserPolicy;
use App\Policies\VonageAccountPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        DeliveryAddress::class => DeliveryAddressPolicy::class,
        Image::class => ImagePolicy::class,
        PaymentSession::class => PaymentSessionPolicy::class,
        ProductPage::class => ProductPagePolicy::class,
        Proxy::class => ProxyPolicy::class,
        User::class => UserPolicy::class,
        VonageAccount::class => VonageAccountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
