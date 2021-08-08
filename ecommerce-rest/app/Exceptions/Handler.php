<?php

namespace App\Exceptions;


use Illuminate\Auth\Access\AuthorizedException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseHelper;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */

    const FOREIGN_KEY_VIOLATION_CODE = 1451;
    const DB_CONNECTION_FAILED = 2002;

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

    public function render($request, Throwable $e)
    {
        if($e instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($e, $request);

        } else if($e instanceof ModelNotFoundException){
            $className = class_basename($e->getModel());
            return $this->errorResponse("$className does not exist with the specified key", 404);

        } else if($e instanceof AuthenticationException){
            return $this->unauthenticated($request, $e);

        } else if($e instanceof AuthorizationException){
            return $this->errorResponse("The specified URL cannot be found", 404);

        } else if ($e instanceof MethodNotAllowedException){
            return $this->errorResponse("The specified method for the request is invalid", 405);

        } else if($e instanceof HttpException){
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());

        } else if($e instanceof QueryException){
            $sqlErrorCode = $e->errorInfo[1];
            if($sqlErrorCode == self::FOREIGN_KEY_VIOLATION_CODE){
                return $this->errorResponse("Cannot remove this resource permanently, as it is referred by some other resource", 409);
            } else if($sqlErrorCode == self::DB_CONNECTION_FAILED){
                return $this->errorResponse("Our database server is down! Please try after sometime", 500);
            }
        }

        if(config('app.debug')){
            return parent::render($request, $e);
        }
        return $this->errorResponse("Unexpected server error", 500);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        return $this->reportMultipleErrors($e->errors(), 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse("Unauthenticated", 401);
    }
}
