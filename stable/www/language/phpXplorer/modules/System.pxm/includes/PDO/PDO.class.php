<?php
/*******************************************************************************
    The Kingdoms of Chaos - An online browser text game - <http://www.tkoc.net>
    Copyright (C) 2011 - Administrators of The Kingdoms of Chaos

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Contact Information:
    Petros Karipidis  - petros@rufunka.com - <http://www.rufunka.com/>
    Anastasios Nistas - tasosos@gmail.com  - <http://tasos.pavta.com/>

    Other Information
    =================
    The exact Author of each source file should be specified after this license
    notice. If not specified then the "Current Administrators" found at
    <http://www.tkoc.net/about.php> are considered the Authors of the source
    file.

    As stated at the License Section 5.d: "If the work has interactive user
    interfaces, each must display Appropriate Legal Notices; however, if the
    Program has interactive interfaces that do not display Appropriate Legal
    Notices, your work need not make them do so.", we require you give
    credits at the appropriate section of your interface.
********************************************************************************/

/** File PDO.class.php			*
 * 	Porting of native PHP 5.1 PDO	*
 *      object usable with PHP 4.X.X	*
 *      and PHP 5.0.X version.		*
 * ------------------------------------ *     
 *(C) Andrea Giammarchi [2005/10/19]	*
 * ____________________________________ */

