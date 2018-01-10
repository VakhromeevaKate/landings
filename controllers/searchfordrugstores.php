<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07.11.2017
 * Time: 16:40
 */
spl_autoload_register(function ($class_name) {
    include_once ($_SERVER["DOCUMENT_ROOT"].'/landings/models/'.strtolower($class_name) . '.php');
});

#Создаем новый поиск
$search = new SearchForDrugstores();
if (strpos($_SERVER['REQUEST_URI'],"?")!= false){
    $pageurl = substr($_SERVER['REQUEST_URI'],1, strpos($_SERVER['REQUEST_URI'],"?")-2);
} else $pageurl = substr($_SERVER['REQUEST_URI'],1, strlen($_SERVER['REQUEST_URI'])-2);

#Список станций метро
$metro_list = $xml->createElement("metro_list");
if (count($search->getMetroStations()) == 0){
   $metro_list->setAttribute("isMetro","no");
} else $metro_list->setAttribute("isMetro","yes");
$metro_list = $stx->ArrayToXML($search->getMetroStations(),$metro_list);
$block->appendChild($metro_list);

#устанавливаем, пока ничего не искали
$block->setAttribute("type","nothing_entered");

    #Генерируем JSON-файл по заданным параметрам:
if (isset($_GET['city']) || isset($_GET['metro']) || isset($_GET['DayAndNight']) || isset($_GET['OpenedNow']) || isset($_GET['street'])) {
    foreach ($_GET as $key => $value) {
        switch ($key) {
            case 'city':
                $search->getFilter()->setCity($_GET['city']);
                continue;
            case 'metro':
                $search->getFilter()->setMetroStation($_GET['metro']);
                continue;
            case 'street':
                $search->getFilter()->setAddress($_GET['street']);
                continue;
            case 'DayAndNight':
                $search->getFilter()->setEveryTime($_GET['DayAndNight']);
                continue;
            case 'OpenedNow':
                $search->getFilter()->setOpened($_GET['OpenedNow']);
                continue;
        }
    }
}



    #Добавляем аптеки в XML
    $block->setAttribute("name","searchfordrugstores");
    $drugstores = $search->search('');
    $block = $stx->ArrayToXML($drugstores, $block);

    #Добавляем города
    $cities_list = $xml->createElement('cities_list');
    $cities_list = $stx->ArrayToXML($search->getCitiesList(),$cities_list);
    $block->appendChild($cities_list);
    
    #Добавляем регионы
    $regions_list = $xml->createElement('regions_list');
    $regions_list = $stx->ArrayToXML($search->getRegionsist(),$regions_list);
    $block->appendChild($regions_list);

    #Добавляем заголовок и тексты в XML
    $pageInfo = $xml->createElement('pageInfo');
    $pageInfoArr = $search->getStorePageInfo($pageurl);
    $pageInfo = $stx->ArrayToXML($pageInfoArr,$pageInfo);
    $block->appendChild($pageInfo);

    #Предустановленные фильтры для поиска (по URL):
    $filter = $search->getFilter()->setFiltersFromURI();
    $filters = $xml->createElement('filter');
    $filters = $stx->ArrayToXML($filter->toArray(),$filters);
    $block->appendChild($filters);

    #Генерируем JSON
    $json = addslashes($search->createStoresJSON($drugstores));
    $json_xml = $xml->createElement('json',$json);
    $block->appendChild($json_xml);


    $block->setAttribute("type","found");
    if (count($drugstores)>0){
        #устанавливаем, когда что-то нашли
        $block->setAttribute("type","found");
    } else {
        #устанавливаем, когда ничего не нашли
        $block->setAttribute("type","nothing_found");
    }













