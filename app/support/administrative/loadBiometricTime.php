<?php
ini_set('max_input_time', 300); // 5 minutos para procesar entrada
ini_set('max_execution_time', 600); // 10 minutos para ejecutar el script
ini_set('memory_limit', '512M'); // Ajusta si el archivo es muy grande

require '../../logic/conn.php';
require '../../../lib/vendor/autoload.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$message = '';
$message_2 = '';
$icon = '';
$flag_carga = '';
$endDate = date('Y-m-d H:i:s');
$startDate = '';

$format = 'd/m/Y';
function is_date($dateFile, $format) {
    $d = DateTime::createFromFormat($format, $dateFile);
    return $d && $d->format($format) === $dateFile;
}

$formatTime = 'H:i';
function is_time($timeFile, $formatTime) {
    $t = DateTime::createFromFormat($formatTime, $timeFile);
    return $t && $t->format($formatTime) === $timeFile;
}

//echo $endDate;

use PhpOffice\PhpSpreadsheet\IOFactory;



    if (isset($_FILES['archivo_csv'])) {
        $archivo_csv = $_FILES['archivo_csv'];

        // Verificar si no hay errores al subir el archivo
        if ($archivo_csv['error'] === UPLOAD_ERR_OK) {

            // Ruta temporal donde se guardó el archivo subido
            $archivoTemporal = $archivo_csv['tmp_name'];

            // Abrir el archivo CSV en modo lectura
            $csvFile = fopen( $archivoTemporal , 'r');

            // Leer la primera fila para obtener los nombres de las columnas
            $encabezados = fgetcsv($csvFile);

            //print_r($encabezados);

            // Eliminar caracteres no deseados del principio del primer encabezado de columna
            $encabezados[0] = trim($encabezados[0]);

            //Asignamos nombre al encabezado
            $encabezados[0] = 'ID_NOM';
            $encabezados[1] = 'STATUS';            
            $encabezados[2] = 'RECORD_DATE';
            $encabezados[3] = 'RECORD_TIME';
            $encabezados[4] = 'CREATED_BY';
            $encabezados[5] = 'CREATED_DATE';

            // Limitar a los primeros 4 encabezados
            $encabezados_limitados = array_slice($encabezados, 0, 6);

            //print_r($encabezados_limitados);

            // Usar implode para convertirlos en una cadena
            $columnas = implode(", ", $encabezados_limitados);

            //echo $columnas;
            $contador = 0;
            $registros_ok = 0;
            $registros_err = 0;

            try{
                // Iterar sobre las filas del archivo CSV
                while (($fila = fgetcsv($csvFile)) !== false){ 

                        if (isset($fila[9]) || isset($fila[10])) {                            
                            $date = $fila[9];
                            $time = $fila[10];

                            if (is_date($date, $format)) {
                                if (is_time($time, $formatTime)) {

                                    $idNom = $fila[0];
                                    $date = DateTime::createFromFormat('d/m/Y', $fila[9])->format('Y-m-d');
                                    $time = $fila[10];
                                    $status = $fila[11];

                                    // Escapar los valores para prevenir inyección de SQL
                                    $valoresEscapados = array_map(array($mysqli, 'real_escape_string'), $fila);

                                    $valoresEscapados[0] = $idNom;
                                    $valoresEscapados[1] = $status;
                                    $valoresEscapados[2] = $date;
                                    $valoresEscapados[3] = $time;
                                    $valoresEscapados[4] = $user_active;
                                    $valoresEscapados[5] = $endDate;

                                    // Limitar los valores a los primeros 4 elementos
                                    $valoresNecesarios = array_slice($valoresEscapados, 0, 6);

                                    // Construir la parte de la consulta SQL para los valores
                                    $valores = implode("', '", $valoresNecesarios);

                                    // Construir y ejecutar la consulta SQL
                                    $insert = "INSERT IGNORE INTO biometricTimeClock ($columnas) VALUES ('$valores');";

                                    //echo '<br>'.$insert;

                                    if ($mysqli->query($insert)) {

                                        //$message = 'Registros de Checador Biométrico cargados correctamente';
                                        $registros_ok ++;                                            

                                    } else {
                                        //$message = 'Error cargando registros Checador Biométrico';
                                        $registros_err ++;
                                    }
                                    
                                    $contador++;

                                    if($registros_err > 0){
                                        $message = 'Hay errores en la carga de los registros de Checador Biométrico';
                                        $icon = 'error';
                                        $url = "#";
                                        $button = "Regresar";
                                    } elseif($registros_ok == $contador and $registros_err == 0){
                                        $message = 'Registros de Checador Biométrico cargados correctamente';
                                        $icon = 'success';
                                        $url = "update_attendance.php?f={$endDate}&r={$registros_ok}";
                                        $button = "Continuar";
                                    }else{
                                        echo "Contador: ",$contador ,"<br>";
                                        echo "Correctos: ",$registros_ok,"<br>";
                                        echo "Errores: ",$registros_err,"<br>";
                    
                                        $message = 'Hay discrepancias en la carga de los registros de Checador Biométrico';
                                        $icon = 'error';
                                        $url = "#";
                                        $button = "Regresar";
                                    }

                                } else {
                                    $message = 'Validar que se esté cargando el archivo correcto';
                                    $icon = 'error';
                                    $url = "../";
                                    $button = "Regresar";
                                }
                            } else {
                                $message = 'Validar que se esté cargando el archivo correcto';
                                $icon = 'error';
                                $url = "../";
                                $button = "Regresar";
                            }
                        } else {
                            $message = 'Validar que se esté cargando el archivo correcto';
                            $icon = 'error';
                            $url = "../";
                            $button = "Regresar";
                        }

                        

                    

                }   // Cierra el while de carga de registros del CSV       
                
                /* if($registros_err > 0){
                    $message = 'Hay errores en la carga de los registros de Checador Biométrico';
                    $icon = 'error';
                    $url = "#";
                    $button = "Regresar";
                } elseif($registros_ok == $contador and $registros_err == 0){
                    $message = 'Registros de Checador Biométrico cargados correctamente';
                    $icon = 'success';
                    $url = "update_attendance.php?f={$endDate}&r={$registros_ok}";
                    $button = "Continuar";
                }else{
                    echo "Contador: ",$contador ,"<br>";
                    echo "Correctos: ",$registros_ok,"<br>";
                    echo "Errores: ",$registros_err,"<br>";

                    $message = 'Hay discrepancias en la carga de los registros de Checador Biométrico';
                    $icon = 'error';
                    $url = "#";
                    $button = "Regresar";
                } */
                
            } catch (Exception $e) {
                $message = 'Error al cargar registros de Checador Biométrico';
                $icon = 'error';
                $url = "#";
                $button = "Regresar";
            }
        } else {
            $message = 'Error al cargar archivo CSV de Checador Biométrico';
            $icon = 'error';
                $url = "#";
                $button = "Regresar";
        }
    } else {
        $message = 'No existe archivo CSV de Checador Biométrico';
        $icon = 'error';
        $url = "#";
        $button = "Regresar";
    }




?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Registros Biotime</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>
    <script type="text/javascript">
        swal({
            title: "Carga registros Checador",
            text: "<?php echo $message . $message_2; ?>",
            icon: "<?php echo $icon; ?>",
            button: "<?php echo $button; ?>",
        }).then(function() {
            window.location = "<?php echo $url; ?>";
        });
    </script>
</body>

</html>