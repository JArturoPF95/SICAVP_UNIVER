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

$getTime = date('H:i:s');
$getDate = date('Y-m-d');
$codeDay = date('w');
$incidence = '';
$payrollPeriodID = '';

$justify_comment = $_POST['comment'];

//echo $getDate . ' ' . $user_active . ' ' . $justify_comment;

$sql_valInsertJustify = "SELECT DISTINCT NAME_DAY, ATTENDANCE_DATE, AttendanceId,
    IFNULL( (SELECT INCI.DESCRIP_TINC FROM admin_attendance ATE INNER JOIN code_incidence INCI ON INCI.CODE_TINC = ATE.TINC WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 1 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), '') TINC,
    IFNULL( (SELECT ATE.ATTENDANCE_TIME FROM admin_attendance ATE WHERE ATE.ATTENDANCE_DATE = ATT.ATTENDANCE_DATE AND ATE.NOM_ID = ATT.NOM_ID AND ATE.IN_OUT = 1 ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1), '') CHECKIN
    FROM admin_attendance ATT
    INNER JOIN code_days DYS ON DYS.CODE_DAY = ATT.CODE_DAY
    WHERE NOM_ID = '$user_active' AND ATT.ATTENDANCE_DATE = DATE(NOW()) 
    ORDER BY ATTENDANCE_TIME ASC LIMIT 1";
$result_valInsertJustify = $mysqli->query($sql_valInsertJustify);
if ($result_valInsertJustify->num_rows > 0) {
while ($rowJust = $result_valInsertJustify -> fetch_assoc()) {

    $idAtte = $rowJust['AttendanceId'];

    $sql_updateJustify = "UPDATE admin_attendance SET COMMENTS = '$justify_comment', JUSTIFY = 'P'
    WHERE AttendanceId = '$idAtte';";
        if ($mysqli->query($sql_updateJustify) === true) {
            $title = 'Justificación Enviada';
            $message = 'Se solicitó justificación con éxito';
            $icon = 'success';
        } else {
            $title = '¡¡Error!!';
            $message = 'Se produjo un error solicitando Justificación \n Favor de intentar nuevamente';
            $icon = 'error';
        }
}
} else {

    //Validamos si es retardo o falta
    $sqlValTime = "SELECT EMP.ID_NOM, ASH.* FROM admin_schedules ASH
    INNER JOIN employed EMP ON EMP.SCHEDULE_GROUP = ASH.CODE_SCHEDULE
    WHERE EMP.ID_NOM = '$user_active' AND CODE_DAY = '$codeDay';";
    $resultValTime = $mysqli -> query($sqlValTime);
    if ($resultValTime -> num_rows > 0) {
        while ($rowValTime = $resultValTime -> fetch_assoc()) {
            if ($getTime <= $rowValTime['AUSENCE_TIME']) {

                require_once 'query_attendance.php';
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
            } elseif ($getTime > $rowValTime['AUSENCE_TIME']) {
                $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'FALTA INJUSTIFICADA';";
                $resultIncidence = $mysqli -> query($sqlIncidence);
                if ($resultIncidence -> num_rows > 0) {
                    while ($rowIn = $resultIncidence -> fetch_assoc()) {
                        $incidence = $rowIn['CODE_TINC'];

                    }
                }
            }
        }
    }

    $sql_insertJustify = "INSERT INTO admin_attendance 
(NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG, COMMENTS, JUSTIFY) 
VALUES ('$user_active','$codeDay',DATE(NOW()),'$getTime', '$incidence', 1, 'S', '$justify_comment','P');";
    if ($mysqli->query($sql_insertJustify) === true) {
        $title = 'Justificación Enviada';
        $message = 'Se solicitó justificación con éxito';
        $icon = 'success';
    } else {
        $title = '¡¡Error!!';
        $message = 'Se produjo un error solicitando Justificación \n Favor de intentar nuevamente';
        $icon = 'error';
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencias del día</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
</head>

<body>
    <script type="text/javascript">
        swal({
            title: "<?php echo $title; ?>",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../../last_today.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>