<?php

namespace App\Exceptions;

use Exception;
use Log;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response as IlluminateResponse;

trait RestExceptionHandlerTrait
{

    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getResponseForException(Request $request, Exception $e)
    {
        $code = $e->getStatusCode();

        //$tokenReset = $e->getResetToken();
        //if (!is_null($tokenReset)) {
        //    $email  = $e->getResetEmail();
        //    $retval = $this->exceptionErrorPasswordExpired($email, $tokenReset, $e->getMessageToShow(), $e->getErrors(), IlluminateResponse::HTTP_UNAUTHORIZED);
        //    return $retval;
        //}

        switch ($code) {
            case IlluminateResponse::HTTP_NOT_FOUND:
                $retval = $this->exceptionError($e->getMessageToShow(), $e->getErrors(), IlluminateResponse::HTTP_NOT_FOUND);
                break;
            case IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY:
                $retval = $this->exceptionError($e->getMessageToShow(), $e->getErrors(), IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY);
                break;
            case IlluminateResponse::HTTP_BAD_REQUEST:
                $retval = $this->exceptionError($e->getMessageToShow(), $e->getErrors(), IlluminateResponse::HTTP_BAD_REQUEST);
                break;
            case IlluminateResponse::HTTP_UNAUTHORIZED:
                $retval = $this->exceptionError($e->getMessageToShow(), $e->getErrors(), IlluminateResponse::HTTP_UNAUTHORIZED);
                break;
            default:
                $retval = $this->exceptionError($e->getMessageToShow(), $e->getErrors(), IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return $retval;
    }


    public function render($request, Exception $e)
    {
        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        } /*elseif($e instanceof GoShowException) {
            return $this->getResponseForException($request, $e);
        }*/

        return $this->prepareResponse($request, $e);
    }


    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param array $errors
     * @param integer $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function exceptionError($message = "Something was wrong", $errors = [], $statusCode = IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->jsonResponse([
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

      /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param array $errors
     * @param integer $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function exceptionErrorPasswordExpired($email, $token, $message = "Something was wrong", $errors = [], $statusCode = IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->jsonResponse([
            'message' => $message,
            'errors' => $errors,
            'token' => $token,
            'email' => $email,
        ], $statusCode);
    }


       /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload = null, $statusCode = 404)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }
}