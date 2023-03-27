<?php

namespace App\Http\Controllers;

use App\Entities\Admin;
use App\Entities\User;
use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected ?User $clerk;
    protected ?Admin $admin;
    protected ?User $merchant;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return $this->setBasicUser($request, $next);
        });
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    private function setBasicUser(Request $request, Closure $next): mixed
    {
        $user = auth()->user();
        if ($user instanceof Admin) {
            $this->admin = $user;
        } else if ($user instanceof User) {
            $this->clerk = $user;
            $this->merchant = is_null($this->clerk->api_key) ? $this->clerk->node->parent->user : $this->clerk;
        }

        return $next($request);
    }

    protected function initRepositoryParams()
    {
        collect(config('repository.criteria.params'))->each(fn($param) => request()->request->remove($param));
    }

    protected function escapeSpecificChars($value): Str|string
    {
        return is_string($value) ? Str::replace([':', ';'], ['：', '；'], $value) : '';
    }
}