<?php

namespace App\Exceptions;

use Throwable;
use Inertia\Inertia;
use App\Exceptions\KompenzacijeModalException;
use App\Exceptions\KompenzacijeToastException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Throwable  $e
     * @return \Throwable
     */
    public function render($request, Throwable $e)
    {

        if ($e instanceof KompenzacijeModalException) {
            $meta = $e->getMeta() ?? []; 
            return back()->with([
                'modal' => array_merge([
                    'title' => __(isset($meta['status']) ? "modals.common.{$meta['status']}" : 'modals.common.error'),
                    'content' => $e->getMessage(),
                    'status' => $meta['status'] ?? 'error',
                    'actions' => [
                        [
                            'action' => 'close',
                            'text' => __('modals.common.confirm')
                        ]
                    ]
                ], $meta)
            ]);
        }

        if ($e instanceof KompenzacijeToastException) {
            return back()->with([
                'toast' => [
                    'text' => $e->getMessage(),
                    'type' => 'error'
                ]
            ]);
        }

        if (!app()->environment(['staging', 'local']) && in_array($e->getCode(), [500, 503, 404, 403])) {
            return Inertia::render('Dashboard', ['status' => $e->getCode()])
                ->toResponse($request)
                ->setStatusCode($e->getCode());
        } else if ($e->getCode() === 419) {
            return back()->with([
                'modal' => [
                    'title' => __('modals.page_expired.title'),
                    'content' => __('modals.page_expired.body'),
                    'status' => 'error',
                    'actions' => [[
                        'action' => [
                            'type' => 'redirect',
                            'url' => route('home')
                        ],
                        'text' => __('modals.common.confirm')
                    ]]
                ]
            ]);
        }

        return parent::render($request, $e);
    }
}
