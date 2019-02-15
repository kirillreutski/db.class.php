<?php
include "config.php"; // <-- here we store array $sql with server, user, pass and db keys for MySQL connection
class db {
  private $host;
  private $user;
  private $pwd;
  private $db;
  public $con;
  public $cValue;
  public $name;
  public $parent;
  public $and;
  function __construct($t = "", $con = ""){
    if ($con != "")
      $this->con = $con;
    else {
      global $sql;
      $this->host = $sql['server'];
      $this->user = $sql['user'];
      $this->pwd  = $sql['pass'];
      $this->db   = $sql['db'];
      $this->con = mysqli_connect($this->host,$this->user,$this->pwd ,$this->db);
    }

    $this->parent = $t;


  }
  function rand(){
    echo "rand function executed!";
  }
  function __call($method_name, $args){
    $this->name = $method_name;
    if (count($args)==1){
      $this->cValue = $args[0];
      $this->and = " AND ";
    }

    else if (count($args)==2){
      $this->cValue = $args[0];
      $this->and = " ".$args[1]." ";
    }



    else{
      $this->cValue = "";
      $this->and = " AND ";
    }


    if ($method_name == 'get'){
      $returning_one_column = false;
      $searching_by_id = false;
      // GET:
      // if two calls, then we get all columns
      // if three columns, then we get specific column
      // SELECT [columns] from [table] WHERE [ARRAY|STRING], if STRING then id = STRING
      $query_data = $this->finalize();
      $query = "";
      if (count($query_data) == 2){
        foreach ($query_data[0] as $table => $em)
          $query = "SELECT * FROM $table";
        $query .= " WHERE ";
        $whereArray = array();

        if (gettype($query_data[1]['get']) == "array"){
          foreach($query_data[1]['get'] as $column => $value){
            $whereArray[] = "$column = '$value'";
          }
        }
        else {
          if (strlen($query_data[1]['get'])>0){
            $whereArray[] = "id = '".$query_data[1]['get']."'";
            $searching_by_id = true;
          }

          else
            $whereArray[] = "1 LIMIT 100";
        }
        $query .= implode($this->and, $whereArray);
        $r = mysqli_query($this->con, $query);
        if (($searching_by_id)&&($returning_one_column))
          return mysqli_fetch_row($r)[0];
        else {
          if ($searching_by_id)
            return mysqli_fetch_all($r, MYSQLI_ASSOC)[0];
          else return mysqli_fetch_all($r, MYSQLI_ASSOC);
        }

      }
      else if (count($query_data) == 3){
        foreach ($query_data[0] as $table => $em)
          $query = " FROM $table";
        $whereArray = array();
        if (isset($query_data[1]['columns'])){// getting several columns
          $query = "SELECT ".$query_data[1]['columns']." ".$query." WHERE ";
        }
        else { //getting one column
          foreach($query_data[1] as $column=>$em){
            // do nothing
          }
          $returning_one_column = true;
          $query = "SELECT $column ".$query." WHERE ";
        }

        if (gettype($query_data[2]['get']) == "array"){
          foreach($query_data[2]['get'] as $column => $value){
            $whereArray[] = "$column = '$value'";
          }
        }
        else {
          $searching_by_id = true;
          $whereArray[] = "id = '".$query_data[2]['get']."'";
        }
        $query .= " ".implode($this->and, $whereArray);
      }
      $r = mysqli_query($this->con, $query);
      if (($searching_by_id)&&($returning_one_column))
        return mysqli_fetch_row($r)[0];
      else {
        if ($searching_by_id)
          return mysqli_fetch_all($r, MYSQLI_ASSOC)[0];
        else return mysqli_fetch_all($r, MYSQLI_ASSOC);
      }

      echo $query.PHP_EOL;
    }
    else if ($method_name == 'set'){
      // create update query
      // SET:
      // UPDATE [table] SET x=y WHERE [STRING|ARRAY]
      // always 3 params
      $query_data = $this->finalize();
      foreach($query_data[0] as $table=>$em){
        // do nothing
      }
      $query = "UPDATE $table SET ";

      if (isset($query_data[1]['values'])){// setting several values
        $tempArray = $query_data[1]['values'];
        foreach($tempArray as $key => $value){
          $tempArray[$key] = "$key = '".$value."'";
        }
        $query .= implode(', ', $tempArray)." ";
      }
      else { //setting one value
        foreach($query_data[1] as $column=>$value){
          // do nothing
        }
        $query .= " $column = '$value' ";
      }
      $query .= " WHERE ";

      if (gettype($query_data[2]['set']) == 'array'){
        $tempArray = $query_data[2]['set'];
        foreach($tempArray as $key => $value){
          $tempArray[$key] = "$key = '".$value."'";
        }
        $query .= implode($this->and, $tempArray);

      }
      else {
        $query .= " id = ".$query_data[2]['set'];
      }
      $r = mysqli_query($this->con, $query);
      return mysqli_affected_rows($this->con);
      //echo $query.PHP_EOL;
    }
    else if ($method_name == 'insert'){
      $query_data = $this->finalize();
      // always three params
      foreach($query_data[0] as $table=>$em){
        // do nothing
      }
      $query = "INSERT into $table SET ";
      // setting several values
      $tempArray = $query_data[1]['values'];
      foreach($tempArray as $key => $value){
        $tempArray[$key] = "$key = '".$value."'";
      }
      $query .= implode(', ', $tempArray)." ";
      $r = mysqli_query($this->con, $query);
      return mysqli_insert_id($this->con);
      //echo $query.PHP_EOL;
    }
    else if ($method_name == 'delete'){
      // always 2 params
      $query_data = $this->finalize();
      foreach($query_data[0] as $table=>$em){
        // do nothing
      }
      $query = "DELETE from $table WHERE ";
      if (gettype($query_data[1]['delete']) == 'array'){
        $tempArray = $query_data[1]['delete'];
        foreach($tempArray as $key => $value){
          $tempArray[$key] = "$key = '".$value."'";
        }
        $query .= implode($this->and, $tempArray);

      }
      else {
        $query .= " id = ".$query_data[1]['delete'];
      }
      //echo $query.PHP_EOL;
      $r = mysqli_query($this->con, $query);
      return mysqli_affected_rows($this->con);
    }
    else {
      return new db($this, $this->con);
    }

  }
  function finalize(){
    $outArray = array();
    $iterator = $this;

    do {
      $outArray[] = array($iterator->name=>$iterator->cValue);
      $iterator = $iterator->parent;
    } while ($iterator != "");

    return array_reverse($outArray);
  }

}
