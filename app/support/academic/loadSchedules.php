<?php

ini_set('max_input_time', 300); // 5 minutos para procesar entrada
ini_set('max_execution_time', -1); // 10 minutos para ejecutar el script
ini_set('memory_limit', '512M'); // Ajusta si el archivo es muy grande

require '../../logic/conn.php';
require '../../../lib/vendor/autoload.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location:../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$message = '';
$icon = '';

use PhpOffice\PhpSpreadsheet\IOFactory;

$sqlDelete = "DELETE FROM academic_schedules";
if ($mysqli->query($sqlDelete)) {

    if (isset($_FILES['archivo_xlsx'])) {
        $archivo_xlsx = $_FILES['archivo_xlsx'];

        // Verificar si no hay errores al subir el archivo
        if ($archivo_xlsx['error'] === UPLOAD_ERR_OK) {

            // Ruta temporal donde se guardó el archivo subido
            $archivoTemporal = $archivo_xlsx['tmp_name'];

            // Cargar el archivo Excel desde la ruta temporal
            $spreadsheet = IOFactory::load($archivoTemporal);

            // Obtener el escritor para CSV
            $writer = IOFactory::createWriter($spreadsheet, 'Csv');
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setSheetIndex(0); // Puedes ajustar el índice de la hoja según sea necesario

            // Guardar el archivo CSV
            $writer->save($archivo_xlsx['tmp_name']);

            //echo "El archivo Excel se ha convertido correctamente a formato CSV.";

            // Cerrar el archivo CSV
            $writer = null;

            // Abrir el archivo CSV en modo lectura
            $csvFile = fopen($archivo_xlsx['tmp_name'], 'r');

            // Leer la primera fila para obtener los nombres de las columnas
            $encabezados = fgetcsv($csvFile);

            // Eliminar caracteres no deseados del principio del primer encabezado de columna
            $encabezados[0] = trim($encabezados[0]);

            //Asignamos nombre al encabezado
            $encabezados[0] = 'NUM';
            $encabezados[1] = 'PERSON_CODE_ID';
            $encabezados[2] = 'PREV_GOV_ID';
            $encabezados[3] = 'GOVERNMENT_ID';
            $encabezados[4] = 'LAST_NAME';
            $encabezados[5] = 'Last_Name_Prefix';
            $encabezados[6] = 'FIRST_NAME';
            $encabezados[7] = 'MIDDLE_NAME';
            $encabezados[8] = 'NAME';
            $encabezados[9] = 'ACADEMIC_YEAR';
            $encabezados[10] = 'ACADEMIC_TERM';
            $encabezados[11] = 'ACADEMIC_SESSION';
            $encabezados[12] = 'START_DATE';
            $encabezados[13] = 'END_DATE';
            $encabezados[14] = 'EVENT_ID';
            $encabezados[15] = 'PUBLICATION_NAME_1';
            $encabezados[16] = 'SECTION';
            $encabezados[17] = 'SERIAL_ID';
            $encabezados[18] = 'PROGRAM';
            $encabezados[19] = 'PROGRAM_DESC';
            $encabezados[20] = 'CURRICULUM';
            $encabezados[21] = 'FORMAL_TITLE';
            $encabezados[22] = 'CLASS_LEVEL';
            $encabezados[23] = 'CIP_CODE';
            $encabezados[24] = 'EVENT_STATUS';
            $encabezados[25] = 'GENERAL_ED';
            $encabezados[26] = 'DESC_GENERAL_ED';
            $encabezados[27] = 'ADDS';
            $encabezados[28] = 'BUILDING_CODE';
            $encabezados[29] = 'BUILD_NAME_1';
            $encabezados[30] = 'ROOM_ID';
            $encabezados[31] = 'ROOM_NAME';
            $encabezados[32] = 'DAY';
            $encabezados[33] = 'CODE_DAY';
            $encabezados[34] = 'START_CLASS';
            $encabezados[35] = 'END_CLASS';
            $encabezados[36] = 'SCHEDULED_MEETINGS';
            $encabezados[37] = 'PLANTILLA';
            $encabezados[38] = 'CONTACT_HR_SESSION';
            $encabezados[39] = 'FLAG_CLINIC';

            // Construir la parte de la consulta SQL para los nombres de las columnas
            $columnas = implode(", ", $encabezados);

            //echo $columnas;

            try{
                // Iterar sobre las filas del archivo CSV
                while (($fila = fgetcsv($csvFile)) !== false) {

                    $num = ($fila[0] == 'NULL') ? '' : $fila[0];
                    $person_code_id = ($fila[1] == 'NULL') ? '' : $fila[1];
                    $prev_gov_id = ($fila[2] == 'NULL') ? '' : $fila[2];
                    $government_id = ($fila[3] == 'NULL') ? '' : $fila[3];
                    $last_name = ($fila[4] == 'NULL') ? '' : $fila[4];
                    $last_name_prefix = ($fila[5] == 'NULL') ? '' : $fila[5];
                    $first_name = ($fila[6] == 'NULL') ? '' : $fila[6];
                    $middle_name = ($fila[7] == 'NULL') ? '' : $fila[7];
                    $nombre = ($fila[8] == 'NULL') ? '' : $fila[8];
                    $academic_year = ($fila[9] == 'NULL') ? '' : $fila[9];
                    $academic_term = ($fila[10] == 'NULL') ? '' : $fila[10];
                    $academic_session = ($fila[11] == 'NULL') ? '' : $fila[11];
                    $start_date = ($fila[12] == 'NULL') ? '' : date('Y-m-d', strtotime($fila[12]));
                    $end_date = ($fila[13] == 'NULL') ? '' : date('Y-m-d', strtotime($fila[13]));
                    $event_id = ($fila[14] == 'NULL') ? '' : $fila[14];
                    $publication_name_1 = ($fila[15] == 'NULL') ? '' : $fila[15];
                    $section = ($fila[16] == 'NULL') ? '' : $fila[16];
                    $serial_id = ($fila[17] == 'NULL') ? '' : $fila[17];
                    $program = ($fila[18] == 'NULL') ? '' : $fila[18];
                    $program_desc = ($fila[19] == 'NULL') ? '' : $fila[19];
                    $curriculum = ($fila[20] == 'NULL') ? '' : $fila[20];
                    $formal_title = ($fila[21] == 'NULL') ? '' : $fila[21];
                    $class_level = ($fila[22] == 'NULL') ? '' : $fila[22];
                    $cip_code = ($fila[23] == 'NULL') ? '' : $fila[23];
                    $event_status = ($fila[24] == 'NULL') ? '' : $fila[24];
                    $general_ed = ($fila[25] == 'NULL') ? '' : $fila[25];
                    $desc_general_ed = ($fila[26] == 'NULL') ? '' : $fila[26];
                    $adds = ($fila[27] == 'NULL') ? '' : $fila[27];
                    $building_code = ($fila[28] == 'NULL') ? '' : $fila[28];
                    $build_name_1 = ($fila[29] == 'NULL') ? '' : $fila[29];
                    $room_id = ($fila[30] == 'NULL') ? '' : $fila[30];
                    $room_name = ($fila[31] == 'NULL') ? '' : $fila[31];
                    $day = ($fila[32] == 'NULL') ? '' : $fila[32];
                    $code_day = ($fila[33] == 'NULL') ? '' : $fila[33];
                    $start_time = ($fila[34] == 'NULL') ? '' : date('H:i:s', strtotime($fila[34]));
                    $end_time = ($fila[35] == 'NULL') ? '' : date('H:i:s', strtotime($fila[35]));
                    $scheduled_meetings = ($fila[36] == 'NULL') ? '' : $fila[36];
                    $plantilla = ($fila[37] == 'NULL') ? '' : $fila[37];
                    $contact_hr_session = ($fila[38] == 'NULL') ? '' : $fila[38];
                    $flag_clinic = ($fila[39] == 'NULL') ? '' : $fila[39];

                    // Escapar los valores para prevenir inyección de SQL
                    $valoresEscapados = array_map(array($mysqli, 'real_escape_string'), $fila);

                    $valoresEscapados[0] = $num;
                    $valoresEscapados[1] = $person_code_id;
                    $valoresEscapados[2] = $prev_gov_id;
                    $valoresEscapados[3] = $government_id;
                    $valoresEscapados[4] = $last_name;
                    $valoresEscapados[5] = $last_name_prefix;
                    $valoresEscapados[6] = $first_name;
                    $valoresEscapados[7] = $middle_name;
                    $valoresEscapados[8] = $nombre;
                    $valoresEscapados[9] = $academic_year;
                    $valoresEscapados[10] = $academic_term;
                    $valoresEscapados[11] = $academic_session;
                    $valoresEscapados[12] = $start_date;
                    $valoresEscapados[13] = $end_date;
                    $valoresEscapados[14] = $event_id;
                    $valoresEscapados[15] = $publication_name_1;
                    $valoresEscapados[16] = $section;
                    $valoresEscapados[17] = $serial_id;
                    $valoresEscapados[18] = $program;
                    $valoresEscapados[19] = $program_desc;
                    $valoresEscapados[20] = $curriculum;
                    $valoresEscapados[21] = $formal_title;
                    $valoresEscapados[22] = $class_level;
                    $valoresEscapados[23] = $cip_code;
                    $valoresEscapados[24] = $event_status;
                    $valoresEscapados[25] = $general_ed;
                    $valoresEscapados[26] = $desc_general_ed;
                    $valoresEscapados[27] = $adds;
                    $valoresEscapados[28] = $building_code;
                    $valoresEscapados[29] = $build_name_1;
                    $valoresEscapados[30] = $room_id;
                    $valoresEscapados[31] = $room_name;
                    $valoresEscapados[32] = $day;
                    $valoresEscapados[33] = $code_day;
                    $valoresEscapados[34] = $start_time;
                    $valoresEscapados[35] = $end_time;
                    $valoresEscapados[36] = $scheduled_meetings;
                    $valoresEscapados[37] = $plantilla;
                    $valoresEscapados[38] = $contact_hr_session;
                    $valoresEscapados[39] = $flag_clinic;

                    // Construir la parte de la consulta SQL para los valores
                    $valores = implode("', '", $valoresEscapados);
                    // Construir y ejecutar la consulta SQL
                    $insert = "INSERT IGNORE INTO academic_schedules ($columnas) VALUES ('$valores');";
                    if ($mysqli->query($insert)) {
                        $message = 'Layout de Horarios Docentes Convertido y Cargado correctamente.';
                        $icon = 'success';
                    } else {
                        $message = 'Error cargando Horarios Docentes';
                        $icon = 'error';
                    }
                }
            } catch (mysqli_sql_exception $ex) {
                echo $insert;
                $message = 'Se ha producido un error al insertar los datos. Favor de cargar el archivo correcto.'; // . $ex -> getMessage();
                $icon = 'warning';
            }

            // Cerrar el csvFile de archivos y la conexión a la base de datos
            fclose($csvFile);
        } else {
            $message = 'Error cargano el Archivo';
            $icon = 'error';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envío de Horarios Docentes</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Carga de Horarios Docentes",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>