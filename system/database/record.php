<?php namespace System\Database;

/**
 * Nano
 *
 * Just another php framework
 *
 * @package		nano
 * @link		http://madebykieron.co.uk
 * @copyright	http://unlicense.org/
 */

abstract class Record {

	/**
	 * Holds the current record data
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * The database table
	 *
	 * @var array
	 */
	public static $table;

	/**
	 * The database table primary key
	 *
	 * @var array
	 */
	public static $primary = 'id';

	/**
	 * Find a record by primary key and return a new Record object
	 *
	 * @param int
	 * @return object Record
	 */
	public static function find($id) {
		return static::where(static::$primary, '=', $id)->fetch();
	}

	/**
	 * Create a new instance of the record class
	 *
	 * @param array
	 */
	public function __construct($row = array()) {
		$this->data = $row;
	}

	/**
	 * Commit the data array to the database
	 *
	 * @return int Affected Row
	 */
	public function save() {
		if(isset($this->data[static::$primary])) {
			return static::where(static::$primary, '=', $this->data[static::$primary])->update($this->data);
		}

		return static::insert($this->data);
	}

	/**
	 * Delete the record from the database
	 *
	 * @return int Affected Row
	 */
	public function delete() {
		static::where(static::$primary, '=', $this->data[static::$primary])->delete();
	}

	/**
	 * Set the data array
	 *
	 * @param array|object
	 */
	public function populate($row) {
		$this->data = array_merge($this->data, (is_object($row) ? get_object_vars($row) : $row));
	}

	/**
	 * Magic method for getting a item from the data array
	 *
	 * @param string
	 * @return mixed
	 */
	public function __get($key) {
		return Arr::get($this->data, $key);
	}

	/**
	 * Magic method for setting a item in the data array
	 *
	 * @param string
	 * @param mixed
	 */
	public function __set($key, $value) {
		Arr::set($this->data, $key, $value);
	}

	/**
	 * Create a new instance of the record class for chaining
	 *
	 * @param array
	 * @return object
	 */
	public static function create($row) {
		return static::find(static::insert_get_id($row));
	}

	/**
	 * Commit data array to database matching the primary key
	 *
	 * @param int
	 * @param data
	 * @return int Affected Row
	 */
	public static function update($id, $row) {
		return static::where(static::$primary, '=', $id)->update($row);
	}

	/**
	 * Magic method for calling other Query methods
	 *
	 * @param string
	 * @param array
	 * @return mixed
	 */
	public static function __callStatic($method, $arguments) {
		$obj = Query::table(static::$table)->for(get_called_class());

		if(method_exists($obj, $method)) {
			return call_user_func_array(array($obj, $method), $arguments);
		}
	}

}