// check and preserve native PDO driver, for PHP4 or PHP 5.0 users
	
	// SUPPORTED STATIC ENVIROMENT VARIABLES
	define('PDO_ATTR_SERVER_VERSION', 4);	// server version
	define('PDO_ATTR_CLIENT_VERSION', 5);	// client version
	define('PDO_ATTR_SERVER_INFO', 6);	// server informations
	define('PDO_ATTR_PERSISTENT', 12);	// connection mode, persistent or normal
	
	// SUPPORTED STATIC PDO FETCH MODE VARIABLES
	define('PDO_FETCH_ASSOC', 2);		// such mysql_fetch_assoc
	define('PDO_FETCH_NUM', 3);		// such mysql_fetch_row
	define('PDO_FETCH_BOTH', 4);		// such mysql_fetch_array
	define('PDO_FETCH_OBJ', 5);		// such mysql_fetch_object
	
	// UNSUPPORTED STATIC PDO FETCH MODE VARIABLES
        define('PDO_FETCH_LAZY', 1);		// usable but not supported, default is PDO_FETCH_BOTH and will be used
	define('PDO_FETCH_BOUND', 6);		// usable but not supported, default is PDO_FETCH_BOTH and will be used
	
	/**
	 * Class PDO
         * 	PostgreSQL, SQLITE and MYSQL PDO support for PHP 4.X.X or PHP 5.0.X users, compatible with PHP 5.1.0 (RC1).
         *
         * DESCRIPTION [directly from http://us2.php.net/manual/en/ref.pdo.php]
	 * 	The PHP Data Objects (PDO) extension defines a lightweight, consistent interface for accessing databases in PHP.
         *      Each database driver that implements the PDO interface can expose database-specific features as regular extension functions.
         *      Note that you cannot perform any database functions using the PDO extension by itself;
         *      you must use a database-specific PDO driver to access a database server.
         *
         * HOW TO USE
         * 	To know how to use PDO driver and all its methods visit php.net wonderful documentation.
         *      http://us2.php.net/manual/en/ref.pdo.php
         *      In this class some methods are not available and actually this porting is only for MySQL, SQLITE and PostgreSQL.
         *
         * LIMITS
         * 	For some reasons ( time and php used version with this class ) some PDO methods are not availables and
         *      someother are not totally supported.
         *      
         *      PDO :: UNSUPPORTED METHODS:
         *      	- beginTransaction 	[ mysql 3 has not transaction and manage them is possible only with a direct BEGIN 
         *              			  or COMMIT query ]
         *              - commit
         *              - rollback
         *              
         *      PDO :: NOT TOTALLY SUPPORTED METHODS:
         *      	- getAttribute		[ accepts only PDO_ATTR_SERVER_INFO, PDO_ATTR_SERVER_VERSION,
         *              			  PDO_ATTR_CLIENT_VERSION and PDO_ATTR_PERSISTENT attributes ]
         *              - setAttribute		[ supports only PDO_ATTR_PERSISTENT modification ]
         *              - lastInsertId		[ only fo PostgreSQL , returns only pg_last_oid ]
         *
         *      - - - - - - - - - - - - - - - - - - - - 
         *              
         *      PDOStatement :: UNSUPPORTED METHODS:
         *      	- bindColumn 		[ is not possible to undeclare a variable and using global scope is not
         *              			  really a good idea ]
         *              
         *      PDOStatement :: NOT TOTALLY SUPPORTED METHODS:
         *      	- getAttribute		[ accepts only PDO_ATTR_SERVER_INFO, PDO_ATTR_SERVER_VERSION,
         *              			  PDO_ATTR_CLIENT_VERSION and PDO_ATTR_PERSISTENT attributes ]
         *              - setAttribute		[ supports only PDO_ATTR_PERSISTENT modification ]
         *              - setFetchMode		[ supports only PDO_FETCH_NUM, PDO_FETCH_ASSOC, PDO_FETCH_OBJ and
         *              			  PDO_FETCH_BOTH database reading mode ]
	 * ---------------------------------------------
	 * @Compatibility	>= PHP 4
	 * @Dependencies	PDO_mysql.class.php
         *                      PDO_sqlite.class.php
         *                      PDOStatement_mysql.class.php
         *                      PDOStatement_sqlite.class.php
	 * @Author		Andrea Giammarchi
	 * @Site		http://www.devpro.it/
	 * @Mail		andrea [ at ] 3site [ dot ] it
	 * @Date		2005/10/13
	 * @LastModified	2005/12/01 21:40
	 * @Version		0.1b - tested, supports only PostgreSQL, MySQL or SQLITE databases
	 */ 
	class PDO {
		
		/** Modified on 2005/12/01 to support new PDO constants on PHP 5.1.X */

		var $FETCH_ASSOC = PDO_FETCH_ASSOC;
		var $FETCH_NUM = PDO_FETCH_NUM;
		var $FETCH_BOTH = PDO_FETCH_BOTH;
		var $FETCH_OBJ = PDO_FETCH_OBJ;
		var $FETCH_LAZY = PDO_FETCH_LAZY;
		var $FETCH_BOUND = PDO_FETCH_BOUND;
		var $ATTR_SERVER_VERSION = PDO_ATTR_SERVER_VERSION;
		var $ATTR_CLIENT_VERSION = PDO_ATTR_CLIENT_VERSION;
		var $ATTR_SERVER_INFO = PDO_ATTR_SERVER_INFO;
		var $ATTR_PERSISTENT = PDO_ATTR_PERSISTENT;
		
		/**
		 * 'Private' variables:
		 *	__driver:PDO_*		Dedicated PDO database class
		 */
		var $__driver;
		
		/**
		 * Public constructor
		 *	http://us2.php.net/manual/en/function.pdo-construct.php
		 */
		function PDO($string_dsn, $string_username = '', $string_password = '', $array_driver_options = null) {
			$con = &$this->__getDNS($string_dsn);
			if($con['dbtype'] === 'mysql') {
				require_once('PDO_mysql.class.php');
				if(isset($con['port']))
					$con['host'] .= ':'.$con['port'];
				$this->__driver = new PDO_mysql(
					$con['host'],
					$con['dbname'],
					$string_username,
					$string_password
				);
			}
			elseif($con['dbtype'] === 'sqlite2' || $con['dbtype'] === 'sqlite') {
				require_once('PDO_sqlite.class.php');
				$this->__driver = new PDO_sqlite($con['dbname']);
			}
			elseif($con['dbtype'] === 'pgsql') {
				require_once('PDO_pgsql.class.php');
				$string_dsn = "host={$con['host']} dbname={$con['dbname']} user={$string_username} password={$string_password}";
				if(isset($con['port']))
					$string_dsn .= " port={$con['port']}";
				$this->__driver = new PDO_pgsql($string_dsn);
			}
		}
		
		/** UNSUPPORTED
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-begintransaction.php
		 */
		function beginTransaction() {
			$this->__driver->beginTransaction();
		}
		
		/** NOT NATIVE BUT MAYBE USEFULL FOR PHP < 5.1 PDO DRIVER
		 * Public method
                 * Calls database_close function.
		 *	this->close( Void ):Boolean
                 * @Return	Boolean		True on success, false otherwise
		 */
		function close() {
			return $this->__driver->close();
		}
		
		/** UNSUPPORTED
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-commit.php
		 */
		function commit() {
			$this->__driver->commit();
		}
		
		/** 
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-exec.php
		 */
		function exec($query) {
			return $this->__driver->exec($query);
		}
		
		/** 
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-errorcode.php
		 */
		function errorCode() {
			return $this->__driver->errorCode();
		}
		
		/** 
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-errorinfo.php
		 */
		function errorInfo() {
			return $this->__driver->errorInfo();
		}
		
		/** NOT TOTALLY UNSUPPORTED
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-getattribute.php
		 */
		function getAttribute($attribute) {
			return $this->__driver->getAttribute($attribute);
		}
		
		/** 
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-lastinsertid.php
		 */
		function lastInsertId() {
			return $this->__driver->lastInsertId();
		}
		
		/** 
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-prepare.php
		 */
		function prepare($query, $array = Array()) {
			return $this->__driver->prepare($query, $array = Array());
		}
		
		/** 
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-query.php
		 */
		function query($query) {
			return $this->__driver->query($query);
		}
		
		/** 
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-quote.php
		 */
		function quote($string) {
			return $this->__driver->quote($string);
		}
		
		/** UNSUPPORTED
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-rollback.php
		 */
		function rollBack() {
			$this->__driver->rollBack();
		}
		
		/** NOT TOTALLY UNSUPPORTED
		 * Public method
		 *	http://us2.php.net/manual/en/function.pdo-setattribute.php
		 */
		function setAttribute($attribute, $mixed) {
			return $this->__driver->setAttribute($attribute, $mixed);
		}
		
		// PRIVATE METHOD [uncommented]
                function __getDNS(&$string) {
			$result = array();
			$pos = strpos($string, ':');
			$parameters = explode(';', substr($string, ($pos + 1)));
			$result['dbtype'] = strtolower(substr($string, 0, $pos));
			for($a = 0, $b = count($parameters); $a < $b; $a++) {
				$tmp = explode('=', $parameters[$a]);
				if(count($tmp) == 2)
					$result[$tmp[0]] = $tmp[1];
				else
					$result['dbname'] = $parameters[$a];
			}
			return $result;
		}
	}

?>