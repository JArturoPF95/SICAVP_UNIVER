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

$payrollPeriodID = '';

$message_check = '';
$incidence = '';
$delays = 0;
$flagClinic = '';

$getTime = date('H:i:s');
$today = '';
$code_day = '';

$idClass = $_POST['id_class'];
$check = $_POST['check'];
if (isset($_POST['summary'])) {
    $summary = $_POST['summary'];
} else {
    $summary = '';
}
if (isset($_POST['id_teacher'])) {
    $supDoc = $_POST['id_teacher'];
} else {
    $supDoc = '';
}
if (isset($_POST['prog'])) {
    $program = $_POST['prog'];
} else {
    $program = '';
}
if (isset($_POST['send'])) {
    $sendFlag = $_POST['send'];
} else {
    $sendFlag = '';
}
if (isset($_POST['selDat'])) {
    $dateSel = $_POST['selDat'];
} else {
    $dateSel = '';
}

//echo $idClass . ' ' . $supDoc . ' ' . $program . ' ' . $sendFlag . ' ' . $summary . ' ' .  $check . ' ' . $dateSel . '<br>';

/*
$session = $_POST['session'];
$degree = $_POST['degree'];
$program = $_POST['program'];
$curriculum = $_POST['curriculum'];
$event = $_POST['event'];
$room = $_POST['room'];
$start = $_POST['c_start'];
$end = $_POST['c_end'];
*/
if ($check != '3') {    
    $sql_get_incidence = "SELECT DISTINCT
    PK, CODE_DAY, ASH.ACADEMIC_SESSION, PROGRAM, CURRICULUM, EVENT_ID, START_CLASS, END_CLASS, PERSON_CODE_ID,
    ROOM_ID, MAX_DELAY_CLASS, MIN_END_CLASS, DELAY_CLASS, MAX_BEFORE_CLASS, GENERAL_ED, SERIAL_ID, SECTION
    FROM academic_schedules ASH
    WHERE ASH.PK = '$idClass' AND (DATE(NOW()) BETWEEN ASH.START_DATE AND ASH.END_DATE)";
} else {
    $sql_get_incidence = "SELECT DISTINCT
    PK, CODE_DAY, ASH.ACADEMIC_SESSION, PROGRAM, CURRICULUM, EVENT_ID, START_CLASS, END_CLASS, PERSON_CODE_ID,
    ROOM_ID, MAX_DELAY_CLASS, MIN_END_CLASS, DELAY_CLASS, MAX_BEFORE_CLASS, GENERAL_ED, SERIAL_ID, SECTION
    FROM academic_schedules ASH
    WHERE ASH.PK = '$idClass' AND ('$dateSel' BETWEEN ASH.START_DATE AND ASH.END_DATE)";
}

//echo $sql_get_incidence;

