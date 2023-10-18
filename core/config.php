<?php
//definicion de rutas y constantes
//header("Access-Control-Allow-Origin: *");
define('ROOT_PATH',	"./");
define('LIBS_PATH',	ROOT_PATH.'libs/');
define('MODULE_PATH',	ROOT_PATH.'modulos/');
define('STATIC_PATH',	ROOT_PATH.'static/');
define('COMPONENT_PATH',	STATIC_PATH.'componentes/');
define('TEMPLATE_PATH',	STATIC_PATH.'template/');
define('CORE',ROOT_PATH.'core/');
define ('BASE_URL_PATH', 'http://'.dirname($_SERVER['HTTP_HOST'].''.$_SERVER['SCRIPT_NAME']).'/');

define('IMG_USURIOS_PATH',STATIC_PATH.'images_usuarios/');
define('IMG_GUIAS_PATH',STATIC_PATH.'images_guias/');




//require CONF_PATH;

$file=CORE."/config/".$_SERVER['SERVER_NAME'].".ini";

if(!isset($_SESSION['config'])){
	
	if(file_exists($file)){
		$_SESSION['config']=parse_ini_file($file,true);
	}
}