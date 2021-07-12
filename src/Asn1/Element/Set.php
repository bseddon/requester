<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\Asn1\Element;

use lyquidity\Asn1\UniversalTagID;

/**
 * ASN.1 element: SET / SET OF.
 */
class Set extends AbstractList
{
    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::getTypeID()
     */
    public function getTypeID()
    {
        return UniversalTagID::SET;
    }

    /**
     * Create a new instance.
     *
     * @param \lyquidity\Asn1\Element[] $elements
     *
     * @return static
     */
    public static function create(array $elements = [])
    {
        $result = new static();

        return $result->addElements($elements);
    }
}
