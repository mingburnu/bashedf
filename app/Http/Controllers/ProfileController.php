<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Repositories\MerchantSettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\WhiteIpRepository;
use App\Validators\UserValidator;
use DB;
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

/**
 * Class ProfileController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProfileController extends Controller
{
    protected UserRepository $repository;

    protected UserValidator $validator;

    /**
     * UsersController constructor.
     *
     * @param UserRepository $repository
     * @param UserValidator $validator
     */
    public function __construct(UserRepository $repository, UserValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $merchant = $this->merchant;
        return view('profile.index', compact('merchant'));
    }

    /**
     * @return View|Factory|RedirectResponse|Application
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     * @throws ValidatorException
     */
    public function bind(): View|Factory|RedirectResponse|Application
    {
        if (!is_null($this->clerk->google2fa_secret)) {
            return redirect()->route('profile.index');
        }

        $google2fa_secret = Google2FA::generateSecretKey();
        $user = $this->repository->update(compact('google2fa_secret'), $this->clerk->id);
        return view('profile.google2fa-secret.index', compact('user'));
    }

    public function generate(): JsonResponse
    {
        try {
            $this->validator->setRules(['password' => ['required', 'password:user']])->with(request()->all())->passesOrFail();
            $messages = [$this->merchant->google2fa_secret, $this->merchant->qr_code];
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

    /**
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        try {
            $this->validator->setRules(['password' => ['required', 'password:user']])->with(request()->all())->passesOrFail();
            $messages = [$this->merchant->api_key];
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

    /**
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function link(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            app(MerchantSettingRepository::class)->update($request->only(['default_payment_callback_url']), $this->merchant->id);
            DB::commit();
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }

    /**
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function configure(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            app(MerchantSettingRepository::class)->update($request->only('api_token_switch'), $this->merchant->id);
            DB::commit();
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }

    /**
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function fill(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $old_list = $this->merchant->whiteIps->pluck('ip')->mapWithKeys(fn($ip) => [$ip => $ip]);

            $new_list = collect($request->input('white_ips'))->mapWithKeys(fn($ip) => is_null($ip) ? [] : [$ip => $ip]);

            $intersection = $old_list->intersect($new_list->values());

            DB::beginTransaction();
            $old_list->forget($intersection->keys()->toArray())->each(fn($ip) => app(WhiteIpRepository::class)->whereUserId($this->merchant->id)->whereIp($ip)->delete());
            $new_list->forget($intersection->keys()->toArray())->each(fn($ip) => app(WhiteIpRepository::class)->create(compact('ip') + ['user_id' => $this->merchant->id]));

            DB::commit();
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}