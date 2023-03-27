<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Rules\SignRule;
use App\Services\SignService;
use App\Transformers\UserTransformer;
use App\Validators\UserValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Log;
use Prettus\Validator\Exceptions\ValidatorException;
use Throwable;

class WalletsController extends Controller
{
    protected UserRepository $repository;

    protected UserValidator $validator;

    /**
     * WalletsController constructor.
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
     * Display the specified resource.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function index(): JsonResponse
    {
        try {
            $rules = ['sign' => ['required', 'string', new SignRule(request()->all(), $this->merchant->api_key)]];
            $this->validator->setRules($rules)->with(request()->all())->passesOrFail();
            $data = app(UserTransformer::class)->transform($this->merchant);
            return response()->json(app(SignService::class)->sign(compact('data'), $this->merchant->api_key));
        } catch (ValidatorException $e) {
            throw ValidationException::withMessages($e->getMessageBag()->toArray());
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
