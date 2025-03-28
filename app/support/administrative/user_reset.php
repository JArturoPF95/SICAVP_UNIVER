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

$idUser = $_GET['id'];
$reset_message = '';
$reset_icon = '';

//Obtiene la contraseña temporal para su nuevo acceso
$sqlGetPassTemp = "SELECT 
    ACCESS_LEVEL, CONCAT(SUBSTRING(GOVERNMENT_ID,9,2),SUBSTRING(GOVERNMENT_ID,7,2),SUBSTRING(GOVERNMENT_ID,5,2)) BDAY
    , FLAG_CONFIRM
FROM users 
LEFT OUTER JOIN employed ON users.SICAVP_USER = employed.ID_NOM
WHERE SICAVP_USER = '$idUser'";
$resultPassTemp = $mysqli->query($sqlGetPassTemp);
if ($resultPassTemp->num_rows > 0) {
    while ($rowPT = $resultPassTemp->fetch_assoc()) {
        $access = $rowPT['ACCESS_LEVEL'];
        $flagConfirm = $rowPT['FLAG_CONFIRM'];
        if ($access == '1' || $access == '2') {
            $passNew = $rowPT['BDAY'];
            $message2 = 'Recuerda que la contraseña es la fecha de nacimiento del usuario en formato: ddmmaa';
        } elseif ($access == '3' || $access == '5') {
            $passNew = 'ABCD1234';
            $message2 = 'Recuerda que la contraseña es ABCD1234';
        }
    }
}

//Validamos si el usuario ya confirmó su correo
if ($flagConfirm == '1') {
    //Actualiza sólo contraseña en caso de que haya sido así
    $sql_userReset = "UPDATE users SET PASSWORD = '', PASS_TEMP = '$passNew', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE SICAVP_USER = '$idUser' AND SEPARATION_FLAG = '0'";
    if ($mysqli->query($sql_userReset) === TRUE) {
        $reset_message = 'Accesos reseteados con éxito. ' . $message2 . '.';
        $reset_icon = 'success';
    } else {
        $reset_message = 'Error reseteando accesos';
        $reset_icon = 'error';
    }
} else {
    //Resetea correo y contraseña si no se ha confirmado correo previamente
    $sql_userReset = "UPDATE users SET PASSWORD = '', PASS_TEMP = '$passNew', EMAIL = '', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW()  WHERE SICAVP_USER = '$idUser' AND SEPARATION_FLAG = '0'";
    if ($mysqli->query($sql_userReset) === TRUE) {
            $reset_message = 'Accesos reseteados con éxito. Recuerda que la contraseña es la fecha de nacimiento del usuario en formato ddmmaa.';
        $reset_icon = 'success';
    } else {
        $reset_message = 'Error reseteando accesos';
        $reset_icon = 'error';
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
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Contraseña Reseteada",
            text: "<?php echo $reset_message; ?>",
            icon: "<?php echo $reset_icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../admin_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>