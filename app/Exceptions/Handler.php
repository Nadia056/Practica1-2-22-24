<?php

namespace App\Exceptions;

use ErrorException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PDOException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    public function render($request, Throwable $e)
    {
        if ($e instanceof QueryException) {
            Log::error('Error en la base de datos: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
        if ($e instanceof PDOException) {
            Log::error('Error en la base de datos: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
        if ($e instanceof ValidationException) {
            Log::error('Error de validación: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
        return parent::render($request, $e);
    
    }
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
