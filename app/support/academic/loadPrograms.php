<?php
require '../../logic/conn.php';
require '../../../lib/vendor/autoload.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location:../../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

$message = '';
$icon = '';

$sqlGetPrograms = "SELECT DISTINCT PROGRAM, PROGRAM_DESC FROM academic_schedules";
$resultGetPrograms = $mysqli -> query($sqlGetPrograms);
if ($resultGetPrograms -> num_rows > 0) {
    while ($rowPrograms = $resultGetPrograms -> fetch_assoc()) {
        $code = $rowPrograms['PROGRAM'];
        $desc = $rowPrograms['PROGRAM_DESC'];

        $sqlInsert = "INSERT IGNORE INTO academic_tolerance (CODE_PROGRAM, DESCRIPTION, MIN_TIME, DELAY_CLASS, MAX_CLASS, MIN_END, MODIFIED_BY, MODIFIED_DATE) 
                        VALUES ('$code','$desc',5,12,30,5,'$user_active',NOW())"; /** Las tolerancias por default, se pueden parametrizar en el apartado Tolerancias */
        if ($mysqli -> query($sqlInsert)) {
            /** Genera las tolerancias por default, mismas que se pueden customizar en el menú tolerancias */
            $updateParIni = "UPDATE academic_schedules SET MAX_BEFORE_CLASS = DATE_ADD(START_CLASS, INTERVAL -5 MINUTE),
                                                           DELAY_CLASS = DATE_ADD(START_CLASS, INTERVAL 12 MINUTE),
                                                           MAX_DELAY_CLASS = DATE_ADD(START_CLASS, INTERVAL 30 MINUTE),
                                                           MIN_END_CLASS = DATE_ADD(END_CLASS, INTERVAL -5 MINUTE);";
            if ($mysqli -> query($updateParIni)) {
                $message = "Programas actualizados con éxito";
                $icon = 'success';
            } else {
                $message = "Error actualizando programas";
                $icon = 'error';
            }
        } else {
            $message = "Error actualizando programas";
            $icon = 'error';
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
    <title>Envío de Nivel - Programa Docentes</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Carga Programas Docentes",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "../academic_users.php?id=<?php echo $user_active ?>&sn=2";
        });
    </script>

</body>

</html>