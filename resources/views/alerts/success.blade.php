@if (session('result')===true)
    <nav aria-label="breadcrumb" class="">
        <ol class="breadcrumb mt-5 bg-info">
            <li class="breadcrumb-item active text-white" aria-current="page">{{__('message.success')}}</li>
        </ol>
    </nav>
@endif
@if (session('result')===false)
    <div class="alert alert-danger">{{__('message.try_again')}}</div>
@endif
