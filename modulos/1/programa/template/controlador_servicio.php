class [[programa]]Controller extends mainController{
private $Business;

public function __construct () {
$this->Business = new [[programa]]Business();
}

//METODO DE EJEMPLO CONSULTA USUARIOS
function PeticionEjemplo(){
$modelo = new inicio();
$modelo->estado=$estado;
$lista=$modelo->GetUsuarios();
$ruta = IMG_USURIOS_PATH;

foreach ($lista as $value) {

if (file_exists($ruta.$value->usuario.".png"))
$value->imagen = $value->usuario.'.png';
else
$value->imagen = 'default.png';
}

$this->response($lista);
}
}