<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07.11.2017
 * Time: 11:36
 */

spl_autoload_register(function ($class_name) {
    $rootdir = Utils::getDocumentRoot();
    include_once ($rootdir.'/landings/models/'.$class_name . '.php');
});


$search = new SearchForDrugs();
var_dump($search->search('афобазол'));
