<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // DB::listen(function($query) {
        //     file_put_contents("/tmp/queries.log", $query->sql . "\n", FILE_APPEND);
        // });
        Validator::extend('max_chars_field', function($attribuate, $value, $parameters, $validator) {
            $max_field = $parameters[0];
            $data = $validator->getData();
            $max_value = $data[$max_field];
            return strlen($value) <= $max_value;
        });
        Validator::replacer('max_chars_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });
        Validator::extend('max_dup_chars_field', function($attribuate, $value, $parameters, $validator) {
            $dup_field = $parameters[0];
            $data = $validator->getData();
            $max_value = $data[$dup_field];
            $chars = count_chars($value, 1);
            return max($chars) <= $max_value;
        });
        Validator::replacer('max_dup_chars_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });
        Validator::extend('max_dup_chars', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $chars = count_chars($value, 1);
            return max($chars) <= $parameters[0];
        });
        Validator::replacer('max_dup_chars', function($message, $attribute, $rule, $parameters) {
            return str_replace(':value', $parameters[0], $message);
        });
        Schema::defaultStringLength(191);
        if (env('APP_ENV') !== 'local') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
