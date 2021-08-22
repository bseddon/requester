<?php

/**
 * Originally from https://github.com/mlocati/ocsp
 * Changed namespaces so there are no clashes with the orginal project
 */

namespace lyquidity\OCSP;

use lyquidity\Asn1\Der\Decoder as DerDecoder;
use lyquidity\Asn1\Der\Encoder as DerEncoder;
use lyquidity\Asn1\Element;
use lyquidity\Asn1\Element\BitString;
use lyquidity\Asn1\Element\Integer;
use lyquidity\Asn1\Element\ObjectIdentifier;
use lyquidity\Asn1\Element\OctetString;
use lyquidity\Asn1\Element\PrintableString;
use lyquidity\Asn1\Element\RawPrimitive;
use lyquidity\Asn1\Element\Sequence;
use lyquidity\Asn1\Tag;
use lyquidity\Asn1\UniversalTagID;
use lyquidity\Asn1\Util\BigInteger;
use lyquidity\OID\OID;

use function lyquidity\Asn1\asBitString;
use function lyquidity\Asn1\asObjectIdentifier;
use function lyquidity\Asn1\asSequence;
use function lyquidity\Asn1\asUTCTime;

class CertificateInfo
{
    /**
     * The decoder to be used to decode DER-encoded data.
     *
     * @var \lyquidity\Asn1\Der\Decoder
     */
    private $derDecoder;

