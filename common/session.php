<?php
/**
 *
 * Security System
 *
 * @package  HTTP_Session
 * @author   Aaron Kalin <no@spam.com>
 * @version  1.0.0
 * @category HTTP
 */
class HTTP_Session_DB extends PEAR {
	/**
	 * Options for the session wrapper
	 *
	 * @var array
	 * @access private
	 */
	var $options;

	/**
	 * Database object
	 *
	 * @var object
	 * @access private
	 */
	var $db;

	/**
	 * Prepared Statements
	 *
	 * @var array
	 * @access private
	 */
	var $prepared;

	/**
	 * Session cache
	 *
	 * @var mixed
	 * @access private
	 */
	var $crc;

	/**
	 * Constrtuctor method
	 *
	 * @access	public
	 * @param	object	$db			Database Object
	 * @param	array	$options	Additional options for the object
	 * @return	void
	 */
	function HTTP_Session_DB(&$db, $options) {
		$this->crc = false;
		$this->options = $this->defaultOptions();
		$this->db = $db;
		$this->parseOptions($options);
		$this->prepared = $this->prepareStatements();
		session_module_name('user');
		session_set_save_handler(
			array(&$this, 'open'), 
			array(&$this, 'close'), 
			array(&$this, 'read'), 
			array(&$this, 'write'), 
			array(&$this, 'destroy'), 
			array(&$this, 'gc')
		);
	}

	/**
	 * Sets the default class options
	 *
	 * @access	private
	 * @return	void
	 */
	function defaultOptions() {
		$options = array('lifetime' => '1440',
							'table' => 'sessions',
							'id_field' => 'session_id',
							'activity_field' => 'session_activity',
							'data_field' => 'session_data');
		return $options;
	}

	/**
	 * Creates the prepared queries for internal use
	 *
	 * @access	private
	 * @return	array
	 */
	function prepareStatements() {
		$prepared = array();
		$prepared['session_read_select'] = 'SELECT %s FROM %s WHERE %s = %s AND %s >= %d';
		$prepared['session_write_update_1'] = 'UPDATE %s SET %s = %d WHERE %s = %s AND %s >= %d';
		$prepared['session_write_select'] = 'SELECT COUNT(%s) FROM %s WHERE %s = %s';
		$prepared['session_write_insert'] = 'INSERT INTO %s (%s, %s, %s) VALUES (%s, %d, %s)';
		$prepared['session_write_update_2'] = 'UPDATE %s SET %s = %d, %s = %s WHERE %s = %s AND %s >= %d';
		$prepared['session_destroy_delete'] = 'DELETE FROM %s WHERE %s = %s';
		$prepared['session_gc_delete'] = 'DELETE FROM %s WHERE %s < %d';
		return $prepared;
	}

	/**
	 * Parses parameter options for internal use
	 *
	 * @access	public
	 * @param	array	$options	Additional options for the object
	 * @return	void
	 */
	function parseOptions($options) {
		foreach ($options as $option => $value) {
			if (in_array($option, array_keys($this->options))) {
				$this->options[$option] = $value;
			}
		}
	}

	/**
	 * Session open - Called when a session is created
	 *
	 * @access	private
	 * @return	bool
	 */
	function open() {
		return true;
	}

	/**
	 * Session close - Called when a session is closed
	 *
	 * @access	private
	 * @return	bool
	 */
	function close() {
		return true;
	}

	/**
	 * Session read - Called when a session is accessed
	 *
	 * @access	private
	 * @return	mixed	Session data on success
	 */
	function read($key) {
		$now = time();
		$result = $this->db->getOne(sprintf($this->prepared['session_read_select'],
									$this->db->quoteIdentifier($this->options['data_field']), 
									$this->db->quoteIdentifier($this->options['table']),
									$this->db->quoteIdentifier($this->options['id_field']),
									$this->db->quoteSmart($key),
									$this->db->quoteIdentifier($this->options['activity_field']),
									$this->db->quoteSmart($now)));
		if (DB::isError($result)) {
			return false;
		}
		$this->crc = strlen($result) . crc32($result);
		return $result;
	}

	/**
	 * Session write - Called when writing session data
	 *
	 * @access	private
	 * @return	bool
	 */
	function write($key,$val) {
		$now = time();
		$expire = $now + $this->options['lifetime'];
		if (($this->crc !== false) && ($this->crc === (strlen($val) . crc32($val)))) {
			// $_SESSION hasn't been touched, no need to update the blob column
			$query = sprintf($this->prepared['session_write_update_1'],
								$this->db->quoteIdentifier($this->options['table']),
								$this->db->quoteIdentifier($this->options['activity_field']),
								$this->db->quoteSmart($expire),
								$this->db->quoteIdentifier($this->options['id_field']),
								$this->db->quoteSmart($key),
								$this->db->quoteIdentifier($this->options['activity_field']),
								$this->db->quoteSmart($now));
		} else {
			// Check if table row already exists
			$query = sprintf($this->prepared['session_write_select'],
								$this->db->quoteIdentifier($this->options['id_field']),
								$this->db->quoteIdentifier($this->options['table']),
								$this->db->quoteIdentifier($this->options['id_field']),
								$this->db->quoteSmart($key));
			$result = $this->db->getOne($query);
			if (DB::isError($result)) {
				return false;
			}
			if ($result == 0) {
				// Insert new row into table
				$query = sprintf($this->prepared['session_write_insert'],
									$this->db->quoteIdentifier($this->options['table']),
									$this->db->quoteIdentifier($this->options['id_field']),
									$this->db->quoteIdentifier($this->options['activity_field']),
									$this->db->quoteIdentifier($this->options['data_field']),
									$this->db->quoteSmart($key),
									$this->db->quoteSmart($expire),
									$this->db->quoteSmart($val));
			} else {
				// Update existing row
				$query = sprintf($this->prepared['session_write_update_2'],
									$this->db->quoteIdentifier($this->options['table']),
									$this->db->quoteIdentifier($this->options['activity_field']),
									$this->db->quoteSmart($expire),
									$this->db->quoteIdentifier($this->options['data_field']),
									$this->db->quoteSmart($val),
									$this->db->quoteIdentifier($this->options['id_field']),
									$this->db->quoteSmart($key),
									$this->db->quoteIdentifier($this->options['activity_field']),
									$this->db->quoteSmart($now));
			}
		}
		$result = $this->db->query($query);
		if (DB::isError($result)) {
			return false;
		}
		return true;
	}

	/**
	 * Session destroy - Called when a session is destroyed
	 *
	 * @access	private
	 * @return	bool
	 */
	function destroy($key) {
		$result = $this->db->query(sprintf($this->prepared['session_destroy_delete'],
									$this->db->quoteIdentifier($this->options['table']),
									$this->db->quoteIdentifier($this->options['id_field']),
									$this->db->quoteSmart($key)));
		if (DB::isError($result)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Session gc - Garbage collection, called by probability
	 *
	 * @access	private
	 * @return	void
	 */
	function gc() {
		$expire = time() - $this->options['lifetime'];
		$result = $this->db->query(sprintf($this->prepared['session_gc_delete'],
									$this->db->quoteIdentifier($this->options['table']),
									$this->db->quoteIdentifier($this->options['activity_field']),
									$this->db->quoteSmart($expire)));
		if (DB::isError($result)) {
			return false;
		} else {
			return true;
		}
	}
}

?>