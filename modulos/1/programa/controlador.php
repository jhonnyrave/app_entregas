<?php
$plantilla = "default/default.html";

class programaController extends mainController {
    private $Business;

    public function __construct() {
        parent::__construct();
		
    }

    public function grabarGeneral($parametros) {
        // Validation and data processing
        $programa = strtoupper($parametros['form']['programa']);
        $descripcion = strtoupper($parametros['form']['descripcion']);
        $menu = $parametros['form']['menu-programa'];
        $controladorJs = $parametros['form']['controladorJs'];
        $autenticado = $parametros['form']['autenticado'];

        $myProg = new programa($programa);
		$template = new Template();

        $errores = array();
        if ($programa == '') {
            $errores[] = "Nombre de aplicación no válido";
        }
        if ($descripcion == '') {
            $errores[] = "Descripción no válida";
        }
        if ($menu == '') {
            $errores[] = "Debe seleccionar un menú válido";
        }
        if ($controladorJs == '' && $myProg->existe == 'N') {
            $errores[] = "Debe seleccionar un controlador";
        }

        if (count($errores) > 0) {
            return $this->response;
        }

        $myProg->descripcion = $descripcion;
        $menu_ant = $myProg->menu; // Backup the previous menu ID
        $myProg->menu = $menu;

        $myProg->begin_work();
        $myProg->grabarGeneral();

        // Create templates if the program is new
        if ($myProg->existe == 'N') {
            $carpetaModulo = MODULE_PATH . $myProg->menu . "/" . strtolower($programa);
			$template->makeDir($carpetaModulo);
        
            $template->makeDir($carpetaModulo);
            $rutaTemplate = MODULE_PATH . "1/programa/template/";

            $carpetaMenu = MODULE_PATH . $myProg->menu;
            $template->makeDir($carpetaMenu);

            // Create model
            $data = $template->getFile($rutaTemplate . "modelo.php");
            $data = str_replace("[[programa]]", strtolower($programa), $data);
            $archivoModelo = $carpetaModulo . "/modelo.php";
            $template->putFile($archivoModelo, $data);

            // Create business logic
            $data = $template->getFile($rutaTemplate . "negocio.php");
            $data = str_replace("[[programa]]", strtolower($programa), $data);
            $archivoModelo = $carpetaModulo . "/negocio.php";
            $template->putFile($archivoModelo, $data);

            // Create PHP controller
            if ($controladorJs == 'S') {
                $data1 = $template->getFile($rutaTemplate . "controlador_servicio.php");
            }

            $data = str_replace("[[programa]]", strtolower($programa), '<?php' . "\n" . '$plantilla="default/default.html";' . "\n" . $data1);
            $archivoControlador = $carpetaModulo . "/controlador.php";
            $template->putFile($archivoControlador, $data);

            // Create JS controller
            if ($controladorJs == 'S') {
                $data = $template->getFile($rutaTemplate . "controlador.js");
                $data = str_replace("[[programa]]", strtolower($programa), $data);
                $archivoModelo = $carpetaModulo . "/controlador.js";
                $template->putFile($archivoModelo, $data);
            }

            // Create view
            $data = $template->getFile($rutaTemplate . "vista.html");
            $data = str_replace("[[programa]]", strtolower($programa), $data);
            $data = str_replace("[[titulo]]", ucwords(strtolower($programa)), $data);
            $data = str_replace("[[subtitulo]]", ucfirst(strtolower($descripcion)), $data);
            $archivoModelo = $carpetaModulo . "/vista.html";
            $template->putFile($archivoModelo, $data);

            // Create JS test
            $data = $template->getFile($rutaTemplate . "test.js");
            $data = str_replace("[[programa]]", strtolower($programa), $data);
            $archivoModelo = $carpetaModulo . "/test.js";
            $template->putFile($archivoModelo, $data);

            // Create a default option and assign permission to 'admin'
            $myProg->agregarPermiso('A', 'Acceso a la aplicación');
            $myProg->grabarPermisos();
            $respuesta = array(
                'programa' => $programa,
                'permiso' => $myProg->id . "-A-"
            );
        }

        if ($myProg->existe == 'S') {
            if ($menu_ant != '' && $menu != $menu_ant) {
                if (!file_exists(MODULE_PATH . "$menu/")) {
                    mkdir(MODULE_PATH . "$menu/");
                    chmod(MODULE_PATH . "$menu/", 0777);
                }
                rename(MODULE_PATH . "$menu_ant/" . strtolower($programa), MODULE_PATH . "$menu/" . strtolower($programa));
            }
        }
        $myProg->commit();

        $respuesta = $programa;
        return $this->response($respuesta);
    }

