<?php
date_default_timezone_set('America/Mexico_City');
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

$idUser = $_GET['id'];
$return_message = '';
$return_icon = '';

$sqlValFlag = "SELECT * FROM users WHERE SICAVP_USER = '$idUser'";
$resultFlag = $mysqli->query($sqlValFlag);
if ($resultFlag -> num_rows > 0) {
    while ($rowFlag = $resultFlag -> fetch_assoc()) {
        $flagConfirm = $rowFlag['FLAG_CONFIRM'];
    }
}

if ($flagConfirm == '1') {
    $sqlUpdatePass = "UPDATE users SET PASSWORD = '', PASS_TEMP = '$idUser', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE SICAVP_USER = '$idUser'";
    if ($mysqli->query($sqlUpdatePass) === TRUE) {
        $return_message = 'Acceso reseteado con éxito. Recuerda que la contraseña es la misma que el usuario';
        $return_icon = 'success';
    } else {
        $return_message = 'Error Reseteando Contraseña';
        $return_icon = 'error';
    }
} else {
    $sqlUpdatePass = "UPDATE users SET PASSWORD = '', PASS_TEMP = '$idUser', EMAIL = '', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE SICAVP_USER = '$idUser'";
    if ($mysqli->query($sqlUpdatePass) === TRUE) {
        $return_message = 'Acceso reseteado con éxito. Recuerda que la contraseña es la misma que el usuario';
        $return_icon = 'success';
    } else {
        $return_message = 'Error Reseteando Contraseña';
        $return_icon = 'error';
    }
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraseñas</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Reseteo de Contraseña",
            text: "<?php echo $return_message; ?>",
            icon: "<?php echo $return_icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>