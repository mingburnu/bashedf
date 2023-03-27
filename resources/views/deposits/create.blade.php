@extends('layouts.app')
@push('css')
    <style>
        .card:hover {
            border-color: #007bff;
            cursor: pointer;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid p-5 shadow-sm bg-white">
        <div class="container-fluid">
            <div class="row mb-4 pr-3">
                <div class="col-auto mr-auto">
                    <h2 class="font-weight-bold">{{__('ui.deposit')}}</h2>
                </div>
            </div>
            <div>
                @include('alerts.success')
                <form method="POST" action="{{ route('deposits.store') }}">
                    @csrf
                    {!! Form::rowInput('amount', 'number', __('validation.attributes.amount'), $errors, null, ['min' => Auth::user()->contract->min_deposit_amount, 'max' => Auth::user()->contract->max_deposit_amount, 'step' => '0.01', 'required' => true]) !!}
                    <div class="form-group row">
                        <label for="depositFeePercent"
                               class="col-sm-2 col-form-label">{{__('validation.attributes.processing_fee')}}</label>
                        <div class="col-sm-10">
                            <p class="col-form-label" id="depositFeePercent"
                               data-percent="{{Auth::user()->contract->deposit_processing_fee_percent}}">
                                <span>{{Auth::user()->contract->deposit_processing_fee_percent}}</span>
                                <i class="fas fa-percent"></i>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="totalAmount"
                               class="col-sm-2 col-form-label">{{__('validation.attributes.total_amount')}}</label>
                        <div class="col-sm-10">
                            <p class="col-form-label" id="totalAmount"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{__('ui.select') . __('ui.bank_card')}}</label>
                        @if ($errors->has('bank_card_id'))
                            <span class="invalid-feedback d-block" role="alert">
                              <strong>{{ $errors->first('bank_card_id') }}</strong>
                            </span>
                        @endif
                        @if(Auth::user()->bankCards->isEmpty())
                            <span class="invalid-feedback d-block" role="alert">
                              <strong>{{__('message.please_call', ['value' => __('ui.admin') . __('ui.bind') . __('ui.bank_card')])}}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group row">
                        @foreach (Auth::user()->bankCards as $card)
                            <label class="form-check-label col-md-4 bank-label"
                                   for="bank_card_{{$card->id}}">
                                <input class="hidden" type="radio" name="bank_card_id"
                                       id="bank_card_{{$card->id}}" value="{{$card->id}}">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text">
                                            {{__('validation.attributes.bank_name')}}：{{$card->bank_name}}
                                        </p>
                                        <p class="card-text">
                                            {{__('validation.attributes.account_name')}}：{{$card->account_name}}
                                        </p>
                                        <p class="card-text">
                                            {{__('validation.attributes.account_number')}}：{{$card->account_number}}
                                        </p>
                                        <p class="card-text">
                                            {{__('validation.attributes.bank_district')}}：{{$card->bank_district}}
                                        </p>
                                        <p class="card-text">
                                            {{__('validation.attributes.bank_address')}}：{{$card->bank_address}}
                                        </p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">{{__('ui.submit')}}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('.card').click(function (e) {
            $('.card').removeClass('border-primary');
            $(this).addClass('border-primary');
        });

        $(document).on('click keyup paste', "#amount", function () {
            calculate($(this).val());
        });

        function calculate(val) {
            let amount = parseFloat(val),
                depositFeePercent = parseFloat($('#depositFeePercent').data('percent')) / 100,
                totalAmount = amount - (amount * depositFeePercent).toFixed(2);
            $('#totalAmount').html(Math.round(totalAmount * 100) / 100);
        }

        $("#amount").trigger('click');
    </script>
@endpush
