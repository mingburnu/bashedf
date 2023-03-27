@extends('layouts.app')

@push('css')
    <style>
        .divTableCell > p, .divTableHead > p {
            padding: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">{{__('ui.account_settings')}}</h2>

        <table class="table table-bordered w-100">
            <thead>
            <tr>
                <th class="align-top">{{__('validation.attributes.merchant_id')}}</th>
                <td>{{$merchant->merchant_id}}</td>
            </tr>
            <tr>
                <th class="align-top">{{__('validation.attributes.api_key')}}</th>
                <td>
                    <button class="btn btn-success" title="show" @click="showApiKey">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <th>{{__('ui.change_password')}}</th>
                <td>
                    <button class="btn btn-primary" title="edit" @click="resetPassword">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <th class="align-top">{{__('ui.google_authenticator')}}</th>
                <td>
                    @if (!is_null($merchant->google2fa_secret))
                        <button class="btn btn-success" @click="showTwoFa"><i class="fas fa-eye"></i></button>
                    @else
                        @can('use')
                            <form method="post" action="{{route('profile.google2fa-secret.bind')}}">
                                @csrf
                                @method('patch')
                                <button class="btn btn-warning" @click.prevent="bindTwoFa">
                                    {{__('ui.use_google_authenticator')}}
                                </button>
                            </form>
                        @endcan
                        @cannot('use')
                            {{__('message.your_merchant_never_use')}}
                        @endcannot
                    @endif
                </td>
            </tr>
            @can('use')
                <tr>
                    <th class="align-top">{{__('validation.attributes.default_payment_callback_url')}}</th>
                    <td>
                        <span>{{$merchant->merchantSetting->default_payment_callback_url}}</span>
                        <button class="ml-2 btn btn-primary" title="edit" @click="resetDefaultPaymentCallbackURL">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <th class="align-top">{{__('ui.two_way_validation_api')}}</th>
                    <td>
                        <div class="divTable">
                            <div class="divTableRow">
                                <div class="divTableHead"><i class="fas fa-link"></i></div>
                                <div class="divTableHead">{{__('validation.attributes.boolean_index')}}</div>
                                <div class="divTableHead">{{__('ui.post_data')}}</div>
                                <div class="divTableHead">{{__('ui.field_name')}}</div>
                                <div class="divTableHead">{{__('validation.attributes.additional_parameters')}}</div>
                                <div class="divTableHead"></div>
                            </div>
                            <div class="divTableRow">
                                <div class="divTableCell">
                                    <p>{{Auth::user()->authorizer->api ?? ''}}</p>
                                </div>
                                <div class="divTableCell">
                                    <p>{{Auth::user()->authorizer->boolean_index ?? ''}}</p>
                                </div>
                                <div class="divTableCell">
                                    <p>merchant_id</p>
                                    <p>customized_id</p>
                                    <p>account_number</p>
                                    <p>amount</p>
                                </div>
                                <div class="divTableCell">
                                    <p>{{__('validation.attributes.merchant_id')}}</p>
                                    <p>{{__('validation.attributes.order_id')}}</p>
                                    <p>{{__('validation.attributes.account_number')}}</p>
                                    <p>{{__('validation.attributes.amount')}}</p>
                                </div>
                                <div class="divTableCell">
                                    <p>{{Auth::user()->authorizer->additional_parameters}}</p>
                                </div>
                                <div class="divTableCell">
                                    <p>
                                        <a href="{{route('authorizer.edit')}}"
                                           class="btn btn-primary" title="edit"><i class="fas fa-edit"></i></a>
                                        <input type="hidden" value="{{Auth::user()->authorizer->supervising}}">
                                        <button class="ml-2 btn btn-secondary" @click="configureAuthorizerOption"
                                                title="configure">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="align-top">{{__('validation.attributes.api_token_switch')}}</th>
                    <td>
                        <input type="hidden" value="{{Auth::user()->merchantSetting->api_token_switch}}">
                        <button class="btn btn-secondary" @click="setApiTokenSwitch" title="configure">
                            <i class="fas fa-cog"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <th class="align-top">{{__('ui.api_token_white_list')}}</th>
                    <td>
                        <div class="divTable w-[35%]">
                            <div class="divTableRow">
                                <div class="divTableCell w-[85%]">
                                    @if (Auth::user()->whiteIps->count() > 0)
                                        @foreach(Auth::user()->whiteIps as $ip)
                                            {{$ip->ip}}<br>
                                        @endforeach
                                    @else
                                        {{__('ui.no_limit')}}
                                    @endif
                                </div>
                                <div class="divTableCell">
                                    <button class="btn btn-secondary m-1" @click="editWhiteList" title="configure">
                                        <i class="fas fa-server"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endcan
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script>
        Vue.createApp({
            data() {
                return {}
            },
            methods: {
                showApiKey: function () {
                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('message.please_enter', {value: Lang.get('validation.attributes.password')}),
                        input: 'password',
                        preConfirm: function (password) {
                            axios.post(route('users.show'), {password: password})
                                .then(function (response) {
                                    fireSuccessBox(response);
                                    setTimeout(function () {
                                        Swal.close();
                                    }, 10000);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        }
                    }));
                },
                resetPassword: function () {
                    let form,
                        html = '<div class="text-left">' + Lang.get('validation.attributes.old_password') + '</div>' +
                            '<input type="password" class="swal2-input" v-model="old_password">' +
                            '<div class="text-left">' + Lang.get('validation.attributes.new_password') + '</div>' +
                            '<input type="password" class="swal2-input" v-model="new_password">' +
                            '<div class="text-left">' + Lang.get('validation.attributes.new_password_confirmation') + '</div>' +
                            '<input type="password" class="swal2-input" v-model="new_password_confirmation">';
                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('message.please_enter', {value: Lang.get('validation.attributes.new_password')}),
                        html: html,
                        onOpen: function () {
                            form = Vue.createApp({
                                data() {
                                    return {
                                        old_password: null, new_password: null, new_password_confirmation: null
                                    }
                                }
                            }).mount('.swal2-content');
                        },
                        preConfirm: function () {
                            axios.post(route('password.update'), form.$data)
                                .then(function (response) {
                                    fireSuccessBox(response);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        }
                    }));
                },
                bindTwoFa: function (event) {
                    Swal.fire(composePopupBoxSchema({
                        title: event.target.innerHTML,
                        type: 'warning',
                    })).then((result) => {
                        if (result.value) {
                            event.target.parentElement.submit();
                        }
                    });
                },
                showTwoFa: function () {
                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('message.please_enter', {value: Lang.get('validation.attributes.password')}),
                        input: 'password',
                        preConfirm: function (password) {
                            axios.post(route('profile.qr-code.generate'), {password: password})
                                .then(function (response) {
                                    fireSuccessBox(response);
                                    setTimeout(function () {
                                        Swal.close();
                                    }, 10000);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        }
                    }));
                },
                resetDefaultPaymentCallbackURL: function (event) {
                    let btn = getEventBtn(event.target),
                        defaultPaymentCallbackURL = btn.previousElementSibling;

                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('validation.attributes.default_payment_callback_url'),
                        input: 'text',
                        inputValue: defaultPaymentCallbackURL.innerText,
                        preConfirm: function (newPaymentCallbackURL) {
                            axios.patch(route('profile.default-payment-callback-url.link'), {default_payment_callback_url: newPaymentCallbackURL})
                                .then(function (response) {
                                    defaultPaymentCallbackURL.innerText = newPaymentCallbackURL;
                                    fireSuccessBox(response);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        },
                    }));
                },
                configureAuthorizerOption: function (event) {
                    let form,
                        btn = getEventBtn(event.target),
                        supervising = btn.previousElementSibling.value,
                        html = '<div class="text-left">' + Lang.get('validation.attributes.password') + '</div>' +
                            '<input type="password" class="swal2-input" v-model="password">' +
                            '<label for="supervising_0">' + Lang.get('ui.close') + '</label>' +
                            '<input type="radio" id="supervising_0" class="w-8" v-bind:value="0" v-model="supervising">' +
                            '<div class="w-8 inline-block"></div>' +
                            '<label for="supervising_1">' + Lang.get('ui.open') + '</label>' +
                            '<input type="radio" id="supervising_1" class="w-8" v-bind:value="1" v-model="supervising">';

                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('ui.two_way_validation_api'),
                        html: html,
                        onOpen: function () {
                            form = Vue.createApp({
                                data() {
                                    return {
                                        password: null,
                                        supervising: supervising
                                    }
                                }
                            }).mount('.swal2-content');
                        },
                        preConfirm: function () {
                            axios.patch(route('authorizer.configure'), form.$data)
                                .then(function (response) {
                                    btn.previousElementSibling.value = form.supervising;
                                    fireSuccessBox(response);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        },
                    }));
                },
                setApiTokenSwitch: function (event) {
                    let form,
                        btn = getEventBtn(event.target),
                        apiTokenSwitch = btn.previousElementSibling.value,
                        html = '<div class="text-left">' + Lang.get('validation.attributes.password') + '</div>' +
                            '<input type="password" class="swal2-input" v-model="password">' +
                            '<label for="switch_0">' + Lang.get('ui.close') + '</label>' +
                            '<input type="radio" id="switch_0" class="w-8" v-bind:value="0" v-model="api_token_switch">' +
                            '<div class="w-8 inline-block"></div>' +
                            '<label for="switch_1">' + Lang.get('ui.open') + '</label>' +
                            '<input type="radio" id="switch_1" class="w-8" v-bind:value="1" v-model="api_token_switch">';

                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('validation.attributes.api_token_switch'),
                        html: html,
                        onOpen: function () {
                            form = Vue.createApp({
                                data() {
                                    return {
                                        password: null,
                                        api_token_switch: apiTokenSwitch
                                    }
                                }
                            }).mount('.swal2-content');
                        },
                        preConfirm: function () {
                            axios.patch(route('profile.api-token-switch.configure'), form.$data)
                                .then(function (response) {
                                    btn.previousElementSibling.value = form.api_token_switch;
                                    fireSuccessBox(response);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        },
                    }));
                },
                editWhiteList: function (event) {
                    let form,
                        btn = getEventBtn(event.target),
                        data = btn.parentElement.previousElementSibling.innerHTML.replaceAll('<br>', '\n').replaceAll(' ', '').slice(0, -1),
                        html = '<div class="text-left">' + Lang.get('validation.attributes.password') + '</div>' +
                            '<input type="password" class="swal2-input" v-model="password">' +
                            '<div class="text-left">' + Lang.get('ui.ip') + Lang.get('ui.list') + Lang.get('') + '</div>' +
                            '<textarea class="swal2-textarea resize-y" @input="limit" v-model="data"></textarea>' +
                            '<div class="text-left">' + Lang.get('ui.format_sample') + '</div>' +
                            '<textarea class="swal2-textarea resize-none bg-secondary text-dark" disabled>127.0.0.1\n127.0.0.2\n::1</textarea>';

                    data = btn.parentElement.previousElementSibling.innerHTML.indexOf(Lang.get('ui.no_limit')) >= 0 ? '' : data;
                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('ui.api_token_white_list'),
                        html: html,
                        width: '36rem',
                        onOpen: function () {
                            let textarea = document.getElementsByTagName('textarea')[0];
                            if (data.split('\n').length <= 4) {
                                textarea.style = 'height:138px';
                            } else {
                                textarea.style = 'height:' + ((data.split('\n').length - 4) * 26 + 138) + 'px';
                            }

                            document.getElementsByTagName('textarea')[1].style = 'height:112px'
                            form = Vue.createApp({
                                data() {
                                    return {
                                        password: null,
                                        data: data,
                                        white_ips: data.split('\n'),
                                    }
                                },
                                methods: {
                                    limit: function (field) {
                                        let rows = this.data.split('\n');
                                        if (rows.length <= 4) {
                                            field.target.style = 'height:138px';
                                        } else {
                                            field.target.style = 'height:' + ((rows.length - 4) * 26 + 138) + 'px'
                                        }

                                        if (field.inputType === "insertFromPaste") {
                                            for (let i = 0; i < rows.length; i++) {
                                                if (rows[i].length > 45) {
                                                    rows[i] = rows[i].slice(0, 45);
                                                }
                                            }

                                            this.data = rows.join('\n');
                                        }

                                        if (field.inputType === "insertText") {
                                            for (let i = 0; i < rows.length; i++) {
                                                if (rows[i].length > 45) {
                                                    this.data = this.white_ips.join('\n');
                                                    break;
                                                }
                                            }
                                        }

                                        this.white_ips = this.data.split('\n');
                                    }
                                }
                            }).mount('.swal2-content');
                        },
                        preConfirm: function () {
                            form.data = undefined;
                            axios.put(route('profile.white-list.fill'), form.$data)
                                .then(function (response) {
                                    if (form.white_ips.join('').length === 0) {
                                        btn.parentElement.previousElementSibling.innerHTML = Lang.get('ui.no_limit');
                                    } else {
                                        btn.parentElement.previousElementSibling.innerHTML = (form.white_ips.join('<br>') + '<br>').replaceAll('<br><br>', '<br>');
                                    }

                                    fireSuccessBox(response);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        },
                    }));
                }
            }
        }).mount('#main');
    </script>
@endpush