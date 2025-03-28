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
if (isset($_GET['pr'])) {
    $program = $_GET['pr'];
} else {
    $program = '';
}
if (isset($_GET['s'])) {
    $sendFlag = $_GET['s'];
} else {
    $sendFlag = '';
}
if (isset($_GET['dt'])) {
    $dateSel = $_GET['dt'];
} else {
    $dateSel = '';
}

//echo $id . ' ' . $program . ' ' . $sendFlag . ' ' . $dateSel . '<br>';

$deleteSup = "DELETE FROM academic_attendance WHERE AttendanceId = '$id'";
if ($mysqli -> query($deleteSup)) {
    $message_check = 'Suplente eliminado correctamente';
    $icon_check = 'success';
    $url = '../../teachers.php?id='.$user_active.'&p='.$program.'&sn='.$sendFlag.'&sd='.$dateSel;
} else {
    $message_check = 'Error eliminando suplente';
    $icon_check = 'error';
    $url = '../../teachers.php?id='.$user_active.'&p='.$program.'&sn='.$sendFlag.'&sd='.$dateSel;
}

//echo $deleteSup;

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