<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP;

/**
 * List of responses received from the OCSP Responder.
 */
class ResponseList
{
    /**
     * @var \OCSP\Response[]
     */
    private $responses = [];

    /**
     * Create a new instance.
     *
     * @param \OCSP\Response[] $responses
     *
     * @return static
     */
    public static function create(array $responses = [])
    {
        $result = new static();

        return $result->addResponses($responses);
    }

    /**
     * Add a new response to this list.
     *
     * @param \OCSP\Response $response
     *
     * @return $this
     */
    public function addResponse(Response $response)
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * Add new responses to this list.
     *
     * @param Response[] $responses
     *
     * @return $this
     */
    public function addResponses(array $responses)
    {
        foreach ($responses as $response) {
            $this->addResponse($response);
        }

        return $this;
    }

    /**
     * Get the response list.
     *
     * @return lyquidity\OCSP\Response[]
     */
    public function getResponses()
    {
        return $this->responses;
    }
}
