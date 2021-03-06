<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\Asn1;

use lyquidity\Asn1\Element\BitString;
use lyquidity\Asn1\Element\Boolean;
use lyquidity\Asn1\Element\Enumerated;
use lyquidity\Asn1\Element\GeneralizedTime;
use lyquidity\Asn1\Element\Integer;
use lyquidity\Asn1\Element\ObjectIdentifier;
use lyquidity\Asn1\Element\OctetString;
use lyquidity\Asn1\Element\PrintableString;
use lyquidity\Asn1\Element\RawConstructed;
use lyquidity\Asn1\Element\RawPrimitive;
use lyquidity\Asn1\Element\Sequence;
use lyquidity\Asn1\Element\Set;
use lyquidity\Asn1\Element\UTCTime;
use lyquidity\Asn1\Element\UTF8String;

/**
 * Returns type of Sequence if Sequence or null
 *
 * @param Element $element
 * @return Sequence
 */
function asSequence( $element )
{
    return  $element instanceof Sequence
        ? $element 
        : null;
};

/**
 * Returns type of Set if Sequence or null
 *
 * @param Element $element
 * @return Set
 */
function asSet( $element )
{
    return  $element instanceof Set
        ? $element 
        : null;
};

/**
 * Returns type of ObjectIdentifier if ObjectIdentifier or null
 *
 * @param Element $element
 * @return ObjectIdentifier
 */
function asObjectIdentifier( $element )
{
    return  $element instanceof ObjectIdentifier
        ? $element 
        : null;
};

/**
 * Returns type of OctetString if OctetString or null
 *
 * @param Element $element
 * @return OctetString
 */
function asOctetString( $element )
{
    return $element instanceof OctetString
        ? $element 
        : null;
};

/**
 * Returns type of Integer if Integer or null
 *
 * @param Element $element
 * @return Integer
 */
function asInteger( $element )
{
    return $element instanceof Integer
        ? $element
        : null;
};

/**
 * Returns type of Boolean if Boolean or null
 *
 * @param Element $element
 * @return Boolean
 */
function asBoolean( $element )
{
    return $element  instanceof Boolean
        ? $element 
        : null;
};

/**
 * Returns type of RawPrimitive if RawPrimitive or null
 *
 * @param Element $element
 * @return RawPrimitive
 */
function asRawPrimitive( $element )
{
    return $element instanceof RawPrimitive
        ?  $element 
        : null;
};

/**
 * Returns type of GeneralizedTime if GeneralizedTime or null
 *
 * @param Element $element
 * @return GeneralizedTime
 */
function asGeneralizedTime( $element )
{
    return $element instanceof GeneralizedTime
        ? $element 
        : null;
};

/**
 * Returns type of BitString if BitString or null
 *
 * @param Element $element
 * @return BitString
 */
function asBitString( $element )
{
    return $element instanceof BitString
        ? $element 
        : null;
};

/**
 * Returns type of UTCTime if UTCTime or null
 *
 * @param Element $element
 * @return UTCTime
 */
function asUTCTime( $element )
{
    return $element instanceof UTCTime
        ? $element 
        : null;
};

/**
 * Returns type of UTF8String if UTF8String or null
 *
 * @param Element $element
 * @return UTF8String
 */
function asUTF8String( $element )
{
    return $element instanceof UTF8String
        ? $element 
        : null;
};

/**
 * Returns type of Enumerated if Enumerated or null
 *
 * @param Element $element
 * @return Enumerated
 */
function asEnumerated( $element )
{
    return $element instanceof Enumerated
        ? $element 
        : null;
};

/**
 * Returns type of RawConstructed if RawConstructed or null
 *
 * @param Element $element
 * @return RawConstructed
 */
function asRawConstructed( $element )
{
    return $element instanceof RawConstructed
        ? $element 
        : null;
};

/**
 * Returns type of PrintableString if PrintableString or null
 *
 * @param Element $element
 * @return PrintableString
 */
function asPrintableString( $element )
{
    return $element instanceof PrintableString
        ? $element 
        : null;
};

/**
 * Interface that any ASN.1 element that can be tagged must implement.
 */
abstract class TaggableElement implements Element
{
    /**
     * The applied tag (if any).
     *
     * @var \lyquidity\Asn1\Tag
     */
    private $tag;

    /**
     * Return a Sequence or null
     *
     * @return Sequence
     */
    public function asSequence()
    {
        return asSequence( $this );
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\TaggableElement::getTag()
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lyquidity\Asn1\TaggableElement::setTag()
     */
    public function setTag(Tag $value = null)
    {
        $this->tag = $value;

        return $this;
    }

}
