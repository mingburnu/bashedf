<?php

namespace App\Observers;

use App\Entities\Node;
use App\Entities\User;
use App\Repositories\AuthorizerRepository;
use App\Repositories\NodeRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\MerchantSettingRepository;
use App\Repositories\WhiteIpRepository;
use App\Services\SysService;
use Exception;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param User $user
     * @return void
     * @throws RepositoryException
     * @throws ValidatorException
     */
    public function created(User $user)
    {
        if (auth('admin')->check()) {
            app(WalletRepository::class)->create(['user_id' => $user->id]);
            app(NodeRepository::class)->createRoot($user->id);
            app(MerchantSettingRepository::class)->create(['user_id' => $user->id]);
            app(AuthorizerRepository::class)->create(['user_id' => $user->id]);
        } else {
            app(NodeRepository::class)->createChild(auth('user')->id(), $user->id);
        }
    }

    /**
     * Handle the user "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function deleted(User $user)
    {
        if (!is_null($user->api_key)) {
            $user->node->descendants()->each(function (Node $node) {
                app(UserRepository::class)->delete($node->user_id);
            });

            $user->tokens()->delete();
            $user->admins()->sync([]);
            $user->bankCards()->sync([]);
            $user->whiteIps()->delete();
            app(WhiteIpRepository::class)->whereUserId($user->id)->delete();
            app(SysService::class)->resetOrderCount();
        }

        app(NodeRepository::class)->whereUserId($user->id)->delete();
        $user->email = $user->email . 'D' . now()->timestamp;
        $user->save();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param User $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}