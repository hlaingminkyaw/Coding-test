<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Custom JSON exception rendering for API requests.
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            $status = 500;
            $message = $e->getMessage();

            // Only HttpExceptions have a proper status code
            if ($e instanceof HttpException) {
                $status = $e->getStatusCode();
            }

            return response()->json([
                'message' => $message
            ], $status);
        }

        return parent::render($request, $e);
    }
}
