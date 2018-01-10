<?php

class SearchForDrugs implements ISearchable {

    private $dbconn;

    public function __construct(){
        $_dbconn = new db();
        $_dbconn->connect();
        $this->dbconn = $_dbconn;
    }

    public function search($searchstring){
        $array = [];
        $sql = "SELECT * FROM drugs_office WHERE  name LIKE '%" . $searchstring. "%' ";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }
}