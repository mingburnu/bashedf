<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use App\Validators\AdminValidator;
use Google2FA;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Log;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use Prettus\Validator\Exceptions\ValidatorException;
use Throwable;

class ProfileController extends Controller
{
    protected AdminRepository $repository;

    protected AdminValidator $validator;

    /**
     * ProfileController constructor.
     *
     * @param AdminRepository $repository
     * @param AdminValidator $validator
     */
    public function __construct(AdminRepository $repository, AdminValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function index(): View|Factory|Application
    {
        return view('admin.profile.index');
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     * @throws ValidatorException
     */
    public function bind(): View|Factory|RedirectResponse|Application
    {
        if (!is_null($this->admin->google2fa_secret)) {
            return redirect()->route('admin.profile.index');
        }

        $google2fa_secret = Google2FA::generateSecretKey();
        $admin = $this->repository->update(compact('google2fa_secret'), $this->admin->id);
        return view('admin.profile.google2fa-secret.index', compact('admin'));
    }

    public function generate(): JsonResponse
    {
        try {
            $this->validator->setRules(['password' => ['required', 'password:admin']])->with(request()->all())->passesOrFail();
            $messages = [$this->admin->google2fa_secret, $this->admin->qr_code];
            return response()->json(compact('messages'));
        } catch (ValidatorException $e) {
            $errors = $e->getMessageBag();
            return response()->json(compact('errors'), 422);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}