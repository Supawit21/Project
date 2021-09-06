<?php
   class DB{
      var $conn;
      var $result;
      function __construct(){
         $this->conn = mysqli_connect("localhost","root","abc123","project");
         // เชื่อมต่อ error //
         if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
         }
         mysqli_set_charset($this->conn,"utf8");
      }
      function insert($table,$fields){
         // ex insert //
         // INSERT INTO table (att1,att2) VALUES ('att1','att2'); //
         $query = "INSERT INTO ".$table." (".implode(",",array_keys($fields)).")";
         $query .= " VALUES ('".implode("','",$fields)."');";
         // echo $query;
         if(mysqli_query($this->conn, $query))  
         {  
            return true;  
         }
         else{
            die("Query error:" . mysqli_error($this->conn));
         }
      }
      function query($txtSQL){
         $this->result = mysqli_query($this->conn,$txtSQL);
         if(!$this->result){
            die("Query error:" . mysqli_error($this->conn));
         }
         return $this->result;
      }
      function update($table, $fields,$where_condition)  
      {  
         $query = '';
         $condition = '';
         /// ทำ string field กับ value ของ ตาราง ///
         foreach($fields as $key => $value)  
         {  
            $query .= $key . "='".$value."', ";  
         }  
         $query = substr($query, 0, -2);  
         /// ทำตัวเงื่อนไข ของ คำสั่ง query ///
         foreach($where_condition as $key => $value)  
         {  
            $condition .= $key . "='".$value."' AND ";  
         }  
         $condition = substr($condition, 0, -5);  
         $query = "UPDATE ".$table." SET ".$query." WHERE ".$condition.""; 
         // echo $query;
         if(mysqli_query($this->conn, $query))  
         {  
            return true;  
         }else{
            die("Query error:" . mysqli_error($this->conn));
         } 
      }  
     
}

