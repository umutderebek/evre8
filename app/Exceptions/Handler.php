<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //model yada controller bulamazsak bu şekilde döndürülebilinir.

        if($exception instanceof NotFoundHttpException)
        {
            return response()->view('errors.404',compact('exception'));
        }

        if($exception instanceof ModelNotFoundException)
        {
            return response()->view('errors.404',compact('exception'));
        }
        return parent::render($request, $exception);
    }




    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $guard = array_get($exception->guards(),0);

        //using switch statement to switch between the guards
        switch ($guard) {
            case 'admin':
                $login = 'admin.login';
                break;
            case 'psikolog':
                $login = 'psikolog.login';
                break;
            case 'danisman':
                $login = 'danisman.login';
                break;
            case 'danisan':
                $login = 'danisan.login';
                break;
            default:
                $login = 'login';
                break;
        }
        return redirect()->guest(route($login));
    }
}
