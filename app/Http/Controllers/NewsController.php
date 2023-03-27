<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Repositories\NewsRepository;
use App\Validators\NewsValidator;

/**
 * Class NewsController.
 *
 * @package namespace App\Http\Controllers;
 */
class NewsController extends Controller
{
    protected NewsRepository $repository;

    protected NewsValidator $validator;

    /**
     * NewsController constructor.
     *
     * @param NewsRepository $repository
     * @param NewsValidator $validator
     */
    public function __construct(NewsRepository $repository, NewsValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function index(): Factory|View|Application
    {
        $news = $this->repository->whereStatus(1)->get();
        return view('news.index', compact('news'));
    }
}