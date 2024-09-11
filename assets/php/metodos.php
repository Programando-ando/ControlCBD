<?php
require_once "db.php";
header('Content-Type: text/html; charset=utf-8');
$valido['success'] = array('success' => false, 'mensaje' => "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    switch ($action) {
        case "add":
            $a = isset($_POST['cate']) ? $_POST['cate'] : null;
            $b = isset($_POST['activi']) ? $_POST['activi'] : null;
            $c = isset($_POST['cal']) ? $_POST['cal'] : null;
        
            if ($a && $b && $c) {
                $sql = "INSERT INTO registro VALUES (null, '$a', '$b', '$c')";
                if ($cx->query($sql)) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "SE GUARDÓ CORRECTAMENTE";
                } else {
                    $valido['success'] = false;
                    $valido['mensaje'] = "ERROR AL GUARDAR EN BD";
                }
            } else {
                $valido['success'] = false;
                $valido['mensaje'] = "FALTAN DATOS PARA EL REGISTRO";
            }
        
            echo json_encode($valido);
            break;
        

        case "selectAll":
            $sql = "SELECT * FROM registro";

            $registros = array('data' => array());
            $res = $cx->query($sql);

            if ($res->num_rows > 0) {
                while ($row = $res->fetch_array()) {
                    $registros['data'][] = array($row[0], $row[1], $row[2], $row[3]);
                }
            }

            echo json_encode($registros);
            break;

        case "delete":
            if (isset($_POST['id'])) {
                $id = $_POST['id'];

                $sql = "DELETE FROM gasto WHERE idgasto = $id";
                if ($cx->query($sql)) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "SE ELIMINÓ CORRECTAMENTE";
                } else {
                    $valido['success'] = false;
                    $valido['mensaje'] = "ERROR AL ELIMINAR EN BD";
                }
            } else {
                $valido['success'] = false;
                $valido['mensaje'] = "FALTA EL ID DEL GASTO";
            }

            echo json_encode($valido);
            break;

        case "select":
            $valido = array(
                'success' => false,
                'mensaje' => "",
                'id' => "",
                'descripcion' => "",
                'costo' => "",
                'categoria' => ""
            );

            if (isset($_POST['id'])) {
                
                $id = $_POST['id'];

                $sql = "SELECT ga.idgasto, ga.descripcion, ga.monto, c.categoria
                        FROM gasto ga
                        INNER JOIN categoria c ON ga.idcategoria = c.idcategoria
                        WHERE ga.idgasto = $id";

                $res = $cx->query($sql);
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_array();

                    $valido['success'] = true;
                    $valido['mensaje'] = "SE ENCONTRÓ GASTO";
                    $valido['id'] = $row[0];
                    $valido['descripcion'] = $row[1];
                    $valido['costo'] = $row[2];
                    $valido['categoria'] = $row[3];
                } else {
                    $valido['success'] = false;
                    $valido['mensaje'] = "GASTO NO ENCONTRADO";
                }
            } else {
                $valido['success'] = false;
                $valido['mensaje'] = "FALTA EL ID DEL GASTO";
            }   

            echo json_encode($valido);
            break;                                          

        case "update":
            if (isset($_POST['idgasto'], $_POST['descripcion'], $_POST['costo'], $_POST['categoria'])) {
                $id = $_POST['id'];
                $a = $_POST['descripcion'];                                                
                $b = $_POST['costo'];   
                $c = $_POST['categoria'];

                $sql_categoria = "SELECT idcategoria FROM categoria WHERE categoria = '$c'";
                $result_categoria = $cx->query($sql_categoria);

                if ($result_categoria->num_rows > 0) {
                    $row_categoria = $result_categoria->fetch_assoc();
                    $id_c = $row_categoria['idcategoria'];

                    $sql_update = "UPDATE gasto SET descripcion='$a', monto='$b', idcategoria='$id_c' WHERE idgasto='$id'";
                    if ($cx->query($sql_update)) {
                        $valido['success'] = true;
                        $valido['mensaje'] = "SE ACTUALIZÓ CORRECTAMENTE EL GASTO";
                    } else {
                        $valido['success'] = false;
                        $valido['mensaje'] = "ERROR AL ACTUALIZAR EN BD";
                    }
                } else {
                    $valido['success'] = false;
                    $valido['mensaje'] = "CATEGORÍA NO ENCONTRADA";
                }
            } else {
                $valido['success'] = false;
                $valido['mensaje'] = "FALTAN DATOS PARA ACTUALIZAR";
            }

            echo json_encode($valido);
            break;

        case "reset":
            $sql = "DELETE FROM gasto";
            if ($cx->query($sql)) {
                $valido['success'] = true;
                $valido['mensaje'] = "SE ELIMINARON TODOS LOS GASTOS";
            } else {
                $valido['success'] = false;
                $valido['mensaje'] = "ERROR AL ELIMINAR";
            }

            echo json_encode($valido);
            break;

        case 'readAll':
            readAll($cx);
            break;

        default:
            echo json_encode(array("error" => "Acción no válida"));
            break;
    }

} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
