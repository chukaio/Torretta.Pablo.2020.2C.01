<?php
require __DIR__ . "/vendor/autoload.php";
require_once "./clases/usuario.php";
require_once "./clases/auto.php";

$path = $_SERVER['PATH_INFO'] ?? "";
$method = $_SERVER['REQUEST_METHOD'];

echo $path . " y " . $method . "<br>";

switch ($path) {
    case '/registro':
        if ($method == 'POST') {
            //01 --> Registrar un usuario con los siguientes datos: email, tipo de usuario, password y foto. El tipo de usuario puede ser admin o user. Validar que el mail no esté registrado previamente.
            //echo "Caso 1<br>";
            $email = $_POST['email'] ?? "";
            $tiposuario = $_POST['tipo'] ?? "";
            $password = $_POST['password'] ?? "";
            $foto = $_FILES['imagen']['name'] ?? "";
            $user = new usuario($email, $password, $tiposuario, $foto);

            if ($user->validateUser()) {
                $user->setPhotoName();
                $source = $_FILES["foto"]["tmp_name"] ?? "";
                $destination = "./img/" . $user->_foto;
                Usuario::movePhoto($source, $destination);
                if ($user->saveFile()) {
                    echo "Archivo con el usuario guardado exitosamente";
                } else {
                    echo "Ha ocurrido un error al intentar guardar el archivo";
                }
            } else {
                echo "Usuario incorrecto";
            }
        }
        break;
    case '/login':
        //02 --> Los usuarios deberán loguearse y se les devolverá un token con email y tipo en caso de estar registrados, caso contrario se informará el error.
        if ($method == 'POST') {
            $email = $_POST['email'] ?? "";
            $password = $_POST['password'] ?? "";
            $user = new usuario($email, $password);
            $user = $user->getUser();

            if ($user) {
                echo $user->generateAuthToken();
                //echo $user->_password;
            } else {
                echo "El Usuario no esta registrado";
            }
        }
        break;
    case '/ingreso':
        //03 --> Sólo users. Se ingresará patente, fecha_ingreso (dia y hora) y el email del usuario que ingresó el auto y se guardará en el archivo autos.xxx.
        if ($method == 'POST') {
            $patente = $_POST['patente'] ?? "";
            $auto = new auto($patente);

            if ($auto) {
                if ($auto->saveFile()) {
                    echo "Archivo con el auto guardado exitosamente";
                } else {
                    echo "Ha ocurrido un error al intentar guardar el archivo";
                }
            } else {
                echo "Acceso denegado";
            }
        }else if ($method == 'GET') {
            //05 --> Devuelve un listado con todos los vehículos estacionados ordenados por tipo
            var_dump(auto::GetAll());
        }
        break;
    case 'users':
        //07 --> Se recibe una imagen y se actualiza el usuario dado, la imagen original se guardará en la carpeta backups.
        if ($method == 'POST') {
            $foto = $_FILES["imagen"]['name'] ?? "";
            //$email=$_POST['email'] ?? "";
            //$clave=$_POST['clave'] ?? "";
            $usuario=new Usuario("","","",$foto);
            $usuario=$usuario->getUser();

            if($usuario != null){
                $source=$_FILES["foto"]['tmp_name'] ?? "";
                $destination="./img/backup/" . $foto;
                if(usuario::MovePhoto($source, $destination)){
                    if($usuario->saveFile()){
                        echo "Archivo con el usuario guardado exitosamente";
                    }
                    else{
                        echo "Ha ocurrido un error al intentar guardar el archivo";
                    }
                }
            }
            else{
                echo "No existe el usuario ingresado";
            }
        }
    break;
    default:
        break;
}
