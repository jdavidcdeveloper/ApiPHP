<?php


$host = "localhost";
$user = "root";
$password = "";
$db = "api_php";


$conexion = new mysqli($host, $user, $password, $db);

if ($conexion->connect_error) {

    die("Conexión fallida" . $conexion->connect_error);


}

// Configuración de la respuesta como JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");    

// Obtener el método de la solicitud
$metodo = $_SERVER['REQUEST_METHOD'];

// Obtener la información de la ruta
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

// Dividir la ruta en segmentos
$buscarId = explode('/', $path);

// Obtener el último segmento como identificador
$id = ($path !== '/') ? end($buscarId) : null;


// // Sugerencias de depuración
// echo "Contenido de la solicitud: " . file_get_contents('php://input') . "\n";
// echo "Método: $metodo\n";
// echo "Ruta: $path\n";
// echo "ID obtenido: $id\n";




switch ($metodo) {
    //SELECT users
    case 'GET':
        consulta($conexion, $id);
        break;

    //INSERT users
    case 'POST':
        insertar($conexion);
        break;

    //UPDATE users
    case 'PUT':
        actualizar($conexion, $id);
        break;

    //DELETE users
    case 'DELETE':
        borrar($conexion, $id);
        break;

    default:
        echo "Método no permitido";
        break;
}

function consulta($conexion, $id)
{
    $sql = ($id===null) ? "SELECT * FROM usuarios":"SELECT * FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }
}

function insertar($conexion)
{
    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    } else {
        echo json_encode(array('error' => 'Error al crear usuario'));
    }
}

function borrar($conexion, $id)
{
    echo "el id a borrar es: " . $id;

    if ($id !== null) {
        $sql = "DELETE FROM usuarios WHERE id = '$id'";
        $resultado = $conexion->query($sql);

        if ($resultado) {
            echo json_encode(array('mensaje' => 'Usuario eliminado correctamente'));
        } else {
            echo json_encode(array('error' => 'Error al eliminar usuario'));
        }
    } else {
        echo json_encode(array('error' => 'ID no proporcionado para eliminar usuario'));
    }
}

function actualizar($conexion, $id)
{
    echo "el id a actualizar es: " . $id;

    if ($id !== null) {
        $dato = json_decode(file_get_contents('php://input'), true);
        $nombre = $dato['nombre'];

        $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = '$id'";
        $resultado = $conexion->query($sql);

        if ($resultado) {
            echo json_encode(array('mensaje' => 'Usuario actualizado correctamente'));
        } else {
            echo json_encode(array('error' => 'Error al actualizar usuario'));
        }
    } else {
        echo json_encode(array('error' => 'ID no proporcionado para actualizar usuario'));
    }
}
