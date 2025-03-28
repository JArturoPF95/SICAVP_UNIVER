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

    if (isset($_POST['send'])) {
        $send = $_POST['send'];
    } else {
        $send = 0;
    }

    if (isset($_POST['idNom'])) {
        $Upid_nom = $_POST['idNom'];
    } else {
        $Upid_nom = '';
    }

    if (isset($_POST['nameUser'])) { 
        $Upname_user = $_POST['nameUser'];
    } else {
        $Upname_user = '';
    }

    if (isset($_POST['bioUser'])) {
        $UpidBio = $_POST['bioUser'];
    } else {
        $UpidBio = '';
    }

    if (isset($_POST['flagBio'])) {
        $flagBio = $_POST['flagBio'];
    } else {
        $flagBio = '';
    }

    //echo $Upid_nom . ' ' . $Upname_user . ' ' . $UpidBio . ' ' . $send . ' ' . $flagBio;

    if ($flagBio == 0) {
        $insertBio = "INSERT INTO mapping_bioadminemploy VALUES ('$UpidBio','$Upname_user','$Upid_nom')";
        if ($mysqli->query($insertBio) === true) {
            $message = 'Usuario actualizado con éxito';
            $icon = 'success';
        } else {
            $message = 'Error actualizando usuario';
            $icon = 'error';
        }
    } else {
        $updateBio = "UPDATE mapping_bioadminemploy SET ID_BIOUNIVER = '$UpidBio' WHERE ID_NOM2001 = '$Upid_nom'";
        if ($mysqli->query($updateBio) === true) {
            $message = 'Usuario actualizado con éxito';
            $icon = 'success';
        } else {
            $message = 'Error actualizando usuario';
            $icon = 'error';
        }
    }


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Biotime</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>
<body>
        <script type="text/javascript">
            swal({
                title: "Actualización de ID Biotime",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon; ?>",
                button: "Volver",
            }).then(function() {
                window.location = "updateBio.php?u=<?php echo $Upid_nom ?>";
            });
        </script>

</body>

</html>