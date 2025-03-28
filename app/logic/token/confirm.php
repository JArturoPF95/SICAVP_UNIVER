<?php 
require '../conn.php'; // Conexión a la BD


function decryptId($encryptedId) {
    $key = "mi_clave_secreta";
    return openssl_decrypt(hex2bin($encryptedId), 'aes-256-cbc', $key, 0, substr(md5($key), 0, 16));
}


if (isset($_GET['token'])) {
    $id = decryptId($_GET['token']);

    // $sqlName = "(SELECT CONCAT(NAME,' ',LAST_NAME) USR_NAME FROM employed WHERE ID_NOM = '$id') UNION (SELECT CONCAT(NAME,' ',LAST_NAME) USR_NAME FROM academic_schedules WHERE PERSON_CODE_ID = '$id')";
    $sqlName = "SELECT DISTINCT USR.SICAVP_USER
        , CASE
            WHEN EMP.NAME IS NOT NULL THEN CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX)
            WHEN ASH.NAME IS NOT NULL THEN CONCAT(ASH.NAME,' ',ASH.LAST_NAME,' ',ASH.LAST_NAME_PREFIX)
            WHEN USR.ACCESS_LEVEL = 3 THEN CONCAT('ADMINISTRADOR ',USR.SICAVP_USER)
            ELSE CONCAT('SUPERVISOR DOCENTE ',USR.SICAVP_USER)
            END USR_NAME
        FROM users USR
        LEFT OUTER JOIN employed EMP ON EMP.ID_NOM = USR.SICAVP_USER
        LEFT OUTER JOIN academic_schedules ASH ON ASH.PERSON_CODE_ID = USR.SICAVP_USER
        WHERE USR.SICAVP_USER = '$id';";
    $resultName = $mysqli -> query($sqlName);
    if ($resultName -> num_rows > 0) {
        while ($rowName = $resultName -> fetch_assoc()) {
            $name = $rowName['USR_NAME'];
        }
    }
    
    if ($id) {
        // Marcar al usuario como confirmado en la base de datos
        $updateFlag = "UPDATE users SET FLAG_CONFIRM = 1 WHERE SICAVP_USER = '$id'";
        if ($mysqli -> query($updateFlag)) {
            $tittle = "¡Correo confirmado con éxito!";
            $parragraph = "¡Bienvenido $name! Ahora puedes acceder a las funcionalidades de SICAVP.";
            $image = "../../../static/img/comprobado.png";
        } else {
            echo $updateFlag;
        }
    } else {
        $tittle = "Token inválido o expirado.";
        $parragraph = "El enlace que intentaste usar no es válido o ha caducado. Por favor, solicita un nuevo enlace.";
        $image = "../../../static/img/advertencia.png";
    }
} else {
    $tittle = "No se proporcionó un token.";
    $parragraph = "No encontramos la información necesaria para confirmar tu cuenta. <br> Asegúrate de haber utilizado el enlace correcto o vuelve a intentarlo desde el correo que te enviamos.";
    $image = "../../../static/img/cancelar.png";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../../static/img/fav_sicavp.png" type="image/x-icon">
    <title>Cambia Contaseña</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/upd_pass.css">
    <style>
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body class="bg-white">
    <div class="my-5">
    <div class="p-5 text-center bg-white">
        <div class="container py-5">
            <img class="my-4" src="<?php echo $image ?>" alt="" srcset="" width="100px">
        <h1 class="text-body-emphasis"><?php echo $tittle ?></h1>
        <p class="col-lg-8 mx-auto lead">
            <?php echo $parragraph; ?>
        </p>
        <img class="my-4" src="../../../static/img/1.png" alt="" srcset="" width="300px">
        </div>
    </div>
    </div>
</body>
</html>