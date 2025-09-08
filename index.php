<?php  

session_start();

require_once("Config/Config.php");
require_once("Helpers/Helpers.php");

// Obtener la ruta actual
$url = !empty($_GET['url']) ? $_GET['url'] : 'Home/home';
// Procesar la URL
$arrUrl = explode("/", $url);
$controller = $arrUrl[0];
$method = $arrUrl[0];
$params = "";

if (!empty($arrUrl[1])) {
    if ($arrUrl[1] != "") {
        $method = $arrUrl[1];    
    }
}

if (!empty($arrUrl[2])) {
    if ($arrUrl[2] != "") {
        for ($i = 2; $i < count($arrUrl); $i++) {
            $params .=  $arrUrl[$i] . ',';
        }
        $params = trim($params, ',');
    }
}

require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");
require_once("Libraries/Core/Bnc.php");

?>
