<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\Asn1\Element;

use lyquidity\Asn1\Element;
use lyquidity\Asn1\Encoder;
use lyquidity\Asn1\TaggableElement;
use lyquidity\Asn1\UniversalTagID;
use Ocsp\Exception\InvalidAsn1Value;

/**
 * ASN.1 element: OBJECT IDENTIFIER.
 */
class ObjectIdentifier extends TaggableElement
{
    /**
     * The string representation of the identifier.
     *
     * @var string
     */
    private $identifier;

    /**
     * Create a new instance.
     *
     * @param string $identifier the string representation of the identifier
     *
     * @return static
     */
    public static function create($identifier)
    {
        $result = new static();

        return $result->setIdentifier($identifier);
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
        return UniversalTagID::OBJECT_IDENTIFIER;
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
     * Get the string representation of the identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Update the string representation of the identifier.
     *
     * @param string $value
     *
     * @throws \Ocsp\Exception\InvalidAsn1Value
     *
     * @return $this
     */
    public function setIdentifier($value)
    {
        $value = (string) $value;
        if (!preg_match('/^\d+\.\d+(\.\d+)*$/', $value)) {
            throw InvalidAsn1Value::create('Invalid object identifier');
        }
        $this->identifier = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::getEncodedValue()
     */
    public function getEncodedValue(Encoder $encoder)
    {
        return $encoder->encodeIdentifier($this->getIdentifier());
    }
}
