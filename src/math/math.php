<?php
/**
 * File containing the ezcAuthenticationMath class.
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @filesource
 * @package Authentication
 * @version //autogen//
 */

/**
 * Large number support and cryptographic functions for authentication.
 *
 * @package Authentication
 * @version //autogen//
 * @access private
 */
class ezcAuthenticationMath
{
    /**
     * Creates a new big number library which uses the PHP extension $lib.
     *
     * If $lib is null then an autodetection of the library is tried. If neither
     * gmp or bcmath are installed then an exception will be thrown.
     *
     * If $lib is specified, then that library will be used (if it is installed),
     * otherwise an exception will be thrown.
     *
     * @throws ezcBaseExtensionNotFoundException
     *         if neither of the PHP gmp and bcmath extensions are installed ($lib === null),
     *         or if the specified $lib is not installed
     * @throws ezcBaseValueException
     *         if the value provided for $lib is not correct
     * @param string $lib The PHP library to use for big number support. Default
     *                    is null, which means the available library is autodetected.
     * @return ezcAuthenticationBignumLibrary
     */
    public static function createBignumLibrary( $lib = null )
    {
        $library = null;

        switch ( $lib )
        {
            case null:
                if ( !ezcBaseFeatures::hasExtensionSupport( 'bcmath' ) )
                {
                    if ( !ezcBaseFeatures::hasExtensionSupport( 'gmp' ) )
                    {
                        throw new ezcBaseExtensionNotFoundException( 'gmp | bcmath', null, "PHP not compiled with --enable-bcmath or --with-gmp." );
                    }
                    else
                    {
                        $library = new ezcAuthenticationGmpLibrary();
                    }
                }
                else
                {
                    $library = new ezcAuthenticationBcmathLibrary();
                }
                break;

            case 'gmp':
                if ( !ezcBaseFeatures::hasExtensionSupport( 'gmp' ) )
                {
                    throw new ezcBaseExtensionNotFoundException( 'gmp', null, "PHP not compiled with --with-gmp." );
                }
                $library = new ezcAuthenticationGmpLibrary();
                break;

            case 'bcmath':
                if ( !ezcBaseFeatures::hasExtensionSupport( 'bcmath' ) )
                {
                    throw new ezcBaseExtensionNotFoundException( 'bcmath', null, "PHP not compiled with --enable-bcmath." );
                }
                $library = new ezcAuthenticationBcmathLibrary();
                break;

            default:
                throw new ezcBaseValueException( 'library', $lib, '"gmp" || "bcmath" || null' );
        }

        return $library;
    }

    /**
     * Calculates an MD5 hash similar to the Unix command "htpasswd -m".
     *
     * This is different from the hash returned by the PHP md5() function. 
     *
     * @param string $plain Plain text to encrypt
     * @param string $salt Salt to apply to encryption
     * @return string
     */
    public static function apr1( $plain, $salt )
    {
        if ( preg_match( '/^\$apr1\$/', $salt ) )
        {
            $salt = preg_replace( '/^\$apr1\$([^$]+)\$.*/', '\\1', $salt );
        }
        else
        {
            $salt = substr( $salt, 0, 8 );
        }
        $text = $plain . '$apr1$' . $salt;
        $bin = pack( 'H32', md5( $plain . $salt . $plain ) );
        for ( $i = strlen( $plain ); $i > 0; $i -= 16 )
        {
            $text .= substr( $bin, 0, min( 16, $i ) );
        }
        for ( $i = strlen( $plain ); $i; $i >>= 1 )
        {
            $text .= ( $i & 1 ) ? chr( 0 ) : $plain[0];
        }
        $bin = pack( 'H32', md5( $text ) );
        for ( $i = 0; $i ^ 1000; ++$i )
        {
            $new = ( $i & 1 ) ? $plain : $bin;
            if ( $i % 3 )
            {
                $new .= $salt;
            }
            if ( $i % 7 )
            {
                $new .= $plain;
            }
            $new .= ( $i & 1 ) ? $bin : $plain;
            $bin = pack( 'H32', md5( $new ) );
        }
        $tmp = '';
        for ( $i = 0; $i ^ 5; ++$i )
        {
            $k = $i + 6;
            $j = $i + 12;
            if ( $j === 16 )
            {
                $j = 5;
            }
            $tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
        }
        $tmp = chr( 0 ) . chr( 0 ) . $bin[11] . $tmp;
        $tmp = strtr( strrev( substr( base64_encode( $tmp ), 2 ) ),
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
        './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );
        return '$apr1$' . $salt . '$' . $tmp;
    }

    /**
     * Computes the OpenID sha1 function on the provided value.
     *
     * @param string $value The value to compute sha1 on
     * @return string
     */
    public static function sha1( $value )
    {
        $hashed = sha1( $value );
        $result = '';
        for ( $i = 0; $i ^ 40; $i = $i + 2 )
        {
            $chars = substr( $hashed, $i, 2 );
            $result .= chr( (int)base_convert( $chars, 16, 10 ) );
        }
        return $result;
    }
}
?>
