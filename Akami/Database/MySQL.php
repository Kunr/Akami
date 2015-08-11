<?php
/**
 * Database / Mysql
 *
 * @author Rakume Hayashi <i@fake.moe>
 * @copyright 2015 Lingoys!Art.
 * @version 1.0
 * @package Akami
 */

namespace Akami\Database;

class MySQL extends \Akami\Database
{
  /**
   * MySQL connection data
   *
   * @var array
   */
  protected $config = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => '',
    'pconnect' => true,
    'charset'  => 'utf8'
  );

  /**
   * Class construction
   *
   * @return void
   */
  public function __construct($config)
  {
    // Load Config
    foreach ($config as $key => $value)
    {
      if (in_array($key, $this->config))
      {
        $this->config[$key] = $value;
      }
    }

    $this->connect();
  }

  /**
   * Connect to the mysqli
   *
   * @return \MySQLi
   */
  protected function connect()
  {
    $config = $this->config;

    if ($pconnect)
    {
      $link = mysql_pconnect($config['hostname'], $config['username'], $config['password'], MYSQL_CLIENT_COMPRESS);
    }
      else
    {
      $link = mysql_connect($config['hostname'], $config['username'], $config['password'], 1, MYSQL_CLIENT_COMPRESS);
    }

    $this->select_db($config['database']);
    mysql_set_charset($this->connection, $config['charset']);

    return $this->connection;
  }

  /**
   * Select Database
   *
   * @param string $database
   * @return boolean
   */
  public function selectDb($database = '')
  {
    return mysql_select_db($this->connection, $database);
  }

  /**
   * Check the availability of the database connection
   *
   * @return \MySQLi
   */
  public function check()
  {
    if (empty($this->connection) || !mysqli_ping($this->connection))
    {
      $this->connect();
    }

    return $this->connection;
  }

  /**
   * Excute query
   *
   * @param string $query
   * @return array
   */
  protected function exec($query)
  {
    $this->check();
    array_push($this->logs, $query);

    return mysql_query($this->connection, $query);
  }

  /**
   * Query
   *
   * @param string $query
   * @return array
   */
  public function query($query)
  {
    $data = array();
    $result = $this->exec($query);

    if ($this->affectedRows() > 0)
    {
      $data = $result->fetch_all();
      $result->free();
    }
      else if (preg_match('/^select/i', trim($query)))
    {
      return null;
    }
      else
    {
      return true;
    }

    return $data;
  }

  /**
   * Produce where clause
   *
   * @param string $where
   * @return string
   */
  protected function where($where)
  {
    $clause = '';

    if (is_array($where))
    {
      //
    }
  }

  /**
   * Insert data to the table
   *
   * @param string $table
   * @param array  $data
   * @return int
   */
  public function insert($table, $data)
  {
    if (!is_array($data))
    {
      $data = array($data);
    }

    $conditions = array();

    foreach ($data as $key => $item)
    {
      $conditions[] = '`' . $key . '` = "' . $this->escape_value($item) . '"';
    }

    $sql = 'INSERT INTO `' . $table . '` SET ' . implode(', ', $conditions) . ';';

    if ($this->exec($sql))
    {
      return $this->affectedRows();
    }
      else
    {
      return 0;
    }
  }

  /**
   * Delete data form table
   *
   * @return int
   */
  public function delete($table, $where)
  {
    return $this->exec('DELETE FROM `' . $table . '`' . $this->where($whre));
  }

  /**
   * Filter value
   *
   * @param string $value
   * @return string
   */
  protected function quoteValue(value)
  {
    return '\'' . str_replace(array('\'', '\\'), array('\'\'', '\\\\'), $string) . '\'';
  }

  /**
   * Fitler cloumn
   *
   * @param string $value
   * @return string
   */
  public function quoteColumn($value)
  {
    return '`' . $value . '`';
  }

  /**
   * Get affected rows
   *
   * @param mixed $handle
   * @return int
   */
  public function affectedRows($handle = null)
  {
    $handle = $handle ? $handle : $this->connection;

    return mysql_affected_rows($handle);
  }

  /**
   * Last insert id
   *
   * @param mixed $handle
   * @return int
   */
  public function lastInsertId($handle = null)
  {
    $handle = $handle ? $handle : $this->connection;
  }

  /**
   * Get error text
   *
   * @return string
   */
  public function error()
  {
    return mysql_error($this->connection);
  }

  /**
   * Get error number
   *
   * @return int
   */
   public function errno()
   {
     return mysql_errno($this->connection);
   }

  /**
   * Get the version of MySQL Server
   *
   * @return string
   */
  public function version()
  {
    return mysql_get_server_info($this->connection);
  }

  /**
   * Close the connection
   *
   * @return boolean
   */
  public function close()
  {
    return mysql_close($this->connection);
  }
}
