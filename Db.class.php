<?php
/**
 * Description of db.class
 * 
 * This class uses the PHP 5 PDO database connection to connect to mysql database and has a set of
 * methods to execute SQL commands. 
*
 * @author Brian Wilhite
 */
\ini_set('default_charset', 'UTF-8');

class Db {
    
    private $db;

    public function _connect() {
        
        $dsn = 'DSN';
        $username = 'DB_USER';
        $passwd = 'DB_PASS';
        
        // Set options
        $options = [ PDO::ATTR_PERSISTENT          => true,
                         PDO::MYSQL_ATTR_INIT_COMMAND  => 'SET NAMES utf8',
                         PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION
         ];
                
        // connection to database
        try{     
                // Pass connection
                $dbh = new PDO($dsn, $username, $passwd, $options);
                $this->db = $dbh;
              
        }catch (Exception $e){
           
            print  '<pre><strong>' .get_class($e).' inside the exception handler. Message: '.
                   $e->getMessage().' on line '.$e->getLine().'</pre></strong>';
        }
 
}

public function _getConnection(){
    return $this->db;
}

public function imhungry(){
    echo 'Butt Fuck';
}

public function _query($query){
    $this->db->prepare($query);
}

public function _bind($param, $value, $type = null){
    if (is_null($type)) {
        switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
        }
    }
    $this->_query(bindValue($param, $value, $type));
}

public function _execute(){
    return $this->_query(execute());
}

public function _resultset(){
    $this->execute();
    return $this->_query(fetchAll(PDO::FETCH_ASSOC));
}

public function _rowCount(){
    return $this->_query(rowCount());
}

public function _lastInsertId(){
    return $this->_query(lastInsertId());
}

public function _beginTransaction(){
    return $this->_query(beginTransaction());
}

public function _endTransaction(){
    return $this->_query(commit());
}

public function _cancelTransaction(){
    return $this->_query(rollBack());
}

public function _debugDumpParams(){
    return $this->_query(debugDumpParams());
}

public function _list_columns($table = ''){
    $list_cols = $this->query("SHOW COLUMNS FROM ".$table);
    return $this->_execute($list_cols);
}

function _insert($table, $keys, $values){
    $insert = $this->query("INSERT INTO ".$table." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")");
    return $this->_execute($insert);
}

function _update($table, $values, $where, $orderby = array(), $limit = FALSE){
    
    foreach ($values as $key => $val){
        $valstr[] = $key." = ".$val;
    }

    $limit = ( ! $limit) ? '' : ' LIMIT '.$limit;

    $orderby = (count($orderby) >= 1)?' ORDER BY '.implode(", ", $orderby):'';

    $sql = "UPDATE ".$table." SET ".implode(', ', $valstr);

    $sql .= ($where != '' AND count($where) >=1) ? " WHERE ".implode(" ", $where) : '';

    $sql .= $orderby.$limit;

    $update = $this->_query($sql);
    return $this->_execute($update);
}

function _truncate($table){
    $trunc = $this->_query($this->_delete($table));
    return $this->execute($trunc);
}

function _delete($table, $where = array(), $like = array(), $limit = FALSE){
    $conditions = '';

        if (count($where) > 0 OR count($like) > 0){
            $conditions = "\nWHERE ";
            $conditions .= implode("\n", $this->ar_where);

            if (count($where) > 0 && count($like) > 0){
                $conditions .= " AND ";
	    }
	    $conditions .= implode("\n", $like);
	}

            $limit = ( ! $limit) ? '' : ' LIMIT '.$limit;

	$del = $this->_query("DELETE FROM ".$table.$conditions.$limit);
        return $this->_execute($del);
}


function _limit($sql, $limit, $offset){
    
   $sql .= "LIMIT ".$limit;
        if ($offset > 0){
        $sql .= " OFFSET ".$offset;
    }
    $_limit = $this->_query(($sql));
    return $this->_execute($_limit);
}

function _fetch_col(){

    $col = $this->_query((fetchAll(FETCH_COLUMN)));
    
    foreach( $col  as $row) {
    print $this->_execute($row);
    
}
}
}   
/* End of file database.php */
/* Location: php/database.php */


