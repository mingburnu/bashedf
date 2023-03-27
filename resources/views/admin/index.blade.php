@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.dashboard')}}</h2>
                </div>
                <div class="col-auto">
                    <h6 class="text-muted">{{ date("Y/m/d") }}</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid  pt-5 pb-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        {{__('ui.today') . __('ui.payment') . __('validation.attributes.total_amount')}}
                    </div>
                    <div class="card-body d-flex justify-content-between">
                        <h4>
                            <span><i class="fas fa-dollar-sign"></i></span>
                            <span>{{$todayPaymentAmountsSum}}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        {{__('ui.today') . __('ui.payment') . __('validation.attributes.processing_fee') . __('ui.sum')}}
                    </div>
                    <div class="card-body d-flex justify-content-between">
                        <h4>
                            <span><i class="fas fa-dollar-sign"></i></span>
                            <span>{{$todayPaymentProcessingFeesSum}}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        {{__('ui.today') . __('ui.deposit') . __('validation.attributes.processing_fee') . __('ui.sum')}}
                    </div>
                    <div class="card-body d-flex justify-content-between">
                        <h4>
                            <span><i class="fas fa-dollar-sign"></i></span>
                            <span>{{$todayDepositProcessingFeesSum}}</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid ">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        {{__('ui.all') . __('ui.merchant') . __('validation.attributes.balance') . __('ui.sum')}}
                    </div>
                    <div class="card-body d-flex justify-content-between">
                        <h4>
                            <span><i class="fas fa-dollar-sign"></i></span>
                            <span>{{$todayWalletBalancesSum}}</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid  pt-5 pb-5">
        <div class="container-fluid">
            <div class="row mt-2">
                <div class="col-sm-auto mt-4">
                    <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                        <a href="https://www.showdoc.com.cn/p/b9d53524dd5a636a3c6cf5422ec6be48" target="_blank">
                            <div class="card-body">
                                <div class="text-center">
                                    <img class="m-auto" src="{{ asset('images/spec.png') }}" alt="Card image cap">
                                    <p class="font-weight-bold mt-3">{{__('ui.api_information')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @can('user')
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('admin.users.index')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/merchant_user.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.merchant') . __('ui.list')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('admin')
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('admin.admins.index')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/admin_general_user.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.admin') . __('ui.list')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('deposit')
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('admin.deposits.index')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/stored_value.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.deposit') . __('ui.list')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('payment')
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('admin.payments.index')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/payment_detail_request.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.payment') . __('ui.list')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('report')
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('admin.reports.index')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/index_month.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.query') . __('ui.report')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('wallet')
                    <div class="col-sm-auto mt-4">
                        <div class="card shadow-sm w-64" @mouseenter="enlargeShadow" @mouseleave="shrinkShadow">
                            <a href="{{route('admin.wallets.index')}}">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="m-auto" src="{{ asset('images/substitute_payment_single.png') }}"
                                             alt="Card image cap">
                                        <p class="font-weight-bold mt-3">{{__('ui.wallet') . __('ui.list')}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
        <div class="container-fluid"></div>
    </div>
@endsection

@push('js')
    <script>
        Vue.createApp({
            data() {
                return {}
            },
            methods: {
                enlargeShadow(event) {
                    event.target.classList.remove('shadow-sm');
                    event.target.classList.add('shadow-lg');
                },
                shrinkShadow(event) {
                    event.target.classList.remove('shadow-lg');
                    event.target.classList.add('shadow-sm');
                }
            }
        }).mount('#main');
    </script>
@endpush
