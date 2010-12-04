<?
/**
 * @package    phpdbo-api
 * @subpackage phpmydb
 * @author     Mike Green <mdgreen@gmail.com>
 * @copyright  Copyright (c) 2010, Mike Green
 * @license    http://opensource.org/licenses/gpl-2.0.php GPLv2
 */
(basename($_SERVER['PHP_SELF']) == basename(__FILE__)) && die('Access Denied');
class phpmydb {

var $_dbh = false;
var $_db_name;
var $_persist = false;
var $connected = false;

/**
 ** Constructor
 **
 **/
function phpmydb($db_host='',$db_name='',$db_user='',$db_pass='') {
	if (!empty($db_host) and !empty($db_user) and !empty($db_pass)) {
		$this->db_connect($db_host,$db_name,$db_user,$db_pass);
		}
	}

/**
 ** Returns the number of rows affected by the last INSERT, UPDATE, or DELETE
 ** query.
 **
 **/
function db_affected_rows() {
	return mysql_affected_rows($this->_dbh);
	}

/**
 ** Base database operations defined below.  Starting with db_connect, which
 ** as the name implies attempts to connect to the specified database.
 **
 **/
function db_connect($dbh='',$dbn='',$dbu='',$dbp='') {
	if ($this->_persist === true) {
		$func = 'mysql_pconnect';
		} else {
		$func = 'mysql_connect';
		}
	if (!$dbh = $func($dbh,$dbu,$dbp)) {
		return false;
		}
	if (!($dbn == '')) {
		if (!$this->db_select($dbn)) {
			return false;
			}
		}
	$this->_dbh =& $dbh;
	$this->connected = true;
	return true;
	}

/**
 ** Move the database cursor to row $row on the select query qid.
 **
 **/
function db_data_seek($qid,$row) {
	return mysql_data_seek($qid,$row);
	}

/**
 ** Disconnect from the database connection.
 **
 **/
function db_disconnect() {
	$this->connected = false;
	return mysql_close($this->_dbh);
	}

/**
 ** Display the most recent error message.
 **
 **/
function db_error() {
	return mysql_error($this->_dbh);
	}

/**
 ** Fetch a row and convert it into an associative array,
 ** with column names as the array key.
 **/
function db_fetch_array($qid=false) {
	if ($qid === false) { return false; }
	return mysql_fetch_array($qid,MYSQL_ASSOC);
	}

/**
 ** Fetch a row and convert it into an object.
 **
 **/
function db_fetch_object($qid=false) {
	if ($qid === false) { return false; }
	return mysql_fetch_object($qid);
	}

/**
 ** Fetch a row and convert it into an associative array,
 ** without column names as the array key.
 **/
function db_fetch_row($qid=false) {
	if ($qid === false) { return false; }
	return mysql_fetch_row($qid);
	}

/**
 ** Fetch field flags (works off of db_list_fiels())
 **
 **/
function db_field_flags($qid=false,$field_num='') {
	if ($qid === false) { return false; }
	if (!is_numeric($field_num)) { return false; }
	return mysql_field_flags($qid,$field_num);
	}

/**
 ** Fetch a field length (works off of db_list_fiels())
 **
 **/
function db_field_length($qid=false,$field_num='') {
	if ($qid === false) { return false; }
	if (!is_numeric($field_num)) { return false; }
	return mysql_field_len($qid,$field_num);
	}

/**
 ** Fetch a field name (works off of db_list_fiels())
 **
 **/
function db_field_name($qid=false,$field_num='') {
	if ($qid === false) { return false; }
	if (!is_numeric($field_num)) { return false; }
	return mysql_field_name($qid,$field_num);
	}

/**
 ** Return field number of table field name $field from query result qid.
 **
 **/
function db_field_num($qid=false,$field) {
	if ($qid === false) { return false; }
	$field = strtolower($field);
	for ($j=0; $j < $this->db_num_fields(); $j++) {
		if (strtolower($this->db_field_name($qid,$j)) == $field) {
			return $j;
			}
		}
	return false;
	}

/**
 ** Fetch a field type (works off of db_list_fiels())
 **
 **/
function db_field_type($qid=false,$field_num='') {
	if ($qid === false) { return false; }
	if (!is_numeric($field_num)) { return false; }
	return mysql_field_type($qid,$field_num);
	}

/**
 ** Free results from last query
 **
 **/
function db_free_result($qid=false) {
	return mysql_free_result($qid);
	}

/**
 ** Fetch the insert id generated by the most recent insert statement
 ** on a table with an auto_increment primary key.
 **
 **/
function db_insert_id() {
	return mysql_insert_id($this->_dbh);
	}

/**
 ** Fetch a list of field names.
 **
 **/
function db_list_fields($table='') {
	if ($this->_db_name == '' or $table == '') { return false; }
	return mysql_list_fields($this->_db_name,$table);
	}

/**
 ** Return a list of database tables.
 **
 **/
function db_list_tables($database) {
	$sql = 'show tables from `' . addslashes($database) . '`';
	return $this->db_query($sql);
	}

/**
 ** Returns the number of fields in a select query qid.
 **
 **/
function db_num_fields($qid=false) {
	return mysql_num_fields($qid);
	}

/**
 ** Return the number of rows returned from the SELECT query qid.
 **
 **/
function db_num_rows($qid=false) {
	return mysql_num_rows($qid);
	}

/**
 ** Ping the current database connection
 **
 **/
function db_ping() {
	return mysql_ping($this->_dbh);
	}

/**
 ** Perform a query, using the current database handle.
 **
 **/
function db_query($query) {
	return mysql_query($query,$this->_dbh);
	}

/**
 ** Fetch results from a row.
 **
 **/
function db_result($qid=false,$row='',$field='') {
	if ($qid === false) { return false; }
	return mysql_result($qid,$row,$field);
	}

/**
 ** Select the database subsequent operations will be peformed on.
 **
 **/
function db_select($db_name='') {
	if ($db_name == '') { return false; }
	$db_name = addslashes($db_name);
	if (mysql_select_db($db_name) === false) {
		return false;
		} else {
		$this->_db_name = $db_name;
		return true;
		}
	}

/**
 ** Take a query identifier and create an array of associative arrays.
 **
 **/
function query_hash_rows($qid=false,$format='associative') {
	if ($qid === false) { return false; }
	$tmp_ar = array();
	if ($this->db_num_rows($qid) < 1) { return $tmp_ar; }
	for($j=0; $j < $this->db_num_rows($qid); $j++) {
		if ($format == 'associative') {
			$tmp_ar[] = $this->db_fetch_array($qid);
			} else {
			$tmp_ar[] = $this->db_fetch_row($qid);
			}
		}
	return $tmp_ar;
	}
}
?>