<?php

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
}

$ip = $_POST['ip'];
$sesion = $_POST['sesion'];
$name = $_POST['ipName'];
$message = '';
$icon = '';

$getSesion = "SELECT DISTINCT CODE_SESION_NOM, CODE_CITY FROM code_sesion WHERE PK = '$sesion'";
$resultSesion = $mysqli -> query($getSesion);
if ($resultSesion -> num_rows > 0) {
    while ($rowSesion = $resultSesion -> fetch_assoc()) {
        $sesionCode = $rowSesion['CODE_SESION_NOM'];
        $sesionCity = $rowSesion['CODE_CITY'];

    }
}

$sqlInsertIP = "INSERT INTO code_ip (IP, IP_NAME, CODE_SESION, CODE_CITY, CREATED_BY, CREATED_DATE) 
VALUES ('$ip','$name','$sesionCode','$sesionCity','$user_active',NOW())";
if ($mysqli->query($sqlInsertIP)) {
    $message = 'IP Agregada con Éxito';
    $icon = 'success';
} else {
    $message = 'Error Agregando IP';
    $icon = 'error';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPs</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Catálogo de IPs",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../ip.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>