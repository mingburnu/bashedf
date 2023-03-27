@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">{{__('ui.account_settings')}}</h2>

        <table class="table table-bordered w-50">
            <thead>
            <tr>
                <th class="align-top">{{__('ui.google_authenticator')}}</th>
                <td>
                    @if (!is_null(Auth::user()->google2fa_secret))
                        <button class="btn btn-success" @click="showQRCode"><i class="fas fa-eye"></i></button>
                    @else
                        <form method="post" action="{{route('admin.profile.google2fa-secret.bind')}}">
                            @csrf
                            @method('put')
                            <button class="btn btn-warning" @click.prevent="bindSecretKey">
                                {{__('ui.use_google_authenticator')}}
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="align-top">{{__('validation.attributes.password')}}</th>
                <td>
                    <button class="btn btn-primary" @click="resetPassword" title="edit">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
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
                bindSecretKey: function (event) {
                    Swal.fire(composePopupBoxSchema({
                        title: event.target.innerHTML,
                        type: 'warning',
                    })).then((result) => {
                        if (result.value) {
                            event.target.parentElement.submit();
                        }
                    });
                },
                showQRCode: function () {
                    Swal.fire(composePopupBoxSchema({
                        title: Lang.get('message.please_enter', {value: Lang.get('validation.attributes.password')}),
                        input: 'password',
                        preConfirm: function (password) {
                            axios.post(route('admin.profile.qr-code.generate'), {password: password})
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
                            axios.post(route('admin.password.update'), form.$data)
                                .then(function (response) {
                                    fireSuccessBox(response);
                                })
                                .catch(function (error) {
                                    fireErrorBox(error);
                                });
                        }
                    }));
                }
            }
        }).mount('#main');
    </script>
@endpush