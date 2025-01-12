<?php
/**
 * File containing the ezcAuthenticationLdapOptions class.
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
 * Class containing the options for ldap authentication filter.
 *
 * Example of use:
 * <code>
 * // create an options object
 * $options = new ezcAuthenticationLdapOptions();
 * $options->protocol = ezcAuthenticationLdapFilter::PROTOCOL_TLS;
 *
 * // use the options object when creating a new LDAP filter
 * $ldap = new ezcAuthenticationLdapInfo( 'localhost', 'uid=%id%', 'dc=example,dc=com', 389 );
 * $filter = new ezcAuthenticationLdapFilter( $ldap, $options );
 *
 * // alternatively, you can set the options to an existing filter
 * $filter = new ezcAuthenticationLdapFilter( $ldap );
 * $filter->setOptions( $options );
 * </code>
 *
 * @property int $protocol
 *           How to connect to the LDAP server:
 *            - ezcAuthenticationLdapFilter::PROTOCOL_PLAIN - plain connection
 *            - ezcAuthenticationLdapFilter::PROTOCOL_TLS   - TLS connection
 *
 * @package Authentication
 * @version //autogen//
 */
class ezcAuthenticationLdapOptions extends ezcAuthenticationFilterOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options Options for this class
     */
    public function __construct( array $options = [] )
    {
        $this->protocol = ezcAuthenticationLdapFilter::PROTOCOL_PLAIN;

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'protocol':
                $allowedValues = [ezcAuthenticationLdapFilter::PROTOCOL_PLAIN, ezcAuthenticationLdapFilter::PROTOCOL_TLS];
                if ( !in_array( $value, $allowedValues, true ) )
                {
                    throw new ezcBaseValueException( $name, $value, implode( ', ', $allowedValues ) );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
