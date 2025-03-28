<?php

require '../../logic/conn.php';

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
$icon = '';
$prog_desc = '';

$program = $_POST['prog'];
$t_min = $_POST['t_minIn'];
$t_delay = $_POST['t_tolerance'];
$t_absence = $_POST['t_delay'];
$t_outMin = $_POST['t_minOut'];
$prog_desc = $_POST['progDesc'];
$send = $_POST['send'];

$sqlTolerance = "UPDATE academic_tolerance SET MIN_TIME = '$t_min', DELAY_CLASS = '$t_delay', MAX_CLASS = '$t_absence', MIN_END = '$t_outMin', MODIFIED_DATE = NOW(), MODIFIED_BY = '$user_active' WHERE CODE_PROGRAM = '$program'";
$sqlUpdateSchedule = "UPDATE academic_schedules SET MAX_BEFORE_CLASS = DATE_ADD(START_CLASS, INTERVAL -'$t_min' MINUTE), DELAY_CLASS = DATE_ADD(START_CLASS, INTERVAL '$t_delay' MINUTE), MAX_DELAY_CLASS = DATE_ADD(START_CLASS, INTERVAL '$t_absence' MINUTE), MIN_END_CLASS = DATE_ADD(END_CLASS, INTERVAL -'$t_outMin' MINUTE) WHERE PROGRAM = '$program'";
if ($mysqli->query($sqlTolerance) === true && $mysqli->query($sqlUpdateSchedule) === true) {
    $message = 'Tolerancias ' . $prog_desc . ' Actualizadas con Éxito';
    $icon = 'success';
} else {
    $message = 'Error Actualizando Tolerancias de ' . $prog_desc;
    $icon = 'error';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tolerancias</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
</head>

<body>
    <script type="text/javascript">
        swal({
            title: "Generar Tiempos de Tolerancia",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>&sn=<?php echo $send ?>";
        });
    </script>
</body>

</html>