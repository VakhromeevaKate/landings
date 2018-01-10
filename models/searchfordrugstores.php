<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07.11.2017
 * Time: 16:31
 */
class SearchForDrugstores implements ISearchable {
    use PageInfo;
    private $dbconn;
    private $filter;

    public function __construct(){
        $_dbconn = new db();
        $_dbconn->connect();
        $this->dbconn = $_dbconn;
        $this->filter = new DrugstoresFilter();
    }

    public function search($searchstring = "")
    {
        $array = [];
        if ($this->filter->getMetroStation() != ""){
            $metro = " AND s.MetroId in (SELECT MetroId FROM metro WHERE MetroName LIKE '%".$this->filter->getMetroStation()."%') ";
        } else $metro = '';
        if ($this->filter->getEveryTime() != ""){
            $dayAndNight = " AND s.Store24h = 1 ";
        } else $dayAndNight = '';
        if ($this->filter->getCity() != ""){
            $city = " AND c.CityName LIKE '%".$this->filter->getCity()."%' ";
        } else $city = "";
        if ($this->filter->getAddress() != ""){
            $address = " AND s.StoreAdres LIKE '%". $this->filter->getAddress()."%' ";
        } else {
            $address = "";
        }
        $sql = "SELECT  
                    s.`StoreId` as id,
                    s.`StoreSklad_1c` as StoreId,
                    c.`CityName` as CityName,
                    s.`StoreNumber` as StoreNumber, 
                    s.`StoreName` as StoreName, 
                    s.`StoreAdres` as StoreAddress, 
                    s.`MetroId` as MetroId, 
                    m.`MetroName` as MetroName,
                    (SELECT color FROM metro_lines l INNER JOIN metro_links ml ON l.id = ml.l_id WHERE ml.s_id = s.MetroId) as color,
                    s.`StorePhone1` as StorePhone1,
                    s.`StorePhone2` as StorePhone2,
                    s.`StorePhone3` as StorePhone3, 
                    s.`StoreWorktime` as StoreWorktime, 
                    s.`StoreShop`, 
                    s.`StoreShopEmail`, 
                    s.`StorePhoneInt`, 
                    s.`StoreHiddenPrices` as HP,
                    ds.`StoreData` as coordinates,
                    substr(ds.`StoreData`,2,POSITION(',' IN `StoreData`)-2) as latitude,
                    substr(ds.`StoreData`, POSITION(',' IN ds.`StoreData`)+1,LENGTH(StoreData)-POSITION(',' IN StoreData)-1) as longitude,
                    c.`YandexZoom` as zoom      
                FROM  `stores` s 
                      LEFT JOIN `cities` c  
                      ON /*s.RegionId = c.RegionId AND*/ s.CityID = c.CityID #RegionId убран из-за косяка с Питером
                      LEFT JOIN  stores_data_string ds ON s.StoreId = ds.StoreId AND ds.`DataType`	= 1 
                      LEFT JOIN metro m ON s.MetroId = m.MetroId
                WHERE  
                      s.StoreActive = 1 ". $dayAndNight .$metro .$city .$address. " 
                ORDER BY s.StoreName ASC; ";

        $sql = $this->dbconn->query($sql);
        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function getMetroStations(){
        $array = [];
        $where = '';
        if (strlen($this->filter->getCity()) > 0){
            $where = " WHERE  ml.region_id = ( SELECT CASE WHEN  RegionId = 13 THEN  14 ELSE RegionId END FROM cities WHERE CityName = '".$this->filter->getCity()."' )";
        }
        $sql ="SELECT 
                      m.MetroName, m.Translit 
                FROM  metro m 
                    LEFT JOIN metro_links l   ON m.MetroId = l.s_id
                    LEFT JOIN metro_lines ml  ON l.l_id = ml.id
                ".$where."
                ORDER BY m.MetroName ASC;";
        $sql = $this->dbconn->query($sql);

        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function getCitiesList(){
        $array = [];
        $sql =" SELECT 
                CASE 
                  WHEN c.`CityName`='Москва' THEN 1
                  WHEN c.`CityName`='Санкт-Петербург' THEN 2
                  ELSE 3
                END as Sort,
                  c.`CityId` as id,
                  c.`RegionId` as region_id,
                  r.`RegionName` as region_name,
                  c.`CityName` as name, 
                  c.`Translit` as translit, 
                  c.YandexZoom as zoom,
                  c.`YandexMap` as coordinate 
                FROM cities c INNER JOIN regions r ON c.`RegionId` = r.`RegionId`
                WHERE length(c.`Translit`) > 0 AND c.`CityId` in (SELECT `CityId` FROM `stores` WHERE `StoreActive` = 1)
                ORDER BY Sort ASC, c.`CityName` ASC ;";
        $sql = $this->dbconn->query($sql);

        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function getRegionsist(){
        $array = [];
        $sql =" SELECT 
                CASE 
                  WHEN r.RegionName='Москва' THEN 1
                  WHEN r.RegionName='Санкт-Петербург' THEN 2
                  ELSE 3
                END as Sort,
                 r.RegionId, r.RegionName, r.RegionCode, r.RegionNameShort
                FROM regions r
                WHERE r.RegionId IN (SELECT s.RegionId FROM stores s WHERE s.StoreActive = 1)
                ORDER BY Sort;";
        $sql = $this->dbconn->query($sql);

        if ($this->dbconn->num_rows($sql) > 0) {
            while ($row = $this->dbconn->fetch_assoc($sql)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function getFilter(){
        return $this->filter;
    }

    public function orderBySort($a, $b){
        if ($a['Sort'] === $b['Sort']) return 0;
        return $a['Sort'] > $b['Sort'] ? -1 : 1;
    }

    public function createStoresJSON($array){
        $out = new stdClass;
        $out->type = "FeatureCollection";
        $out->features = [];

        foreach($array as $row) {
            $obj = new stdClass;
            $obj->geometry = $obj->options = $obj->properties = new stdClass;
            $obj->geometry = $obj->options = $obj->properties = new stdClass;

            $obj->type = 'Feature';
            $obj->id = $row['StoreId'];
            $obj->geometry->type = 'Point';
            $obj->geometry->coordinates = [$row['latitude'], $row['longitude']];
            $obj->options->iconLayout = 'default#image';
            $obj->options->iconImageHref = '/i/s/maps/placemark.png';
            $header = sprintf("№%d %s", $row['StoreNumber'], $row['StoreName']);

            $balloonContentHeader =<<< BCH
<div class='cart-add-block'>
<div class='info-list'>
<div class='info-list__metro-wrap'>
<div class='info-list__metro info-list__metro_light'>
<span class='info-list__metro-icon' style='background-color: {$row['color']};'></span>{$row['MetroName']}</div>
<div class='info-list__metro-close close'></div>
</div>
BCH;
            $balloonContentBody =<<< BCB
<ul class='info-list__list'>
<li class='info-list__item icon-pin'><b>{$row['StoreAddress']}</b></li>
<li class='info-list__item icon-phone'><a href='tel:{$row['StorePhone1']}'>{$row['StorePhone1']}</a></li>
<li class='info-list__item icon-clock'>{$row['StoreWorktime']}</li>
</ul>
</div>
BCB;
            $balloonContentFooter =<<< BCF
<div class='btn-line btn-line_left'>
<button class='btn btn-primary'><a href="/stores/{$row['id']}" style="text-decoration: none;">Поиск лекарств в аптеке</a></button>
</div>
</div>
</div>

BCF;
            $obj->properties->balloonContentHeader = $balloonContentHeader;
            $obj->properties->balloonContentBody = $balloonContentBody;
            $obj->properties->balloonContentFooter = $balloonContentFooter;

            $out->features[] = $obj;
        }
        #file_put_contents('stores.json', json_encode($out, JSON_UNESCAPED_UNICODE));
        return json_encode($out, JSON_UNESCAPED_UNICODE);
    }

}
