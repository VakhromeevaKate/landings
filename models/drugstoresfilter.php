<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27.11.2017
 * Time: 13:55
 */

class DrugstoresFilter
{
    private $dbconn;
    private $city;
    private $region;
    private $genitive;
    private $prepositional;
    private $metro;
    private $address;
    private $info;
    private $worktime;
    private $phones;
    private $opened;
    private $everytime;
    private $zoom;
    private $mapcenter;

    public function __construct(){
        $_dbconn = new db();
        $_dbconn->connect();
        $this->dbconn = $_dbconn;
        $this->setFiltersFromURI();
    }

    public function setFiltersFromURI(){
        $uri = explode('/',substr($_SERVER['REQUEST_URI'],1,(substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1)=='/' ? strlen($_SERVER['REQUEST_URI'])-2 : strlen($_SERVER['REQUEST_URI'])-1)));
        if (count($uri) == 1) {
            $uri[1] = 'moskva';
        }

        if (count($uri)>1){
            $this->setCyrillicCity($uri[1]);
            $this->setCyrillicRegion($uri[1]);
            $this->setGenitive($uri[1]);
            $this->setPrepositional($uri[1]);
            $this->setCityMapCenter($uri[1]);

            for ($i=2;$i<count($uri);$i++){
                switch ($uri[$i]){
                    case 'spravochnaya_po_aptekam': $this->info     = "true";
                        break;
                    case 'vremya_raboty':           $this->worktime = "true";
                        break;
                    case 'telefony':                $this->phones   = "true";
                        break;
                    case 'otkrytye_seychas':        $this->opened   = "true";
                        break;
                    case 'kruglosutochnye':         $this->everytime = "true";
                        break;
                }
                if (substr($uri[$i],0,5) == "metro"){
                    $this->setCyrillicMetro(substr($uri[$i],6,strlen($uri[$i]) - 6));
                }
                if(substr($uri[$i],0,7) == "address"){
                    $this->setCyrillicAddress(substr($uri[$i],9,strlen($uri[$i]) - 9));
                }

            }
            $this->setCityZoomFilter();
        }
        return $this;
    }

    #Getters
    public function getCity(){
        return $this->city;
    }

    public function getRegion(){
        return $this->region;
    }

    public function getGenitive(){
        return $this->genitive;
    }

    public function getPrepositional(){
        return $this->prepositional;
    }

    public function getMetroStation(){
        return $this->metro;
    }

    public function getAddress(){
        return $this->address;
    }

    public function getInfo(){
        return $this->info;
    }

    public function getWorkTime(){
        return $this->worktime;
    }

    public function getOpened(){
        return $this->opened;
    }

    public function getEveryTime(){
        return $this->everytime;
    }

    public function getZoom(){
        return $this->zoom;
    }

    public function getMapCenter(){
        return $this->mapcenter;
    }

    #Setters
    public function setCity($city){
        $this->city = $city;
    }

    public function setRegion($region){
        $this->region = $region;
    }

    public function setMetroStation($metro){
        $this->metro = $metro;
    }

    public function setAddress($address){
        $this->address = $address;
    }

    public function setOpened($opened){
        $this->opened = $opened;
    }
    public function setZoom($zoom){
        $this->zoom = $zoom;
    }

    public function setMapCenter($coordinates){
        $this->mapcenter = $coordinates;
    }

    public function setEveryTime($everytime){
        $this->everytime = $everytime;
    }
    
    #Other methods
    public function toArray(){
        $array['city'] = $this->getCity();
        $array['region'] = $this->getRegion();
        $array['genitive'] = $this->getGenitive();
        $array['prepositional'] = $this->getPrepositional();
        $array['metro'] = $this->getMetroStation();
        $array['address'] = $this->getAddress();
        $array['info'] = $this->getInfo();
        $array['opened'] = $this->getOpened();
        $array['everytime'] = $this->getEveryTime();
        $array['zoom'] = $this->getZoom();
        $array['mapcenter'] = $this->getMapCenter();

        return $array;
    }

    public function setGenitive($city){
        $sql = "SELECT Genitive FROM cities WHERE Translit = '".$city."'";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $this->genitive = $row['Genitive'];
            }
        } else $this->genitive = "";
    }

    public function setPrepositional($city){
        $sql = "SELECT Prepositional FROM cities WHERE Translit = '".$city."'";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $this->prepositional = $row['Prepositional'];
            }
        } else $this->prepositional = "";
    }

    public function setCyrillicCity($city){
        $sql = "SELECT CityName FROM cities WHERE Translit = '".$city."'";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $this->city = $row['CityName'];
            }
        } else $this->city = "";
    }

    public function setCyrillicRegion($city){
        $sql = "SELECT MAX(r.RegionName) as RegionName FROM cities c INNER  JOIN regions r ON c.RegionId = r.RegionId 
                WHERE c.Translit = '".$city."';";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $this->region = $row['RegionName'];
            }
        } else $this->region = "";
    }

    public function setCyrillicMetro($metro){
        $sql = "SELECT MAX(MetroName) as MetroName FROM metro WHERE Translit = '".$metro."'";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $this->setMetroStation($row['MetroName']);
            }
        }else $this->metro = "";
    }

    public function setCyrillicAddress($address){
        #Пока что не реализована! Нет решения с однозначностью транслита для адреса.
        #$sql = "SELECT StoreAdres FROM stores WHERE StoreAdres = '".$address."'";
        return $address;
    }

    public function setCityZoomFilter(){
        $sql = "SELECT YandexZoom as zoom FROM cities WHERE CityName = '".$this->getCity()."'";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $this->zoom = $row['zoom'];
            }
        } else $this->zoom = 0;
    }

    public function setCityMapCenter($city){
        $sql = "SELECT YandexMap as ymap FROM cities WHERE Translit = '".$city."'";
        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $this->mapcenter = substr($row['ymap'],1,strlen($row['ymap'])-2);
            }
        } else $this->mapcenter = '55.755814,37.617635';
    }

}