<?php

namespace App\Exceptions;

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
    public function report(Throwable $exception)
    {
        if ($exception instanceof QueryException) {
            return redirect()->route('register.form')->withErrors([['error' => 'contact with admin, error 500']])->withInput();
            Log::error('Error en la base de datos: ' . $exception->getMessage());

        }
        if ($exception instanceof PDOException) {
            return redirect()->route('register.form')->withErrors([['error' => 'contact with admin, error 501']])->withInput();
            Log::error('Error en la base de datos: ' . $exception->getMessage());
        }
        if ($exception instanceof \Exception) {
            return redirect()->route('register.form')->withErrors([['error' => 'contact with admin, error 502']])->withInput();
            Log::error('Error en la base de datos: ' . $exception->getMessage());
        }
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return redirect()->route('register.form')->withErrors([['error' => 'contact with admin, error 503']])->withInput();
            Log::error('Error en la base de datos: ' . $exception->getMessage());
        }
        

        parent::report($exception);
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
