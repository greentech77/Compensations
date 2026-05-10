<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    /*
     * NOTE: Laravel's stock fallback uses `realpath(...)` which returns `false`
     * when the directory does not exist OR when the running PHP process has a
     * stale realpath cache (common with mod_php/php-fpm workers that started
     * before storage permissions were fixed). A `false` value makes the Blade
     * compiler bail with "Please provide a valid cache path." We therefore
     * coalesce to the unresolved `storage_path()` so the compiler always
     * receives a non-empty string. Blade itself will create/touch the file.
     */
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views')) ?: storage_path('framework/views')
    ),

];