    /**
     * The encoder to be used to encode data to DER.
     *
     * @var \lyquidity\Asn1\Der\Encoder
     */
    private $derEncoder;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this->derDecoder = new DerDecoder();
        $this->derEncoder = new DerEncoder();
    }

    /**
     * Extract the OCSP Responder URL that *may* be included in a certificate.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate the certificate (loaded with the CertificateLoader class)
     *
     * @return string empty string if not found
     */
    public function extractOcspResponderUrl(Sequence $certificate)
    {
        $authorityInfoAccess = $this->getAuthorityInfoAccessExtension($certificate);
        if ($authorityInfoAccess === null) {
            return '';
        }
        foreach ($authorityInfoAccess->getElements() as $accessDescription) {
            $accessMethod = $accessDescription instanceof Sequence ? $accessDescription->getFirstChildOfType(UniversalTagID::OBJECT_IDENTIFIER) : null;
            /** @var ObjectIdentifier $accessMethod */
            if ($accessMethod === null || $accessMethod->getIdentifier() !== '1.3.6.1.5.5.7.48.1') {
                continue;
            }
            /** @var Sequence $accessDescription */
            $accessLocation = $accessDescription->getFirstChildOfType(6, Element::CLASS_CONTEXTSPECIFIC);
            if (!$accessLocation instanceof RawPrimitive) {
                return '';
            }
            // It's a IA5String, that is US-ASCII
            return $accessLocation->getRawEncodedValue();
        }

        return '';
    }

    /**
     * Extract the URL where the issuer certificate can be retrieved from (if present).
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate the certificate (loaded with the CertificateLoader class)
     *
     * @return string empty string if not found
     */
    public function extractIssuerCertificateUrl(Sequence $certificate)
    {
        $authorityInfoAccess = $this->getAuthorityInfoAccessExtension($certificate);
        if ($authorityInfoAccess === null) {
            return '';
        }
        foreach( $authorityInfoAccess->getElements() as $accessDescription )
        {
            $accessMethod = $accessDescription instanceof Sequence ? $accessDescription->getFirstChildOfType(UniversalTagID::OBJECT_IDENTIFIER) : null;
            /** @var ObjectIdentifier $accessMethod */
            if ($accessMethod === null || $accessMethod->getIdentifier() !== \lyquidity\OCSP\OCSP::caIssuers ) {
                continue;
            }
            /** @var Sequence $accessDescription */
            $accessLocation = $accessDescription->getFirstChildOfType(6, Element::CLASS_CONTEXTSPECIFIC);
            if (!$accessLocation instanceof RawPrimitive) {
                return '';
            }
            // It's a IA5String, that is US-ASCII
            return $accessLocation->getRawEncodedValue();
        }

        return '';
    }

    /**
     * Extract the data to be sent to the OCSP Responder url from a certificate and the issuer certifiacte.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate the certificate (loaded with the CertificateLoader class)
     * @param \lyquidity\Asn1\Element\Sequence $issuerCertificate the issuer certificate (loaded with the CertificateLoader class; its URL can be retrieved with the extractOcspResponderUrl method)
     *
     * @throws \lyquidity\Asn1\Exception\RequestException when some required data is missing in the certificate/issuer certificate
     *
     * @return \lyquidity\OCSP\Request
     */
    public function extractRequestInfo(Sequence $certificate, Sequence $issuerCertificate)
    {
        return Request::create(
            $this->extractSerialNumber($certificate),
            $this->extractIssuerDer($certificate),
            $this->extractSubjectPublicKeyBytes($issuerCertificate)
        );
    }

    /**
     * Get the AuthorityInfoAccess extension included in a certificate.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     *
     * @return \lyquidity\Asn1\Element\Sequence|null
     *
     * @see https://tools.ietf.org/html/rfc2459#section-4.1 for Certificate
     * @see https://tools.ietf.org/html/rfc2459#section-4.2.2.1 for AuthorityInfoAccessSyntax
     */
    protected function getAuthorityInfoAccessExtension(Sequence $certificate)
    {
        $tbsCertificate = $certificate->getFirstChildOfType(UniversalTagID::SEQUENCE, Element::CLASS_UNIVERSAL);
        if (!$tbsCertificate instanceof Sequence) {
            return null;
        }
        $extensions = $tbsCertificate->getFirstChildOfType(3, Element::CLASS_CONTEXTSPECIFIC, Tag::ENVIRONMENT_EXPLICIT);
        if (!$extensions instanceof Sequence) {
            return null;
        }
        foreach ($extensions->getElements() as $extension) {
            if (!$extension instanceof Sequence) {
                continue;
            }
            /** @var Sequence $extension */
            $extnID = $extension->getFirstChildOfType(UniversalTagID::OBJECT_IDENTIFIER);
            /** @var ObjectIdentifier $extnID */
            if ($extnID === null || $extnID->getIdentifier() !== \lyquidity\OCSP\OCSP::authorityInfoAccess ) {
                continue;
            }
            /** @var OctetString */
            $extnValue = $extension->getFirstChildOfType(UniversalTagID::OCTET_STRING);
            if ($extnValue === null) {
                return '';
            }
            try {
                $authorityInfoAccess = $this->derDecoder->decodeElement($extnValue->getValue());
            } catch (\lyquidity\Asn1\Exception\Asn1DecodingException $foo) {
                $authorityInfoAccess = null;
            }

            return $authorityInfoAccess instanceof Sequence ? $authorityInfoAccess : null;
        }

        return null;
    }

    /**
     * Extract the serial number from a certificate as an Integer element
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     * @param bool $asString (default: false) True if the number is to be returned as a string otherwise as binary
     * @return Integer Null if not found
     *
     * @see https://tools.ietf.org/html/rfc2459#section-4.1 for Certificate
     * @see https://tools.ietf.org/html/rfc5912#section-14 for CertificateSerialNumber
     */
    public function extractSerialNumberAsInteger(Sequence $certificate, $asString = false )
    {
        /** @var Sequence */
        $tbsCertificate = $certificate->getFirstChildOfType(UniversalTagID::SEQUENCE);
        if ($tbsCertificate === null) {
            return '';
        }

        /** @var Integer */
        return $tbsCertificate->getFirstChildOfType(UniversalTagID::INTEGER);
    }

    /**
     * Extract the serial number from a certificate.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     * @param bool $asString (default: false) True if the number is to be returned as a string otherwise as binary
     * @return string Empty string if not found
     *
     * @see https://tools.ietf.org/html/rfc2459#section-4.1 for Certificate
     * @see https://tools.ietf.org/html/rfc5912#section-14 for CertificateSerialNumber
     */
    public function extractSerialNumber(Sequence $certificate, $asString = false )
    {
        /** @var Integer */
        $serialNumber = $this->extractSerialNumberAsInteger( $certificate, $asString );
        if ($serialNumber === null) {
            return '';
        }

        if ( $asString )
        {
            $value = $serialNumber->getValue();
            return $value instanceof BigInteger ? $value->__toString() : strval( $value );
        }
        else
            return $serialNumber->getEncodedValue( $this->derEncoder );
    }

    /**
     * Extract the issuer sequence.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     *
     * @return DateTime[] A pair of dates for start and end
     *
     * @see https://tools.ietf.org/html/rfc2459#section-4.1 for Certificate
     */
    public function extractDates(Sequence $certificate)
    {
        /** @var Sequence */
        $tbsCertificate = $certificate->getFirstChildOfType(UniversalTagID::SEQUENCE);
        if ($tbsCertificate === null) 
        {
            return null;
        }
        $seq = asSequence( $tbsCertificate->getNthChildOfType(3, UniversalTagID::SEQUENCE) );
        if ( ! $seq ) return null;

            $dates = array();
            $dates['start'] = asUTCTime( $seq->at(1) )->getValue();
            $dates['end']   = asUTCTime( $seq->at(2) )->getValue();

        return $dates;
    }

    /**
     * Extract the issuer sequence.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     *
     * @return Sequence Empty string if not found
     *
     * @see https://tools.ietf.org/html/rfc2459#section-4.1 for Certificate
     */
    public function extractIssuer(Sequence $certificate)
    {
        /** @var Sequence */
        $tbsCertificate = $certificate->getFirstChildOfType(UniversalTagID::SEQUENCE);
        if ($tbsCertificate === null) 
        {
            return '';
        }
        return $tbsCertificate->getNthChildOfType(2, UniversalTagID::SEQUENCE) ?? '';
    }

    /**
     * Extract the subject sequence.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     *
     * @return Sequence Empty string if not found
     *
     * @see https://tools.ietf.org/html/rfc2459#section-4.1 for Certificate
     */
    public function extractSubject(Sequence $certificate)
    {
        /** @var Sequence */
        $tbsCertificate = $certificate->getFirstChildOfType(UniversalTagID::SEQUENCE);
        if ($tbsCertificate === null) 
        {
            return '';
        }
        return $tbsCertificate->getNthChildOfType(4, UniversalTagID::SEQUENCE) ?? '';
    }

    /**
     * Extract the DER-encoded data of the issuer.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     *
     * @return string Empty string if not found
     *
     * @see https://tools.ietf.org/html/rfc2459#section-4.1 for Certificate
     */
    protected function extractIssuerDer(Sequence $certificate)
    {
        $issuer = $this->extractIssuer( $certificate );
        return $issuer ? $this->derEncoder->encodeElement( $issuer ) : '';
    }

    /**
     * Extract the bytes of the public key of the subject included in the certificate.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     *
     * @return string Empty string if not found
     */
    public function extractSubjectPublicKeyBytes(Sequence $certificate)
    {
        /** @var Sequence */
        $tbsCertificate = $certificate->getFirstChildOfType(UniversalTagID::SEQUENCE);
        if ($tbsCertificate === null) {
            return '';
        }
        /** @var Sequence */
        $subjectPublicKeyInfo = $tbsCertificate->getNthChildOfType(5, UniversalTagID::SEQUENCE);
        if ($subjectPublicKeyInfo === null) {
            return '';
        }
        /** @var BitString */
        $subjectPublicKey = $subjectPublicKeyInfo->getFirstChildOfType(UniversalTagID::BIT_STRING);
        if ($subjectPublicKey === null) {
            return '';
        }

        return $subjectPublicKey->getBytes();
    }

    /**
     * Extract the bytes of the public key of the subject included in the certificate.
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     *
     * @return string Empty string if not found
     */
    public function extractSubjectIdentifier( Sequence $certificate )
    {
        /** @var Sequence */
        $tbsCertificate = $certificate->getFirstChildOfType(UniversalTagID::SEQUENCE);
        if ($tbsCertificate === null) {
            return '';
        }
        /** @var Sequence */
        $extensionsContainer = $tbsCertificate->getFirstChildOfType( UniversalTagID::BIT_STRING, \lyquidity\Asn1\Element::CLASS_CONTEXTSPECIFIC, \lyquidity\Asn1\Tag::ENVIRONMENT_EXPLICIT );
        if ($extensionsContainer === null) {
            return '';
        }

        $subjectIdOID = OID::getOIDFromName('subjectKeyIdentifier');
        foreach( $extensionsContainer->getElements() as $extension )
        {
            /** @var \lyquidity\Asn1\Element\Sequence $extension */
            if ( ! $extension ) continue;
            if ( ! ( $oid = \lyquidity\Asn1\asObjectIdentifier( $extension->at(1) ) ) || $oid->getIdentifier() != $subjectIdOID )
                continue;

            $octet = \lyquidity\Asn1\asOctetString( $extension->getFirstChildOfType( \lyquidity\Asn1\UniversalTagID::OCTET_STRING ) );
            return $octet ? $octet->getValue() : '';
        }
    }

    /**
     * Returns the signature bytes
     *
     * @param \lyquidity\Asn1\Element\Sequence $certificate
     * @return string
     */
    public function getSignatureBytes( Sequence $certificate )
    {
        $sig = asBitString( $certificate->getFirstChildOfType( UniversalTagID::BIT_STRING ) );
        return $sig ? $sig->getBytes() : null;
    }

    /**
     * Generates a human readable DN string
     *
     * @param Sequence $certificate
     * @param boolean $useIssuer True if the string is to be generated for the issuer.  False (default) if the subject.
     * @return string
     */
    public function getDNString( Sequence $certificate, $useIssuer = false )
    {
        $generalNames = $useIssuer ? $this->extractIssuer( $certificate ) : $this->extractSubject( $certificate );
        return $this->getDNStringFromNames( $generalNames );
    }

    /**
     * Generate a names string from the sequence of generalNames
     * @param Sequence $generalNames
     * @return void
     */
    public function getDNStringFromNames( $generalNames )
    {
        $names = array(); 
        foreach( $generalNames->getElements() as $dnSet )
        {
            /** @var Set $dnSet */
            $component = asSequence( $dnSet->at(1) );
            if ( !$component ) continue;
        
            $oid = asObjectIdentifier( $component->getFirstChildOfType( \lyquidity\Asn1\UniversalTagID::OBJECT_IDENTIFIER ) );
            if ( ! $oid ) continue;

            // $componentValue = \lyquidity\Asn1\asPrintableString( $component->at(2) );
            /** @var PrintableString $componentValue */
            $componentValue = $component->at(2);
            if ( ! $componentValue ) continue;
        
            $oidNumber = $oid->getIdentifier();
            $value = $componentValue->getValue();
        
            switch( $oidNumber )
            {
                case "2.5.4.3":  // "commonName",
                    $names[] = "CN=$value";
                    break;

                case "2.5.4.4":  // "surname",
                    $names[] = "SN=$value";
                    break;

                case "2.5.4.5":  // "serialNumber"
                    $names[] = "SERIALNUMBER=$value";
                    break;

                case "2.5.4.6":  // "countryName"
                    $names[] = "C=$value";
                    break;

                case "2.5.4.7":  // "localityName"
                    $names[] = "L=$value";
                    break;

                case "2.5.4.8":  // "stateOrProvinceName"
                    $names[] = "S=$value";
                    break;

                case "2.5.4.10": // "organizationName"
                    $names[] = "O=$value";
                    break;

                case "2.5.4.11": // "organizationalUnitName",
                    $names[] = "OU=$value";
                    break;

                case "2.5.4.12": // "title",
                    $names[] = "T=$value";
                    break;

                case "2.5.4.42": // "givenName",
                    $names[] = "G=$value";
                    break;

                case "1.2.840.113549.1.9.1": // emailAddress
                    $names[] = "E=$value";
                    break;
            }
        }
        
        return join( ', ', array_reverse( $names ) );
    }

    /**
     * Compare two subject or issuer strings.  
     * TODO: instead convert the string back to their OIDs and compare the OIDs
     *
     * @param string $claimed
     * @param string $actual
     * @return bool
     */
    public static function compareIssuerStrings( $claimed, $actual )
    {
        $getIssuerComponents = function(  $issuer )
        {
            return array_reduce( explode( ',', $issuer ), function( $carry, $part )
            {
                list( $code, $value ) = explode( '=', trim( $part ) );
                // $value .= "x";
                $carry[ $code ] = $value;
                // OpenSSL and the .NET Framework seem to use different codes for some OIDs
                if ( $code == "emailAddress" ) $carry['E'] = $value;
                if ( $code == "E" ) $carry['emailAddress'] = $value;
                if ( $code == "ST" ) $carry['S'] = $value;
                if ( $code == "S" ) $carry['ST'] = $value;
    
                return $carry;
            }, array() );
        };
    
        $actualIssuer = $getIssuerComponents( $actual );
        $claimedIssuer = $getIssuerComponents( $claimed );
    
        // Are there any matches?  There should be.
        $matched = array_intersect_key( $actualIssuer, $claimedIssuer );

        // Make sure the values are the same where they intersect
        foreach ( $matched as $code => $value )
        {
            if ( $actualIssuer[ $code ] == $claimedIssuer[ $code ] ) continue;
            $matched = array();
            break;
        }

        return count( $matched ) > 0;
    }
}
