<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP;

/**
 * List of requests to be sent to the OCSP Responder url.
 */
class RequestList
{
    /**
     * @var \lyquidity\OCSP\Request[]
     */
    private $requests = [];

    /**
     * Create a new instance.
     *
     * @param \lyquidity\OCSP\Request[] $requests
     *
     * @return static
     */
    public static function create(array $requests = [])
    {
        $result = new static();

        return $result->addRequests($requests);
    }

    /**
     * Add a new request to this list.
     *
     * @param \lyquidity\OCSP\Request $request
     *
     * @return $this
     */
    public function addRequest(Request $request)
    {
        $this->requests[] = $request;

        return $this;
    }

    /**
     * Add new requests to this list.
     *
     * @param \lyquidity\OCSP\Request[] $requests
     *
     * @return $this
     */
    public function addRequests(array $requests)
    {
        foreach ($requests as $request) {
            $this->addRequest($request);
        }

        return $this;
    }

    /**
     * Get the request list.
     *
     * @return \lyquidity\OCSP\Request[]
     */
    public function getRequests()
    {
        return $this->requests;
    }
}
