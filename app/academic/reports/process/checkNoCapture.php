<?php

require '../../../../lib/vendor/autoload.php';
require '../../../logic/conn.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
    $user_sesion = $_SESSION['session'];
    $user_city = $_SESSION['city'];
}

$id = $_GET['id'];
$statusNoC = $_GET['in'];
$prog = $_GET['pr'];
$sendF = $_GET['sf'];

$inOut = '1';
$getTime = date('H:i:s');
$code_day = date('w');

//echo $code_day;

//echo $id . ' - ' . $statusNoC . ' - ' . $prog . ' - ' . $sendF . '<br>';

$sql_get_incidence = "SELECT DISTINCT
PK, CODE_DAY, ASH.ACADEMIC_SESSION, PROGRAM, CURRICULUM, EVENT_ID, START_CLASS, END_CLASS, PERSON_CODE_ID,
ROOM_ID, MAX_DELAY_CLASS, MIN_END_CLASS, DELAY_CLASS, MAX_BEFORE_CLASS, GENERAL_ED, SERIAL_ID, SECTION
FROM academic_schedules ASH
WHERE ASH.PK = '$id' AND (DATE(NOW()) BETWEEN ASH.START_DATE AND ASH.END_DATE)";

//echo $sql_get_incidence  . '<br>';

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
        $incidence_person_id = $row_get_incidence['PERSON_CODE_ID'];
        
        /** Obtener la incdencia */
        switch ($statusNoC) {
            case 'fa':
                $incidence = '01';
                break;
            case 'as':
                $incidence = '21';
                break;            
            case 're':
                //Validamos los retardos y faltas por retardos de la quincena en cuestión
                $sql_countDelays = "SELECT DISTINCT AAT.EVENT_ID, COUNT(AAT.TINC) TINC
                    FROM academic_attendance AAT
                    INNER JOIN payroll_period PRP ON AAT.ATTENDANCE_DATE BETWEEN PRP.START_DATE AND PRP.END_DATE
                    WHERE date(now()) BETWEEN PRP.START_DATE AND PRP.END_DATE AND AAT.TINC IN (22,02) AND AAT.IN_OUT = 1 AND AAT.ACADEMIC_ID = '$incidence_person_id'
                    GROUP BY AAT.EVENT_ID;";
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
                break;
        }

    }
}
$sql_insert_check = "INSERT INTO academic_attendance (SCHEDULE_ID, ACADEMIC_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, SESSION, PROGRAM,
CURRICULUM, GENERAL_ED, ROOM_ID, EVENT_ID, SECTION, SERIAL_ID, START_CLASS, END_CLASS, TINC, IN_OUT, CLASS_SUMMARY) 
VALUES ('$incidence_time_pk', '$incidence_person_id','$code_day',DATE(NOW()),'$getTime','$incidence_time_session','$incidence_time_program','$incidence_time_curriculum'
,'$incidence_time_generalEd','$incidence_time_room','$incidence_time_event','$incidence_time_section','$incidence_time_serialID','$incidence_time_start'
,'$incidence_time_end', '$incidence','1', '')";

//echo $sql_insert_check . '<br>';
if ($mysqli->query($sql_insert_check) === true) {
    $message_check = 'Estatus sin captura actualizado con éxito';
    $icon_check = 'success';
    $url = '../../teachers.php?id='.$user_active.'&p='.$prog.'&sn='.$sendF;
} else {
    $message_check = 'Error registrando estatus';
    $icon_check = 'error';
    $url = '../../teachers.php?id='.$user_active.'&p='.$prog.'&sn='.$sendF;
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