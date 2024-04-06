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
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
    public function re(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $e)
    {   
          if ($e instanceof PDOException || $e instanceof QueryException) {
                return response()->view('login', ['error' => 'There was an error with the server, please try again or later']);

        }
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->back()->withErrors(['message' => 'Uppps! Something went wrong, please try again']);
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
