<?php
class dbLink
{
	private $conn;
	function __construct($dbnm)
	{
		$lines = file('topsecret');
		//$lines = file('topsecret');
		$host = trim($lines[0]);
		$user = trim($lines[1]);
		$pass = trim($lines[2]);
		$dbnm = trim($dbnm);
		$this->conn = mysqli_connect($host,$user,$pass,$dbnm) or die("Error connecting to SQL: " . mysqli_error($conn));

	}
	function __destruct()
	{
		mysqli_close($this->conn);
	}
	function query($query)
	{
		$this->result = mysqli_query($this->conn, $query) or die('Query failed: '. mysqli_error($conn));
		return $this->result;
	}
  function emptyResult($result){
    return (mysqli_num_rows($this->result) > 0) ? true : false;
  }
	function getlink(){
		return $this->conn;
	}
}


?>
