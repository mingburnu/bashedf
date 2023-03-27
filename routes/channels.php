<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Broadcasting\CheckerChannel;
use App\Broadcasting\FundChannel;
use App\Broadcasting\PaymentChannel;
use App\Broadcasting\DepositChannel;

Broadcast::channel('checker', CheckerChannel::class);
Broadcast::channel('payment', PaymentChannel::class);
Broadcast::channel('deposit', DepositChannel::class);
Broadcast::channel('fund', FundChannel::class);
