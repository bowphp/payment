<?php

namespace Bow\Payment;

use App\Controllers\Controller;
use Bow\Http\Request;

class PaymentNotificationController extends Controller
{
    /**
     * PaymentNotificationController construct
     *
     * @return mixed
     */
    public function __construct()
    {
        $this->user = config('payment.model');
    }

    /**
     * Process payment notification here
     *
     * @param Request $request
     * @return mixed
     */
    public function handle(Request $request)
    {
        $user_id = $request->get('user_id');

        $user = $this->user->find($user_id);

        $user->payment();
    }
}
