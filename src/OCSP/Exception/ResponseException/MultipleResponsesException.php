<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP\Exception\ResponseException;

use lyquidity\OCSP\Exception\ResponseException;

/**
 * Exception thrown when we expect just one response from the OCSP Responder, but we received more that one.
 */
class MultipleResponsesException extends ResponseException
{
    /**
     * Create a new instance.
     *
     * @return static
     */
    public static function create()
    {
        return new static('Multiple OCSP responses received');
    }
}
