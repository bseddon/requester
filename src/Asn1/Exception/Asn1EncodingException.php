<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\Asn1\Exception;

/**
 * Exception thrown when encoding from ASN.1 (probably because of wrong parameters).
 */
class Asn1EncodingException extends Asn1Exception
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
