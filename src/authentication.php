<?php
/**
 * File containing the ezcAuthentication class.
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
 * Container for authentication filters.
 *
 * This is the main class of the authentication component. Filters are added to
 * an object of this class, which will run the filters in sequence. At the end of
 * this process, the status property will contain the statuses of the filters, and
 * the developer can use those statuses to display to the user messages such as
 * "Password incorrect".
 *
 * The session property is optional and it is used to store the authentication
 * information between requests.
 *
 * The credentials property will be passed to all the filters in the queue.
 *
 * Example (using the Htpasswd filter):
 * <code>
 * $credentials = new ezcAuthenticationPasswordCredentials( 'jan.modaal', 'b1b3773a05c0ed0176787a4f1574ff0075f7521e' );
 * $authentication = new ezcAuthentication( $credentials );
 * $authentication->session = new ezcAuthenticationSession();
 * $authentication->addFilter( new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd' ) );
 * // add other filters if needed
 * if ( !$authentication->run() )
 * {
 *     // authentication did not succeed, so inform the user
 *     $status = $authentication->getStatus();
 *     $err = array(
 *             'ezcAuthenticationHtpasswdFilter' => array(
 *                 ezcAuthenticationHtpasswdFilter::STATUS_USERNAME_INCORRECT => 'Incorrect username',
 *                 ezcAuthenticationHtpasswdFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect password'
 *                 )
 *             );
 *     foreach ( $status as $line )
 *     {
 *         list( $key, $value ) = each( $line );
 *         echo $err[$key][$value] . "\n";
 *     }
 * }
 * else
 * {
 *     // authentication succeeded, so allow the user to see his content
 * }
 * </code>
 *
 * @property ezcAuthenticationSession $session
 *           The session object to use during authentication to store the
 *           authentication information between requests.
 * @property ezcAuthenticationStatus $status
 *           The status object which holds the status of the run filters.
 * @property ezcAuthenticationCredentials $credentials
 *           The user credentials to pass to the authentication filters.
 *
 * @package Authentication
 * @version //autogen//
 * @mainclass
 */
class ezcAuthentication
{
    /**
     * The filter queue of the authentication process.
     * 
     * @var array(ezcAuthenticationFilter)
     */
    protected $filters = [];

    /**
     * Options for the Authentication object.
     * 
     * @var ezcAuthenticationOptions
     */
    protected $options;

    /**
     * The properties of this class.
     * 
     * @var array(string=>mixed)
     */
    private $properties = [];

    /**
     * Creates a new object of this class.
     *
     * @param ezcAuthenticationCredentials $credentials Authentication credentials
     * @param ezcAuthenticationOptions $options Options for this class
     */
    public function __construct( ezcAuthenticationCredentials $credentials, ezcAuthenticationOptions $options = null )
    {
        $this->credentials = $credentials;
        $this->status = new ezcAuthenticationStatus();
        $this->options = $options ?? new ezcAuthenticationOptions();
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, mixed $value )
    {
        switch ( $name )
        {
            case 'session':
                if ( $value instanceof ezcAuthenticationSession )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcAuthenticationSession' );
                }
                break;

            case 'status':
                if ( $value instanceof ezcAuthenticationStatus )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcAuthenticationStatus' );
                }
                break;

            case 'credentials':
                if ( $value instanceof ezcAuthenticationCredentials )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcAuthenticationCredentials' );
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name The name of the property for which to return the value
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        return match ($name) {
            'session', 'status', 'credentials' => $this->properties[$name],
            default => throw new ezcBasePropertyNotFoundException( $name ),
        };
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name The name of the property to test if it is set
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        return match ($name) {
            'session', 'status', 'credentials' => isset( $this->properties[$name] ),
            default => false,
        };
    }

    /**
     * Sets the options of this class to $options.
     *
     * @param ezcAuthenticationOptions $options Options for this class
     */
    public function setOptions( ezcAuthenticationOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Returns the options of this class.
     *
     * @return ezcAuthenticationOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Runs through all the filters in the filter list.
     *
     * @return bool
     */
    public function run()
    {
        $code = ezcAuthenticationFilter::STATUS_OK;

        $credentials = $this->credentials;

        if ( isset( $this->session ) )
        {
            $code = $this->session->run( $credentials );
            $this->status->append( $this->session::class, $code );
        }

        if ( !isset( $this->session ) || $code === ezcAuthenticationSession::STATUS_EMPTY )
        {
            foreach ( $this->filters as $filter )
            {
                $code = $filter[0]->run( $credentials );
                if ( $filter[0] instanceof ezcAuthenticationGroupFilter )
                {
                    $statuses = $filter[0]->status->get();

                    // append the statuses from the filters in the group to the
                    // status of the Authentication object
                    foreach ( $statuses as $status )
                    {
                        $key = key($status);
                        $value = current($status);
                        next($status);
                        $this->status->append( $key, $value );
                    }
                }
                else
                {
                    $this->status->append( $filter[0]::class, $code );
                }

                if ( ( $filter[1] === true && $code !== ezcAuthenticationFilter::STATUS_OK ) )
                {
                    return false;
                }

                if ( $filter[1] === true && $code === ezcAuthenticationFilter::STATUS_OK )
                {
                    break;
                }
            }
        }
        elseif ( $code === ezcAuthenticationSession::STATUS_EXPIRED )
        {
            return false;
        }

        if ( $code !== ezcAuthenticationFilter::STATUS_OK )
        {
            return false;
        }

        if ( isset( $this->session ) )
        {
            $this->session->save( $credentials->__toString() );
        }

        return true;
    }

    /**
     * Adds an authentication filter at the end of the filter list.
     *
     * By specifying the second parameter as true, the authentication process
     * (triggered by calling the run() method) will stop after processing this
     * filter regardless of its success.
     *
     * @param ezcAuthenticationFilter $filter The authentication filter to add
     * @param bool $stop If authentication should continue past this filter
     */
    public function addFilter( ezcAuthenticationFilter $filter, $stop = false )
    {
        $this->filters[] = [$filter, $stop];
    }

    /**
     * Returns the status of authentication.
     *
     * The format of the returned array is array( array( class => code ) ).
     *
     * Example:
     * <code>
     * array(
     *        array( 'ezcAuthenticationSession' => ezcAuthenticationSession::STATUS_EMPTY ),
     *        array( 'ezcAuthenticationDatabaseFilter' => ezcAuthenticationDatabaseFilter::STATUS_PASSWORD_INCORRECT )
     *      );
     * </code>
     * 
     * @return array(string=>mixed)
     */
    public function getStatus()
    {
        return $this->status->get();
    }
}
?>