    public function traerComponentes() {
        $gestor = opendir(COMPONENT_PATH);
        $mcomponentes = array();

        while (false !== ($componente = readdir($gestor))) {
            $file_ini = COMPONENT_PATH . "/$componente/config.ini";
            if (file_exists($file_ini)) {
                $config = parse_ini_file($file_ini);
                $mcomponentes[$componente] = $config;
            }
        }
        $this->response($mcomponentes);
    }

    public function buscarPrograma($parametros) {
        $programa = strtoupper($parametros['programa']);
        $myProg = new programa($programa);
        $myProg->getPermisos();
        $this->response($myProg);
    }

    public function eliminarPrograma($parametros) {
		$template = new Template();
        $programa = $parametros['programa'];
        $myProg = new programa($programa);
        $carpetaModulo = MODULE_PATH . $myProg->menu . "/" . strtolower($programa);
        $template->rmDir($carpetaModulo);
        $myProg->eliminarPrograma();
        $this->response(array("resultado" => "success"));
    }

    public function GrabarPermisos($parametros) {
        $programa = strtoupper($parametros['programa']);
        $permisos = explode(",", $parametros['form']['permiso']);
        $descripciones = explode(",", $parametros['form']['descripcion']);
        $myProg = new programa($programa);

        foreach ($permisos as $key => $permiso) {
            $permiso = strtoupper($permiso);
            $descripcion = trim($descripciones[$key]);
            if ($permiso != '') {
                $myProg->agregarPermiso(trim($permiso), trim($descripcion));
            }
        }

        $myProg->begin_work();
        $myProg->grabarPermisos();
        $myProg->commit();
        $this->response($programa);
    }

    public function grabarComponentes($parametros) {
		$template = new Template();
        $contenido = "";
        $componentes = explode(",", $parametros['form']['componentes']);
        $programa = $parametros['programa'];

        $myProg = new programa($parametros['programa']);
        $componetes_anteriores = $template->getFile(MODULE_PATH . $myProg->menu . "/" . strtolower($programa) . "/componentes.ini");

        if ($componetes_anteriores != '') {
            $componetes_anteriores = explode("\n", $componetes_anteriores);
        } else {
            $componetes_anteriores = array();
        }

        if (is_array($componentes)) {
            foreach ($componentes as $key => $value) {
                $contenido .= $value . PHP_EOL;
            }
        }
        $template->putFile(MODULE_PATH . $myProg->menu . "/" . strtolower($programa) . "/componentes.ini", $contenido, 'w+');

        if (is_array($componentes)) {
            foreach ($componentes as $key => $value) {
                if (!in_array($value, $componetes_anteriores)) {
                    $componenteJS = COMPONENT_PATH . "/$value/config.ini";
                    if (file_exists($componenteJS)) {
                        $config = parse_ini_file($componenteJS);
                        if ($config['templateJS'] != '') {
                            $config['templateJS'] = str_replace("[[programa]]", strtolower($programa), $config['templateJS']);
                            $dataAnt = $template->getFile(MODULE_PATH . $myProg->menu . "/" . strtolower($programa) . "/controlador.js");
                            $dataAnt = str_replace('$(document).ready(function() {', '$(document).ready(function() {' . $config['templateJS'], $dataAnt);
                            $template->putFile(MODULE_PATH . $myProg->menu . "/" . strtolower($programa) . "/controlador.js", $dataAnt, 'w+');
                        }
                    }
                    $templateHTML = COMPONENT_PATH . "/$value/template.html";
                    if (file_exists($templateHTML)) {
                        $templateHTML = $template->getFile($templateHTML);
                        $templateHTML = str_replace("[[programa]]", strtolower($programa), $templateHTML);
                        $template->putFile(MODULE_PATH . $myProg->menu . "/" . strtolower($programa) . "/vista.html", $templateHTML);
                    }
                }
                $contenido .= $value . PHP_EOL;
            }
        }

        $this->response($programa);
    }

    public function getOpciones() {
        $programa = new programa();
        $opciones = $programa->getOpciones();
        for ($i = 0; $i < count($opciones); $i++) {
            $opciones[$i]->nombre = utf8_encode($opciones[$i]->nombre);
        }
        $this->response($opciones);
    }

    public function getMenuProgramas() {
        $programa = new programa();
        $menu = $programa->getMenuProgramas(0);
        $this->response($menu);
    }

    public function getPermiso($parametros) {
        $programa = new programa();
        $progr = strtoupper($parametros['programa']);
        if (!empty($parametros['opcion'])) {
            $tienePermiso = $programa->getPermiso(strtoupper($parametros['opcion']), false);
        } else {
            $tienePermiso = false;
        }
        $programa = new programa($progr);
        $this->response(array("permiso" => $tienePermiso, "listado" => $programa->permisos));
    }
}
// END OF CLASS
?>