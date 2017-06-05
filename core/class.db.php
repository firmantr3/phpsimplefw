<?php
/* ------------------------------------------------------------------
 * NoML Framework
 * By : Firman.T.N - Owi - Wahyu
 * ------------------------------------------------------------------
 * Author   : Firman T. Nugraha (firmantr3@gmail.com) 
 */

/**
* Database Engine
*/
class db extends PDO{

	private $result;
	private $rowcount;

	public function __construct($engine,$host,$database,$user,$pass)
	{
		/*try {*/
			$dns = $engine.':dbname='.$database.";host=".$host;
			parent::__construct( $dns, $user, $pass );
		    parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		/*}
		catch (PDOException $e) {
		    error_log('PDO Exception: '.$e->getMessage());
		    die('Error : '.$e);
		}*/
	}
	

	public function status()
	{
		return parent::getAttribute(PDO::ATTR_CONNECTION_STATUS);
	}

	/* INSERT VALUES TO TABLE */

	public function insert($table,$rows=null, $ban = array("konfirmasi_password"))
	{
		$command = 'INSERT INTO '.$table;
		$row = null;
		$subs = null;
		$values = array();

		foreach($rows as $key => $val)
		{
			if((!in_array($key,$ban,true)) && $val != null && $val != '') {
				$row .=",".$key;
				$subs .=",?";
				$values[] = $val;
			}
		}

		$command .="(".substr($row,1).")";
		$command .="VALUES(".substr($subs,1).")";
		
		$stmt = parent::prepare($command);
		$stmt->execute($values);
		$rowcount = $stmt->rowCount();
		return $rowcount;
	}

	public function insert_auto($table,$rows=null)
	{
		$query = parent::prepare("show columns from $table");
		$query->execute();
		$availableFields = array();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$availableFields[] = $row['Field'];
		}
		
		$command = 'INSERT INTO '.$table;
		$row = null;
		$subs = null;
		$values = array();

		foreach($rows as $key => $val)
		{
			if((in_array($key,$availableFields)) && $val != null && $val != '') {
				$row .=",".$key;
				$subs .=",?";
				$values[] = $val;
			}
		}

		$command .="(".substr($row,1).")";
		$command .="VALUES(".substr($subs,1).")";
		
		$stmt = parent::prepare($command);
		$stmt->execute($values);
		$rowcount = $stmt->rowCount();
		return $rowcount;
	}


	public function delete($tabel,$where=null)
    {
        $command = 'DELETE FROM '.$tabel;
       
        $list = Array(); 

		$param = array();

        if($where != null)
        {
	        if(is_array($where))
	        {
				foreach ($where as $key => $value)
	            {
	              $list[] = "$key = :$key";
	              $param[":$key"] = str_replace("'","",$value);
	            }
	            
	            $command .= ' WHERE '.implode(' AND ',$list);      	
	        }
	        else
	        {
	        	$command .= ' WHERE '.$where;
	        }
        }     
          
        $query = parent::prepare($command);
        $query->execute($param);
        $rowcount = $query->rowCount();
		return $rowcount;
    }

	/*UPDATE VALUES*/
	public function update($table, $field = null, $where = null, $ban = array("konfirmasi_password"))
	{
		$command = 'UPDATE '.$table.' SET ';

		$param = array();

		$set = null;
		foreach ($field as $key => $values) {
			if(!in_array($key,$ban,true)) {
				$set .= ','.$key.' = :'.$key;
				$param[":$key"] = $values;
			}
			
		}


		$command .= substr(trim($set), 1);

		
        if($where != null){
			if(is_array($where))
	        {
	        	$list = Array(); 
				foreach ($where as $key => $val)
	            {
	              $list[] = "$key = :$key";
	              $param[":$key"] = str_replace("'","",$val);
	            }

	            $command .= ' WHERE '.implode(' AND ',$list);
			}
	        else
	        {
	        	
	        	$command .= ' WHERE '.$where;
	        }
    	}
		$query = parent::prepare($command);
		$query->execute($param);
		$rowcount = $query->rowCount();
		return $rowcount;
	}
	
	
	public function update_auto($table, $field = null, $where = null)
	{
		$query = parent::prepare("show columns from $table");
		$query->execute();
		$availableFields = array();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$availableFields[] = $row['Field'];
		}
		
		$command = 'UPDATE '.$table.' SET ';

		$param = array();

		$set = null;
		foreach ($field as $key => $values) {
			if(in_array($key,$availableFields)) {
				$set .= ','.$key.' = :'.$key;
				$param[":$key"] = $values;
			}
			
		}


		$command .= substr(trim($set), 1);

		
        if($where != null){
			if(is_array($where))
	        {
	        	$list = Array(); 
				foreach ($where as $key => $val)
	            {
	              $list[] = "$key = :$key";
	              $param[":$key"] = str_replace("'","",$val);
	            }

	            $command .= ' WHERE '.implode(' AND ',$list);
			}
	        else
	        {
	        	
	        	$command .= ' WHERE '.$where;
	        }
    	}
		$query = parent::prepare($command);
		$query->execute($param);
		$rowcount = $query->rowCount();
		return $rowcount;
	}

	/*SELECT DATA FROM TABLE*/
	public function select($table, $rows = null, $where = null, $order = null, $limit = null)
	{

		if($rows != null)
		{
			if(is_array($rows))
			{
				$rows = implode(',',$rows);	
			}
		}
		else
		{
			$rows = '*';	
		}
		
		$command = 'SELECT '.$rows.' FROM '.$table;
		
		$list = Array(); 

		$param = array();

        if($where != null)
        {
	        if(is_array($where))
	        {
				foreach ($where as $key => $value)
	            {
	              $list[] = "$key = :$key";
	              $param[":$key"] = str_replace("'","",$value);
	            }
	            
	            $command .= ' WHERE '.implode(' AND ',$list);      	
	        }
	        else
	        {
	        	$command .= ' WHERE '.$where;
	        }
        }     
		
		if($order != null)
			$command .= ' ORDER BY '.$order;
		if($limit != null)
			$command .= ' LIMIT '.$limit;
			
		$query = parent::prepare($command);
		$query->execute($param);
		$this->rowcount = $query->rowCount();

		if($query->rowCount() == 1)
		{
			$row = $query->fetch(PDO::FETCH_ASSOC);
			return $this->result = $row;			
		}
		else
		{
			$posts = array();
			while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
			{
				$posts[] = $row;
			}

			return $this->result = $posts;
		}
		
		//return $this->result = json_encode(array('post'=>$posts));
	}


	public function query($command,$by_id = null)
	{
		try {
			$query = parent::prepare($command);
			$query->execute();
			$this->rowcount = $query->rowCount();
			if(stripos(trim($command),"select") == 0) {
		
					$posts = array();
					while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
					{
						if($by_id == null) {
							$posts[] = $row;
						}
						else {
							$posts[$row[$by_id]] = $row;
						}
					}
		
					return $this->result = $posts;
			}
		}
		catch(Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		
		//return $this->result = json_encode(array('post'=>$posts));
	}

	public function getResult()
	{
		return $this->result;
	}

	public function rowcount()
	{
		return $this->rowcount;
	}
	
	public function last_id($table,$id) {
		$query = parent::prepare("select max($id) as last_id from $table");
		$query->execute();
		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row['last_id'];
	}
}
?>