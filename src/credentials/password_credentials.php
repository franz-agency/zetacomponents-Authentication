<?php
/**
 * File containing the ezcAuthenticationPasswordCredentials structure.
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
 * Structure containing an id and password, used as authentication credentials.
 *
 * @package Authentication
 * @version //autogen//
 * @mainclass
 */
class ezcAuthenticationPasswordCredentials extends ezcAuthenticationCredentials
{
    /**
     * Constructs a new ezcAuthenticationPasswordCredentials object.
     *
     * @param string $id
     * @param string $password
     */
    public function __construct(
        /**
         * Username or userID or url.
         *
         */
        public $id,
        /**
         * Password for the id.
         *
         */
        public $password
    )
    {
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array Associative array of data members for this class
     * @return ezcAuthenticationPasswordCredentials
     */
    public static function __set_state( array $array )
    {
        return new ezcAuthenticationPasswordCredentials( $array['id'], $array['password'] );
    }

    /**
     * Returns string representation of the credentials.
     *
     * Use it to save the credentials in the session.
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}
?>
