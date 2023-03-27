<?php

namespace App\Observers;

use App\Entities\Admin;
use Exception;

class AdminObserver
{
    /**
     * Handle the admin "created" event.
     *
     * @param Admin $admin
     * @return void
     */
    public function created(Admin $admin)
    {
        //
    }

    /**
     * Handle the admin "updated" event.
     *
     * @param Admin $admin
     * @return void
     */
    public function updated(Admin $admin)
    {
        //
    }

    /**
     * Handle the admin "deleted" event.
     *
     * @param Admin $admin
     * @return void
     * @throws Exception
     */
    public function deleted(Admin $admin)
    {
        $admin->users()->sync([]);
        $admin->syncPermissions([]);
        $admin->email = $admin->email . 'D' . now()->timestamp;
        $admin->save();
    }

    /**
     * Handle the admin "restored" event.
     *
     * @param Admin $admin
     * @return void
     */
    public function restored(Admin $admin)
    {
        //
    }

    /**
     * Handle the admin "force deleted" event.
     *
     * @param Admin $admin
     * @return void
     */
    public function forceDeleted(Admin $admin)
    {
        //
    }
}
