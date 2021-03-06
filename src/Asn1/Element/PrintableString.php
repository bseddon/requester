<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\Asn1\Element;

use \lyquidity\Asn1\Element;
use \lyquidity\Asn1\Encoder;
use \lyquidity\Asn1\TaggableElement;
use \lyquidity\Asn1\UniversalTagID;
use \lyquidity\Asn1\Exception\InvalidAsn1Value;

/**
 * ASN.1 element: PrintableString.
 */
class PrintableString extends TaggableElement
{
    /**
     * The value of the element.
     *
     * @var string
     */
    private $value;

    /**
     * Create a new instance.
     *
     * @param string $value the value of the element
     *
     * @return static
     */
    public static function create($value)
    {
        $result = new static();

        return $result->setValue($value);
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
        return UniversalTagID::PRINTABLESTRING;
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
     * Get the value of the element.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Update the value of the element.
     *
     * @param string $value
     *
     * @throws \lyquidity\Asn1\Exception\InvalidAsn1Value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $value = (string) $value;
        if (!preg_match('/^[A-Za-z0-9 \'()+,\-.\/:=?]*$/', $value)) {
            throw InvalidAsn1Value::create('Invalid ASN.1 PrintableString value');
        }
        $this->value = (string) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\Element::getEncodedValue()
     */
    public function getEncodedValue(Encoder $encoder)
    {
        return $encoder->encodePrintableString($this->getValue());
    }
}
