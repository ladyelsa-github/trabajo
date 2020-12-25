<?php
class Database
{
  private $host;
  private $port;
  private $name;
  private $username;
  private $password;
  private $pdo;
  private $sQuery;
  private $parameters;
  private $connectionStatus;
  public $rowCount = 0;
	public $columnCount = 0;
	public $querycount = 0;

  public function __construct($host, $port, $name, $username, $password) {
    $this->host = $host;
		$this->port = $port;
		$this->name = $name;
		$this->username = $username;
    $this->password = $password;
    $this->parameters = array();
    $this->connect();
  }

private function connect() {
    try {
      $dsn = 'mysql:host='.$this->host.';'. 'port='.$this->port.';'.'dbname='.$this->name;
      //echo $dsn;
      $this->pdo = new PDO($dsn, $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

      # We can now log any exceptions on Fatal error. 
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			# Disable emulation of prepared statements, use REAL prepared statements instead.
      $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
      
      $this->connectionStatus = true;
    } catch (PDOException $e) {
      throw $e;
    }
  }

  public function closeConnection() {
    $this->pdo = null;
  }
  
  private function init($query, $parameters = null, $driverOptions = array()) {
		if (!$this->connectionStatus) {
			$this->connect();
		}
		try {
			$this->parameters = $parameters;
			$this->sQuery = $this->pdo->prepare($this->buildParams($query, $this->parameters), $driverOptions);
			if (!empty($this->parameters)) {
				if (array_key_exists(0, $parameters)) {
					$parametersType = true;
					array_unshift($this->parameters, "");
					unset($this->parameters[0]);
				} else {
					$parametersType = false;
				}
				foreach ($this->parameters as $column => $value) {
					$this->sQuery->bindParam($parametersType ? intval($column) : ":" . $column, $this->parameters[$column]); //It would be query after loop end(before 'sQuery->execute()').It is wrong to use $value.
				}
			}

			if (!isset($driverOptions[PDO::ATTR_CURSOR])) {
                $this->sQuery->execute();
            }
			$this->querycount++;
		}
		catch (PDOException $e) {
      throw $e;
		}

		$this->parameters = array();
	}

  private function buildParams($query, $params = null) {
		if (!empty($params)) {
			$array_parameter_found = false;
			foreach ($params as $parameter_key => $parameter) {
				if (is_array($parameter)){
					$array_parameter_found = true;
					$in = "";
					foreach ($parameter as $key => $value){
						$name_placeholder = $parameter_key."_".$key;
						// concatenates params as named placeholders
             $in .= ":".$name_placeholder.", ";
						// adds each single parameter to $params
						$params[$name_placeholder] = $value;
					}
					$in = rtrim($in, ", ");
					$query = preg_replace("/:".$parameter_key."/", $in, $query);
					// removes array form $params
					unset($params[$parameter_key]);
				}
			}

			// updates $this->params if $params and $query have changed
			if ($array_parameter_found) $this->parameters = $params;
		}
		return $query;
  }
  
  public function query($query, $params = null, $fetchMode = PDO::FETCH_ASSOC) {
    $query = trim($query);
		$rawStatement = preg_split("/( |\r|\n)/", $query);
		$this->init($query, $params);
		$statement = strtolower($rawStatement[0]);
		if ($statement === 'select' || $statement === 'show' || $statement === 'call' || $statement === 'describe') {
			return $this->sQuery->fetchAll($fetchMode);
		} elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
			return $this->sQuery->rowCount();
		} else {
			return NULL;
		}
  }
  
  public function insert($tableName, $params = null) {
		$keys = array_keys($params);
		$rowCount = $this->query(
			'INSERT INTO `' . $tableName . '` (`' . implode('`,`', $keys) . '`)
			VALUES (:' . implode(',:', $keys) . ')',
			$params
		);
		if ($rowCount === 0) {
			return false;
		}
		return $this->lastInsertId();
  }

  public function update($tableName, $params = array(), $where = array()) {
    $rowCount = 0;
    if (!empty($params)) {
      $updColStr = '';
      $whereStr = '';
      $updatePara = array();
      
      // Build update statement
      foreach ($params as $key => $value) {
        $updColStr .= "{$key}=?,";
      }
      
      $updColStr = substr($updColStr, 0, -1);
      $dbQuery = "UPDATE {$tableName}
                  SET {$updColStr}";
        // where condition
        if (is_array($where)) {
          foreach ($where as $key => $value) {
            // Is there need to add "OR" condition?
            $whereStr .= "AND {$key}=?";
        }
          $dbQuery .= " WHERE 1=1 {$whereStr}";
          $updatePara = array_merge(array_values($params), array_values($where));
        } else {
          $updatePara = array_values($params);
        }
        $rowCount = $this->query($dbQuery, $updatePara);
      }
      return $rowCount;
    }

  public function lastInsertId() {
    return $this->pdo->lastInsertId();
  }

  public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {
    $this->init($query, $params);
    $resultRow = $this->sQuery->fetch($fetchmode);
    $this->rowCount = $this->sQuery->rowCount();
    $this->columnCount = $this->sQuery->columnCount();
    $this->sQuery->closeCursor();
    return $resultRow;
  }
  
  public function column($query, $params = null) {
		$this->Init($query, $params);
		$resultColumn = $this->sQuery->fetchAll(PDO::FETCH_COLUMN);
		$this->rowCount = $this->sQuery->rowCount();
		$this->columnCount = $this->sQuery->columnCount();
		$this->sQuery->closeCursor();
		return $resultColumn;
	}
}