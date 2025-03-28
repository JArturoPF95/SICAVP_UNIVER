<?php

require_once '../../../logic/conn.php';

date_default_timezone_set('America/Mexico_City');

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$message_check = '';
$check = $_GET['check'];
$getTime = date('H:i:s');
$getDate = date('Y-m-d');
$codeDay = date('w'); //Obtenemos la clave del día (0-6)
$delays = 0;
$payrollPeriodID = '';

require_once 'query_attendance.php';

//echo $check;

$sql_get_incidence;
$result_get_incidence = $mysqli->query($sql_get_incidence);
if ($result_get_incidence->num_rows > 0) {
    while ($row_get_incidence = $result_get_incidence->fetch_assoc()) {
        $incidence_time_start = $row_get_incidence['TIME_START'];
        $incidence_delay_time = $row_get_incidence['DELAY_TIME_START'];
        $incidence_ausence_time = $row_get_incidence['AUSENCE_TIME'];
        $incidence_time_out = $row_get_incidence['OUT_TIME'];
        $incidence_before_out = $row_get_incidence['MIN_TIME_OUT'];

        //echo $check;

        /** Captura la entrada */
        if ($check == 1) {
            /** Captura asistencia si aún no llega al tiempo de retardo */
            $flag_left_ant = 0;
            if ($getTime <= $incidence_delay_time) {

                //echo 'Entra con Asistencia';
                $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'ASISTENCIA';";
                $resultIncidence = $mysqli -> query($sqlIncidence);
                if ($resultIncidence -> num_rows > 0) {
                    while ($rowIn = $resultIncidence -> fetch_assoc()) {
                        $incidence = $rowIn['CODE_TINC'];
                    }
                }
                /** Valida la asistencia si entra entre el tiempo de retardo y de ausencia */
            } elseif ($getTime > $incidence_delay_time && $getTime <= $incidence_ausence_time) {
                //echo 'Entra con Retardo';
                //Validamos los retardos y faltas por retardos de la quincena en cuestión
                $sql_countDelays;
                $result_countDelays = $mysqli->query($sql_countDelays);
                if ($result_countDelays->num_rows > 0) {
                    while ($row_countDelay = $result_countDelays->fetch_assoc()) {
                        $delay_incidence = $row_countDelay['TINC'];

                        $delays++;
                        
                        /** Cuenta los retardos, si ya lleva 2, en la tercera (que es esta) ya genera falta por retardos */
                        if ((($delays + 1) % 3) == 0) {
                            $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'FALTA POR RETARDOS';";
                                $resultIncidence = $mysqli -> query($sqlIncidence);
                                if ($resultIncidence -> num_rows > 0) {
                                while ($rowIn = $resultIncidence -> fetch_assoc()) {
                                    $incidence = $rowIn['CODE_TINC'];
                                }
                            }
                        // Si apun no cuenta con  los retardos procede a cargar un retardo.
                        } else {
                            $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'RETARDO';";
                            $resultIncidence = $mysqli -> query($sqlIncidence);
                            if ($resultIncidence -> num_rows > 0) {
                                while ($rowIn = $resultIncidence -> fetch_assoc()) {
                                    $incidence = $rowIn['CODE_TINC'];

                                }
                            }
                        }

                    }
                    /** No cuenta aún con retardos, es el primero */
                } else {
                    $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'RETARDO';";
                    $resultIncidence = $mysqli -> query($sqlIncidence);
                    if ($resultIncidence -> num_rows > 0) {
                        while ($rowIn = $resultIncidence -> fetch_assoc()) {
                            $incidence = $rowIn['CODE_TINC'];

                        }
                    }
                }

                /** Si el tiempo ya excede el retardo se genera "Falta Injustificada" */
            } elseif ($getTime > $incidence_ausence_time) {
                //echo 'Entra con Falta';
                $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'FALTA INJUSTIFICADA';";
                $resultIncidence = $mysqli -> query($sqlIncidence);
                if ($resultIncidence -> num_rows > 0) {
                    while ($rowIn = $resultIncidence -> fetch_assoc()) {
                        $incidence = $rowIn['CODE_TINC'];
                    }
                }
            }
        } else {
            $incidence = 0;
            
        }

        //echo $incidence;

        /** Inserta el registro */
        $sql_insert_check = "INSERT INTO admin_attendance (NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG, JUSTIFY, COMMENTS) 
            VALUES ('$user_active','$codeDay','$getDate','$getTime','$incidence','$check', 'S', '', '')";
        if ($mysqli->query($sql_insert_check) === true) {
            if ($check == 1) {
                $message_check = 'Entrada Registrada con éxito';
                $icon_check = 'success';
            } else {
                $message_check = 'Salida Registrada con éxito';
                $icon_check = 'success';
            }
        } else {
            $message_check = 'No se pudo Registrar';
            $icon_check = 'error';
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
    <title>Registro de Asistencia</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
</head>

<body>
    <script type="text/javascript">
        swal({
            title: "Registro Exitoso",
            text: "<?php echo $message_check; ?>",
            icon: "<?php echo $icon_check ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../../last_today.php?id=<?php echo $user_active ?>";
        });
    </script>
</body>

</html>