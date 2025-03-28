<?php

require_once '../../../logic/conn.php';

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

$message_val = '';
$icon_check = '';
$justify = '';
$title = 'Justificación Validada';
$date = '';
$incidence = '';

if (isset($_POST['attendance'])) {

    foreach ($_POST['attendance'] as $pk => $attendance) {
        $nomPeriod = $attendance['payrollNom'] ?? '';
        $attendancePk = $attendance['pk'] ?? '';
        $justified = $attendance['justify'] ?? '';
        $inciAct = $attendance['incidence'] ?? '';

        if ($justified === 'Y') {

            if ($inciAct == 'FALTA POR RETARDOS') {
                $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'TIEMPO POR TIEMPO'";
                $resultIncidence = $mysqli->query($sqlIncidence);
                if ($resultIncidence->num_rows > 0) {
                    while ($rowIn = $resultIncidence->fetch_assoc()) {
                        $incidence = $rowIn['CODE_TINC'];
                    }
                }
            } else {
                $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'ASISTENCIA'";
                $resultIncidence = $mysqli->query($sqlIncidence);
                if ($resultIncidence->num_rows > 0) {
                    while ($rowIn = $resultIncidence->fetch_assoc()) {
                        $incidence = $rowIn['CODE_TINC'];
                    }
                }
            }

            

            $sql_valJustify = "UPDATE admin_attendance SET JUSTIFY = '$justified', JUSTIFIED_BY = '$user_active', JUSTIFIED_DATE = NOW(), TINC = '$incidence' WHERE AttendanceId = '$attendancePk'";
            if ($mysqli->query($sql_valJustify)) {
                $message_val = 'Solicitudes validadas';
                $icon_check = 'success';
            } else {
                $message_val = 'Error Justificando \n Favor de intentar de nuevo';
                $icon_check = 'error';
            }
        } elseif ($justified === 'N') {
            $sql_valJustify = "UPDATE admin_attendance SET JUSTIFY = '$justified', JUSTIFIED_BY = '$user_active', JUSTIFIED_DATE = NOW() WHERE AttendanceId = '$attendancePk'";
            if ($mysqli->query($sql_valJustify)) {
                $message_val = 'Solicitudes validadas';
                $icon_check = 'success';
            } else {
                $message_val = 'Error Justificando \n Favor de intentar de nuevo';
                $icon_check = 'error';
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
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
    <title>Asistencias del día</title>
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "<?php echo $title; ?>",
            text: "<?php echo $message_val; ?>",
            icon: "<?php echo $icon_check ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../justify_delays.php?id=<?php echo $user_active ?>&pe=<?php echo $nomPeriod ?>";
        });
    </script>

</body>

</html>