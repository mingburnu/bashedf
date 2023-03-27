@extends('layouts.app')
@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.new') . __('ui.list')}}</h2>
                </div>
            </div>
            <div class="accordion" id="accordionExample">
                @foreach ($news as $new)
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center"
                             data-toggle="collapse" data-target="#new{{$new->id}}">
                            <h2 class="mb-0">
                                {{$new->title}}
                            </h2>
                            <small>
                                {{$new->created_at->format('Y-m-d')}}
                            </small>
                        </div>

                        <div id="new{{$new->id}}" class="collapse" aria-labelledby="#new{{$new->id}}"
                             data-parent="#accordionExample">
                            <div class="card-body">
                                {!!nl2br(e($new->content))!!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection