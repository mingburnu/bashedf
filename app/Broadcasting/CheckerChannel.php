<?php

namespace App\Broadcasting;

use App\Entities\Admin;

class CheckerChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param Admin $admin
     * @return bool
     */
    public function join(Admin $admin): bool
    {
        return  $admin->canAny(['payment', 'deposit']);
    }
}
