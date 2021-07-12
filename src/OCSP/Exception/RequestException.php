<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP\Exception;

/**
 * Exception thrown when the revocation request body can't be built because there's some missing data in the certificate or in the issuer certificate.
 */
class RequestException extends Exception
{
    /**
     * Create a new instance.
     *
     * @param string $message
     *
     * @return static
     */
    public static function create($message)
    {
        return new static($message);
    }
}
