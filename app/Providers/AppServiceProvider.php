<?php

namespace App\Providers;

use App\Entities\AccountOperation;
use App\Entities\Admin;
use App\Entities\Fund;
use App\Entities\Payment;
use App\Entities\Deposit;
use App\Entities\User;
use App\Observers\AdminObserver;
use App\Observers\PaymentObserver;
use App\Observers\DepositObserver;
use App\Observers\UserObserver;
use App\Rules\BankCardRule;
use App\Rules\DecimalBetweenRule;
use App\Rules\DecimalGtRule;
use App\Rules\DecimalLtRule;
use App\Rules\DecimalMaxRule;
use App\Rules\DecimalMinRule;
use App\Rules\ExcelRule;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Form;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // decimal_min:value
        Validator::extend('decimal_min', function ($attribute, $value, $parameters) {
            $min = BigDecimal::of($parameters[0]);
            $roundingMode = empty($parameters[1]) ? RoundingMode::UNNECESSARY : $parameters[1];
            $scale = empty($parameters[2]) ? 0 : $parameters[2];

            return Validator::make([$attribute => $value], [$attribute => [new DecimalMinRule($min, $roundingMode, $scale)]])->passes();
        });

        Validator::replacer('decimal_min', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':min'], $parameters, $message);
        });

        // decimal_max:value
        Validator::extend('decimal_max', function ($attribute, $value, $parameters) {
            $max = BigDecimal::of($parameters[0]);
            $roundingMode = empty($parameters[1]) ? RoundingMode::UNNECESSARY : $parameters[1];
            $scale = empty($parameters[2]) ? 0 : $parameters[2];

            return Validator::make([$attribute => $value], [$attribute => [new DecimalMaxRule($max, $roundingMode, $scale)]])->passes();
        });

        Validator::replacer('decimal_max', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':max'], $parameters, $message);
        });

        // decimal_between:min,max
        Validator::extend('decimal_between', function ($attribute, $value, $parameters) {
            $min = BigDecimal::of($parameters[0]);
            $max = BigDecimal::of($parameters[1]);
            $roundingMode = empty($parameters[2]) ? RoundingMode::UNNECESSARY : $parameters[2];
            $scale = empty($parameters[3]) ? 0 : $parameters[3];

            return Validator::make([$attribute => $value], [$attribute => [new DecimalBetweenRule($min, $max, $roundingMode, $scale)]])->passes();
        });

        Validator::replacer('decimal_between', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':min', ':max'], $parameters, $message);
        });

        // decimal_lt:value
        Validator::extend('decimal_lt', function ($attribute, $value, $parameters) {
            $compared_number = BigDecimal::of($parameters[0]);
            $roundingMode = empty($parameters[1]) ? RoundingMode::UNNECESSARY : $parameters[1];
            $scale = empty($parameters[2]) ? 0 : $parameters[2];

            return Validator::make([$attribute => $value], [$attribute => [new DecimalLtRule($compared_number, $roundingMode, $scale)]])->passes();
        });

        Validator::replacer('decimal_lt', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':value'], $parameters, $message);
        });

        // decimal_gt:value
        Validator::extend('decimal_gt', function ($attribute, $value, $parameters) {
            $compared_number = BigDecimal::of($parameters[0]);
            $roundingMode = empty($parameters[1]) ? RoundingMode::UNNECESSARY : $parameters[1];
            $scale = empty($parameters[2]) ? 0 : $parameters[2];

            return Validator::make([$attribute => $value], [$attribute => [new DecimalGtRule($compared_number, $roundingMode, $scale)]])->passes();
        });

        Validator::replacer('decimal_gt', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':value'], $parameters, $message);
        });

        // excel
        Validator::extend('excel', function ($attribute, $value, $parameters) {
            return Validator::make([$attribute => $value], [$attribute => [new ExcelRule()]])->passes();
        });

        Validator::replacer('excel', function ($message, $attribute, $rule, $parameters) {
            return $message;
        });

        // BankCard
        Validator::extend('bank_card', function ($attribute, $value, $parameters) {
            return Validator::make([$attribute => $value], [$attribute => [new BankCardRule()]])->passes();
        });

        Validator::replacer('bank_card', function ($message, $attribute, $rule, $parameters) {
            return $message;
        });

        Payment::observe(PaymentObserver::class);
        Deposit::observe(DepositObserver::class);
        Admin::observe(AdminObserver::class);
        User::observe(UserObserver::class);

        Form::macro('rowInput', function ($name, $type, $label, $errors, $value = null, array $attributes = []) {
            $input = match ($type) {
                'password' => Form::password($name, ['id' => $name, 'class' => 'form-control' . ($errors->has($name) ? ' border-danger' : '')] + $attributes),
                'email' => Form::email($name, $value, ['id' => $name, 'class' => 'form-control' . ($errors->has($name) ? ' border-danger' : '')] + $attributes),
                'text' => Form::text($name, $value, ['id' => $name, 'class' => 'form-control' . ($errors->has($name) ? ' border-danger' : '')] + $attributes),
                'number' => Form::number($name, $value, ['id' => $name, 'class' => 'form-control' . ($errors->has($name) ? ' border-danger' : '')] + $attributes),
                'url' => Form::url($name, $value, ['id' => $name, 'class' => 'form-control' . ($errors->has($name) ? ' border-danger' : '')] + $attributes),
                'textarea' => Form::textarea($name, $value, ['id' => $name, 'class' => 'form-control' . ($errors->has($name) ? ' border-danger' : '')] + $attributes),
                default => '',
            };

            return
                '<div class="form-group row">' . Form::label($name, $label, ['class' => 'col-sm-2 col-form-label']) .
                '<div class="col-sm-10">' . $input .
                '<span class="invalid-feedback d-block" role="alert"><strong>' . $errors->first($name) .
                '</strong></span></div></div>';
        });

        Form::macro('rowRadio', function ($name, $label, array $options, $errors, array $values, $checked_value = null, array $attributes = []) {
            $input = '';
            foreach ($values as $i => $value) {
                $checked = $value == $checked_value;
                $id = $name . $value;
                $radio = Form::radio($name, $value, $checked, ['id' => $id, 'class' => ($errors->has($name) ? ' border-danger' : '')] + $attributes);
                $EOL = PHP_EOL;
                $input = $input . "$EOL<label for='$id'>$options[$i]</label>$EOL$radio$EOL";
            }

            return
                '<div class="form-group row">' . Form::label($name, $label, ['class' => 'col-sm-2 col-form-label']) .
                '<div class="col-sm-10">' . $input .
                '<span class="invalid-feedback d-block" role="alert"><strong>' . $errors->first($name) .
                '</strong></span></div></div>';
        });

        Form::macro('rowSubmit', function ($value) {
            return '<div class="row justify-content-end"><div class="col-auto">' .
                Form::submit($value, ['class' => 'btn btn-secondary']) .
                '</div></div>';
        });

        Relation::morphMap([
            'Deposit' => Deposit::class,
            'Payment' => Payment::class,
            'AccountOperation' => AccountOperation::class,
            'Admin' => Admin::class,
            'User' => User::class,
            'Fund' => Fund::class
        ]);
    }
}
