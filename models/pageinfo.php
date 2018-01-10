<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 24.11.2017
 * Time: 10:22
 */
trait PageInfo {

    public function getCities(){
        $array = [];
        $sql = "SELECT Translit FROM cities WHERE length(Translit) > 0;";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_row($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function getMetro(){
        $array = [];
        $sql = "SELECT Translit FROM metro WHERE length(Translit) > 0;";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_row($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function getAddress(){
        $array = [];
        $sql = "SELECT transliterate(StoreAdres) FROM stores WHERE length(StoreAdres) > 0;";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_row($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function getStorePageInfo($url){
        #Строка подразумевается как $url=substr($_SERVER['REQUEST_URI'],1, strpos($_SERVER['REQUEST_URI'],"?")-2),
        #То есть "/" в начале и в конце не нужны, т.к. их нет в базе
        $nodes=['vremya_raboty','spravochnaya_po_aptekam','telefony','kruglosutochnye','otkrytye_seychas'];
        $array = [];
        $sql = "SELECT `PageTitle`, `PageText`, `PageDescription`, `PageKeywords` 
                FROM pages 
                WHERE PageUrl = '".$url."';";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $array['PageTitle'] = addslashes($row['PageTitle']);
                $array['PageText'] = addslashes($row['PageText']);
                $array['PageDescription'] = addslashes($row['PageDescription']);
                $array['PageKeywords'] = addslashes($row['PageKeywords']);
            }
        } elseif (strlen($url) > 0){
            #Не повезло, будем подбирать
            $urlarr = explode("/",$url);
            $c = isset ($urlarr[1]) ? $urlarr[1] : 'moskva';
            #Ищем город
            $sql = "SELECT Translit,Genitive,Prepositional FROM cities WHERE Translit='".$c."';";
            $sql = $this->dbconn->query($sql);
            if($this->dbconn->num_rows($sql) > 0) {
                while ($row = $this->dbconn->fetch_assoc($sql)) {
                    $search = ['*genitive*','*prepositional*'];
                    $replace = [addslashes($row['Genitive']),addslashes($row['Prepositional'])];
                }
                #Нашли город, ищем, какие нужны тексты
                if (count($urlarr)>2){
                    if (in_array($urlarr[2], $nodes)
                        || strpos($urlarr[2],'metro-') == 0
                        || strpos($urlarr[2],'address-') == 0) {
                            if (count($urlarr)>3){
                                $url = $urlarr[0].'/*';
                            } else {
                                $url = $urlarr[0].'/*/'.$urlarr[2];
                            }
                    } elseif ($urlarr[2] == 'otkrytye_seychas') {
                        $url = $urlarr[0].'/*';
                    }
                } else {
                    $url = $urlarr[0].'/*';
                }

                $sql = "SELECT `PageTitle` , `PageText`, `PageDescription`, `PageKeywords` 
                        FROM pages 
                        WHERE LENGTH(PageUrl) > 0 AND PageUrl = '".$url."';";

                $sql = $this->dbconn->query($sql);
                if ($this->dbconn->num_rows($sql) > 0) {
                    while ($row = $this->dbconn->fetch_assoc($sql)) {
                        $array['PageTitle'] = str_replace($search,$replace,addslashes($row['PageTitle']));
                        $array['PageText'] = str_replace($search,$replace,addslashes($row['PageText']));
                        $array['PageDescription'] = str_replace($search,$replace,addslashes($row['PageDescription']));
                        $array['PageKeywords'] = str_replace($search,$replace,addslashes($row['PageKeywords']));
                        break;
                    }
                }
            }
        }

        return $array;
    }
}