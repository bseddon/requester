<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\Asn1\Element;

use DateTimeImmutable;
use lyquidity\Asn1\Element;
use lyquidity\Asn1\Encoder;
use lyquidity\Asn1\TaggableElement;
use lyquidity\Asn1\UniversalTagID;

/**
 * ASN.1 element: INTEGER.
 */
class Enumerated extends TaggableElement
{
    /**
     * @var int[]
     */
    private $value;

    /**
     * Create a new instance.
     *
     * @param bool $value
     *
     * @return static
     */
    public static function create( $value )
    {
        $result = new static();

        return $result->setValue( $value );
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
        return UniversalTagID::ENUMERATED;
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
     * @return int[]
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int[] $value
     *
     * @return $this
     */
    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::getEncodedValue()
     */
    public function getEncodedValue(Encoder $encoder)
    {
        return $encoder->encodeEnumerated( $this->getValue() );
    }
}
