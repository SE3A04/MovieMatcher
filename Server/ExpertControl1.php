<?php

	class ExpertControl1 {
		var $servername ;
		var $username ;
		var $password ;
		var $dbname ;
		var $conn ;

		function _construct () {
			$this->servername = "localhost" ;
			$this->username = "root" ;
			$this->password = "bitnami" ;
			$this->dbname = "experts" ;
			$this->conn = new mysqli($this->servername,$this->username,$this->password,$this->dbname) ;

			if (($this->conn)->connect_error) {
				die("Connection Failed: " . ($this->conn)->connect_error) ;
			}
		}


		function getResults($nameString) {
			$names = explode(",",$nameString) ;
			$sql = bindInValues($names) ;
			$res = ($this->conn)->query($sql) ;
			$this->conn->close() ;

			if ($row = $res->fetch_row()) {	return $row[0] ; }
		}

		function bindInValues(array $values) {
			$sql = sprintf('SELECT DISTINCT m.movie_name,
			(SELECT count(*) as cnt from character_members m2 WHERE m2.movie_name=m.movie_name AND character_members IN (%s)) as order_col
			FROM character_members m
			ORDER BY order_col DESC
			LIMIT 1;',implode(', ', array_fill(0, count($values), '?')));

			$stmt = ($this->conn)->prepare($sql);

			foreach ($values as $value) {
				$stmt->bind_param('s', $value);
			}
			return $stmt;
		}

	}

?>
