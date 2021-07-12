<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP\Exception;

/**
 * Exception thrown when the response from the OCSP is not "succesful".
 */
abstract class ResponseException extends Exception
{
}
