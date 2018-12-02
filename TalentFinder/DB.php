<!-- The database class for making queries, adding, modifying and deleting -->
<?php

class DB
{
    private $db;
    private $dbDir = "sqlite:localization.db";
    
	// constructor, throws exception if database path is invalid or erronous
    public function __construct()
    {
        try
        {
            $this->db = new PDO($this->dbDir);
        }
        catch(PDOException $e)
        {
            throw new Exception("FATAL: could not create database!",0,$e);
        }
    }
    
	//use this function to make an SQL query, checks for errors on query
    public function query($sql)
    {
        $resultset = $this->db->query($sql);
        $records = $resultset->fetchAll(PDO::FETCH_ASSOC);
        $resultset->closeCursor();
        
        return $records;
    }
	//use this function for any other SQL processing doesnt check for errors
	public function insert($sql)
	{
		$this->db->exec($sql);
	}
	//returns the instance of the database, use for raw database commands
    public function getDB()
    {
        return $this->db;
    }
}
?>
