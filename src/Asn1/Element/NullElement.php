<?php

namespace lyquidity\Asn1\Element;

use DateTimeImmutable;
use lyquidity\Asn1\Element;
use lyquidity\Asn1\Encoder;
use lyquidity\Asn1\TaggableElement;
use lyquidity\Asn1\UniversalTagID;

/**
 * ASN.1 element: NULL.
 */
class NullElement extends TaggableElement
{
    /**
     * Create a new instance.
     *
     * @return static
     */
    public static function create()
    {
        $result = new static();
        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::getClass()
     */
    public function getClass()
    {
        return Element::CLASS_UNIVERSAL;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::getTypeID()
     */
    public function getTypeID()
    {
        return UniversalTagID::NULL;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::isConstructed()
     */
    public function isConstructed()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::getEncodedValue()
     */
    public function getEncodedValue(Encoder $encoder)
    {
        return $encoder->encodeNull();
    }
}
