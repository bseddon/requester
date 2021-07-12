<?php

/**
 * Taken from https://github.com/sop/asn1 MIT License Copyright (c) 2016-2021 Joni Eskelinen
 */

namespace lyquidity\Asn1\Element;

use lyquidity\Asn1\UniversalTagID;
use lyquidity\Asn1\PrimitiveString;

/**
 * Implements *UTF8String* type.
 *
 * *UTF8String* is an Unicode string with UTF-8 encoding.
 */
class UTF8String extends PrimitiveString
{
	    /**
     * Create a new instance.
     *
     * @param string $value
     *
     * @return static
     */
    public static function create( $value )
    {
        return new static( $value );
    }

	public function getTypeID()
	{
		return UniversalTagID::UTF8STRING;
	}

    /**
     * {@inheritdoc}
     */
    protected function validateString( $string )
    {
        return mb_check_encoding( $string, 'UTF-8');
    }
}