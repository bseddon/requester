<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP\Exception;

/**
 * Exception thrown when the response is signed but the OCSP signature cannot be verified.
 */
class VerificationException extends Exception
{
}
