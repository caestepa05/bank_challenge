<?php

namespace App\Exceptions;

use Illuminate\Http\Request;

trait RestTrait
{

    /**
     * Determines if request belong to api.
     *
     * If the request URI contains '/api/'.
     *
     * @param Request $request
     * @return bool
     */
    protected function isApiCall(Request $request)
    {
        return strpos($request->getUri(), '/api') !== false;
    }
}