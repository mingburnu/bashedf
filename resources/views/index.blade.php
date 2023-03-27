@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.dashboard')}}</h2>
                </div>
                <div class="col-auto">
                    <h6 class="text-muted">@{{ datetime }}</h6>
                </div>
            </div>
            <div>
                <p class="text-muted">
                    @if (is_null($previousLoginRecord))
                        <span>{{__('ui.first_login')}}</span>
                    @else
                        <span>{{__('ui.previous_login') . __('ui.ip')}}：{{$previousLoginRecord->description}} , {{__('ui.login_at')}}：@{{previousLoginAt }}</span>
                    @endif
                </p>

            </div>
            @can('use')
                <div class="row">
                    <div class="col-sm mt-2">
                        <div class="container">
                            <div class="row">
                                <p class="text-muted">{{__('validation.attributes.balance')}}</p>
                            </div>
                            <div class="row">
                                <h1 class="font-weight-bold text-dark">${{number_format($balance)}}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm mt-2">
                        <div class="container">
                            <div class="row">
                                <p class="text-muted">{{__('ui.today') . __('ui.deposit') . __('ui.sum')}}</p>
                            </div>
                            <div class="row">
                                <h1 class="font-weight-bold text-dark">
                                    ${{number_format($todayDepositTotalAmountSum)}}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm mt-2">
                        <div class="container">
                            <div class="row">
                                <p class="text-muted ">{{__('ui.today') . __('ui.payment') . __('ui.sum')}}</p>
                            </div>
                            <div class="row">
                                <h1 class="font-weight-bold text-dark">
                                    ${{number_format($todayPaymentTotalAmountSum)}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

        </div>
    </div>
    <div class="container-fluid pl-5 pt-5 pb-5">
        <div class="container-fluid">
            <div class="row mt-2">
                @can('use')
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('deposits.create')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/stored_value.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.apply') . __('ui.deposit')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('payments.index')}}">

                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/payment_detail.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.payment') . __('ui.list')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('reports.index')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/index_day.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.query') . __('ui.report')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan

                <div class="col-sm-auto mt-4">
                    <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                        <a href="{{route('payments.create')}}">
                            <div class="card-body">
                                <div class="text-center">
                                    <img class="m-auto" src="{{ asset('images/substitute_payment_multi.png') }}"
                                         alt="Card image cap">
                                    <p class="font-weight-bold mt-3">{{__('ui.apply') . __('ui.payment')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{is_null($previousLoginRecord) ? '' : $previousLoginRecord->created_at->jsonSerialize()}}" ref="previousLoginAt">
@endsection

@push('js')
    <script>
        Vue.createApp({
            data() {
                return {
                    previousLoginAt: null,
                    datetime: moment().format('YYYY-MM-DD HH:mm:ss')
                }
            },
            methods: {
                init() {
                    this.previousLoginAt = moment(this.$refs.previousLoginAt.value).format('YYYY-MM-DD HH:mm:ss');
                },
                enlargeShadow(event) {
                    event.target.classList.remove('shadow-sm');
                    event.target.classList.add('shadow-lg');
                },
                shrinkShadow(event) {
                    event.target.classList.remove('shadow-lg');
                    event.target.classList.add('shadow-sm');
                }
            }
        }).mount('#main').init();
    </script>
@endpush
