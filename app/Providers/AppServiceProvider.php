<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Exceptions\KompenzacijeException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('api', function ($value = null) {
            if ($value === null) {
                return response('', 204);
            } else {
                if ($value instanceof KompenzacijeException) {
                    return response([
                        'status' => 'error',
                        'reason' => $value->getMessage()
                    ]);
                } else {
                    return response([
                        'status' => 'success',
                        'data' => $value
                    ]);
                }
            }
        });

        Response::macro('toastSuccess', function () {
            return redirect()->back()->with([
                'toast' => [
                    'text' => 'Operacija uspešna!',
                    'type' => 'success'
                ]
            ]);
        });

        
        /*Collection::macro('snakeCaseKeys', function () {
            return $this->mapWithKeys(function($value, $key) {
                return [
                    Str::snake($key) => $value
                ];
            });
        });*/

        Arr::macro('camelCaseKeys', function ($array) {
            foreach ($array as $key => $value) {
                if (Str::camel($key) != $key && Str::upper($key) != $key) {
                    $array[Str::camel($key)] = $value;
                    unset($array[$key]);
                }
            }
            return $array;
        });

        Arr::macro('snakeCaseKeys', function ($array) {
            foreach ($array as $key => $value) {
                if (Str::snake($key) != $key && Str::upper($key) != $key) {
                    $array[Str::snake($key)] = $value;
                    unset($array[$key]);
                }
            }
            return $array;
        });
    }
}
