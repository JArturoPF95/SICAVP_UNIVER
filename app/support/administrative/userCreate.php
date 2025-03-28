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

$idNom = $_POST['idnom'];
$sesion = $_POST['sesion'];
$access = $_POST['access'];
$passTemp = 'ABCD1234';

$userIcon = '';
$userMessage = '';

$getLocation = "SELECT DISTINCT CODE_SESION_NOM, CODE_CITY FROM code_sesion WHERE PK = '$sesion'";
$resultLocation = $mysqli -> query($getLocation);
if ($resultLocation -> num_rows > 0) {
    while ($rowLocation = $resultLocation -> fetch_assoc()) {
        $city = $rowLocation['CODE_CITY'];
        $nomSession = $rowLocation['CODE_SESION_NOM'];
    }
}

try{

    $insertUser = "INSERT INTO users(SICAVP_USER, PASS_TEMP, PASSWORD, NOM_SESSION, CITY, PAYROLL, ACCESS_LEVEL, SEPARATION_FLAG, FLAG_CONFIRM, CREATED_BY, CREATED_DATE) 
    VALUES ('$idNom', '$passTemp', '', '$nomSession', '$city', 1, '$access', 0, 0, '$user_active', DATE(NOW()) )";
    if ($mysqli->query($insertUser) === TRUE) {
        $userIcon = 'success';
        $userMessage = 'Usuario Creado con Éxito';
    } else {
        $userIcon = 'error';
        $userMessage = 'Error Creando Usuario';
    }
} catch (mysqli_sql_exception $ex) {
    $userIcon = 'warning';
    $userMessage = 'Error Creando Usuario. Intentar de nuevo';
    echo $insertUser;
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Alta de Usuario",
            text: "<?php echo $userMessage; ?>",
            icon: "<?php echo $userIcon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../admin_users.php?id=<?php echo $user_active ?>";
        });
    </script>

</body>

</html>