$result_get_incidence = $mysqli->query($sql_get_incidence);
if ($result_get_incidence->num_rows > 0) {
    while ($row_get_incidence = $result_get_incidence->fetch_assoc()) {
        $incidence_time_pk = $row_get_incidence['PK'];
        $incidence_time_start = $row_get_incidence['START_CLASS'];
        $incidence_time_end = $row_get_incidence['END_CLASS'];
        $incidence_time_absence = $row_get_incidence['MAX_DELAY_CLASS'];
        $incidence_time_before = $row_get_incidence['MIN_END_CLASS'];
        $incidence_time_room = $row_get_incidence['ROOM_ID'];
        $incidence_time_event = $row_get_incidence['EVENT_ID'];
        $incidence_time_curriculum = $row_get_incidence['CURRICULUM'];
        $incidence_time_program = $row_get_incidence['PROGRAM'];
        $incidence_time_delay = $row_get_incidence['DELAY_CLASS'];
        $incidence_time_before = $row_get_incidence['MAX_BEFORE_CLASS'];
        $incidence_time_session = $row_get_incidence['ACADEMIC_SESSION'];
        $incidence_time_generalEd = $row_get_incidence['GENERAL_ED'];
        $incidence_time_serialID = $row_get_incidence['SERIAL_ID'];
        $incidence_time_section = $row_get_incidence['SECTION'];

        if ($check == 1) {
            $idDoc = $user_active;
            $inOut = '1';
            $attDate = date('Y-m-d');
            $code_day = date('w', strtotime($attDate)); //Obtenemos la clave del día (0-6)

            //echo $idDoc . ' ' . $attDate . ' ' . $code_day . '<br>';
            if ($getTime <= $incidence_time_delay && $getTime >= $incidence_time_before) {
                $incidence = 21;
            } elseif ($getTime > $incidence_time_delay && $getTime <= $incidence_time_absence) {
                require_once 'query.php';
                //Validamos los retardos y faltas por retardos de la quincena en cuestión
                $sql_countDelays;
                $result_countDelays = $mysqli->query($sql_countDelays);
                if ($result_countDelays->num_rows > 0) {
                    while ($row_countDelay = $result_countDelays->fetch_assoc()) {
                        $delay_incidence = $row_countDelay['TINC'];

                        if ((($delay_incidence + 1) % 3) == 0) {
                            $incidence = 02;
                            //echo $check . 'Aplica Falta por Retardos ' . $getTime . '<br>';
                        } else {
                            $incidence = 22;
                            //echo $check . 'Aplica Retardo ' . $getTime . '<br>';
                        }
                    }
                    //Si está en horario de retardo y no tiene retardos antes le pone el primero
                } else {
                    $incidence = 22;
                }
            } elseif ($getTime >= $incidence_time_absence) {
                $incidence = 1;
            }
        } elseif ($check == '2') {
            $incidence = '0';
            $inOut = '2';
            $idDoc = $user_active;
            $attDate = date('Y-m-d');
            $code_day = date('w', strtotime($attDate)); //Obtenemos la clave del día (0-6)
            //echo $idDoc . ' ' . $attDate . ' ' . $code_day . '<br>';
        } elseif ($check == '3') {
            $idDoc = $supDoc;
            $inOut = '1';
            $attDate = $dateSel;
            $getTime = $incidence_time_start;
            $code_day = date('w', strtotime($dateSel));
            //echo $idDoc . ' ' . $attDate . ' ' . $code_day . '<br>';
            $sqlGetCodeIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC LIKE '%SUPLENTE%'";
            $resultGetCodeIncidence = $mysqli -> query($sqlGetCodeIncidence);
            if ($resultGetCodeIncidence -> num_rows > 0) {
                while ($rowGetCodeIncidence = $resultGetCodeIncidence -> fetch_assoc()) {
                    $incidence = $rowGetCodeIncidence['CODE_TINC'];
                }
            }

            /*$sql_get_incidence_noSup = "SELECT DISTINCT
            PK, CODE_DAY, ASH.ACADEMIC_SESSION, PROGRAM, CURRICULUM, EVENT_ID, START_CLASS, END_CLASS, PERSON_CODE_ID,
            ROOM_ID, MAX_DELAY_CLASS, MIN_END_CLASS, DELAY_CLASS, MAX_BEFORE_CLASS, GENERAL_ED, SERIAL_ID, SECTION
            FROM academic_schedules ASH
            WHERE ASH.PK = '$idClass' AND ('$dateSel' BETWEEN ASH.START_DATE AND ASH.END_DATE)";
            $resultGetIncidenceNoSup = $mysqli -> query($sql_get_incidence_noSup);
            if ($resultGetIncidenceNoSup -> num_rows > 0) {
                while ($rowGetIncidenceNoSup = $resultGetIncidenceNoSup -> fetch_assoc()) {
                    $incidenceNoSup_time_pk = $rowGetIncidenceNoSup['PK'];
                    $incidenceNoSup_time_start = $rowGetIncidenceNoSup['START_CLASS'];
                    $incidenceNoSup_time_end = $rowGetIncidenceNoSup['END_CLASS'];
                    $incidenceNoSup_time_absence = $rowGetIncidenceNoSup['MAX_DELAY_CLASS'];
                    $incidenceNoSup_time_before = $rowGetIncidenceNoSup['MIN_END_CLASS'];
                    $incidenceNoSup_time_room = $rowGetIncidenceNoSup['ROOM_ID'];
                    $incidenceNoSup_time_event = $rowGetIncidenceNoSup['EVENT_ID'];
                    $incidenceNoSup_time_curriculum = $rowGetIncidenceNoSup['CURRICULUM'];
                    $incidenceNoSup_time_program = $rowGetIncidenceNoSup['PROGRAM'];
                    $incidenceNoSup_time_delay = $rowGetIncidenceNoSup['DELAY_CLASS'];
                    $incidenceNoSup_time_before = $rowGetIncidenceNoSup['MAX_BEFORE_CLASS'];
                    $incidenceNoSup_time_session = $rowGetIncidenceNoSup['ACADEMIC_SESSION'];
                    $incidenceNoSup_time_generalEd = $rowGetIncidenceNoSup['GENERAL_ED'];
                    $incidenceNoSup_time_serialID = $rowGetIncidenceNoSup['SERIAL_ID'];
                    $incidenceNoSup_time_section = $rowGetIncidenceNoSup['SECTION'];
                    $incidenceNoSup_time_pcd = $rowGetIncidenceNoSup['PERSON_CODE_ID'];

                    $sql_insert_check = "INSERT INTO academic_attendance (SCHEDULE_ID, ACADEMIC_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, SESSION, PROGRAM,
                    CURRICULUM, GENERAL_ED, ROOM_ID, EVENT_ID, SECTION, SERIAL_ID, START_CLASS, END_CLASS, TINC, IN_OUT, CLASS_SUMMARY) 
                    VALUES ('$incidenceNoSup_time_pk', '$incidenceNoSup_time_pcd','$code_day','$attDate','00:00:00','$incidenceNoSup_time_session','$incidenceNoSup_time_program'
                    ,'$incidenceNoSup_time_curriculum','$incidenceNoSup_time_generalEd','$incidenceNoSup_time_room','$incidenceNoSup_time_event','$incidenceNoSup_time_section'
                    ,'$incidenceNoSup_time_serialID','$incidenceNoSup_time_start','$incidenceNoSup_time_end', '21','1', '')";
                    if ($mysqli->query($sql_insert_check) === true) {
                    }
                }
            }*/
        }

        $sql_insert_check = "INSERT INTO academic_attendance (SCHEDULE_ID, ACADEMIC_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, SESSION, PROGRAM,
CURRICULUM, GENERAL_ED, ROOM_ID, EVENT_ID, SECTION, SERIAL_ID, START_CLASS, END_CLASS, TINC, IN_OUT, CLASS_SUMMARY) 
VALUES ('$incidence_time_pk', '$idDoc','$code_day','$attDate','$getTime','$incidence_time_session','$incidence_time_program','$incidence_time_curriculum'
,'$incidence_time_generalEd','$incidence_time_room','$incidence_time_event','$incidence_time_section','$incidence_time_serialID','$incidence_time_start'
,'$incidence_time_end', '$incidence','$inOut', '$summary')";

        //echo $sql_insert_check;
        if ($mysqli->query($sql_insert_check) === true) {
            if ($check == 1) {
                $message_check = 'Clase Registrada con éxito';
                $icon_check = 'success';
                $url = '../../last_today.php?id='.$user_active;
            } elseif ($check == 2) {
                $message_check = 'Salida Registrada con éxito';
                $icon_check = 'success';
                $url = '../../last_today.php?id='.$user_active;
            } else {
                $message_check = 'Suplencia Registrada con éxito';
                $icon_check = 'success';
                $url = '../../teachers.php?id='.$user_active.'&p='.$program.'&sn='.$sendFlag.'&sd='.$dateSel;
            }
        } else {
            $message_check = 'No se pudo Registrar';
            $icon_check = 'error';
            if ($check == '1' || $check == '2') {
                $url = '../../last_today.php?id='.$user_active;
            } else {
                $url = '../../teachers.php?id='.$user_active.'&p='.$program.'&sn='.$sendFlag.'&sd='.$dateSel;
            }
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
            title: "Registro",
            text: "<?php echo $message_check; ?>",
            icon: "<?php echo $icon_check ?>",
            button: "Volver",
        }).then(function() {
            window.location = "<?php echo $url ?>";
        });
    </script>
</body>

</html>