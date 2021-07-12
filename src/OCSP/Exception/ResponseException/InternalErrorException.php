<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP\Exception\ResponseException;

use lyquidity\OCSP\Exception\ResponseException;

/**
 * Exception thrown when the response from the OCSP is "internalError".
 */
class InternalErrorException extends ResponseException
{
    /**
     * Create a new instance.
     *
     * @return static
     */
    public static function create()
    {
        return new static('Internal error in issuer');
    }
}
