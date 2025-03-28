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

$messageFlag = '0';

$url = '';
$message = '';
$icon = '';

// Personal
if(isset($_POST['idNom'])) { $idNom = $_POST['idNom']; } else { $idNom = ''; }
if(isset($_POST['name'])) { $name = strtoupper($_POST['name']); } else { $name = ''; }
if(isset($_POST['lastName'])) { $lastname = strtoupper($_POST['lastName']); } else { $lastname = ''; }
if(isset($_POST['lastNamePrefix'])) { $lastNamePrefix = strtoupper($_POST['lastNamePrefix']); } else { $lastNamePrefix = ''; }
if(isset($_POST['genre'])) { $genre = strtoupper($_POST['genre']); } else { $genre = '';}
if(isset($_POST['governmentID'])) { $governmentID = strtoupper($_POST['governmentID']); } else { $governmentID = ''; }
if(isset($_POST['taxpayerID'])) { $rfc = strtoupper($_POST['taxpayerID']); } else { $rfc = ''; }
if(isset($_POST['imss'])) { $imss = strtoupper($_POST['imss']); } else { $imss = ''; }

//Ubicación
if(isset($_POST['city'])) { $city = $_POST['city']; } else { $city = ''; }
if(isset($_POST['nomSesion'])) { $nomSesion = $_POST['nomSesion']; } else { $nomSesion = ''; }

//Laboral
if(isset($_POST['area'])) { $area = $_POST['area']; } else { $area = ''; }
if(isset($_POST['department'])) { $department = $_POST['department']; } else { $department = ''; }
if(isset($_POST['job'])) { $job = $_POST['job']; } else { $job = ''; }
if(isset($_POST['position'])) { $position = $_POST['position']; } else { $position = ''; }
if(isset($_POST['scheduleGroup'])) { $scheduleGroup = $_POST['scheduleGroup']; } else { $scheduleGroup = ''; }
if(isset($_POST['daytrip'])) { $daytrip = $_POST['daytrip']; } else { $daytrip = ''; }
if(isset($_POST['contract'])) { $contract = $_POST['contract']; } else { $contract = ''; }
if(isset($_POST['payroll'])) { $payroll = $_POST['payroll']; } else { $payroll = ''; }
if(isset($_POST['access'])) { $access = $_POST['access']; } else { $access = ''; }

//fechas
if (isset($_POST['admissionDate'])) { $admissionDate = $_POST['admissionDate']; } else { $admissionDate = ''; }
if (isset($_POST['permanentEmpDate'])) { $permanentEmpDate = $_POST['permanentEmpDate']; } else { $permanentEmpDate = ''; }
if (isset($_POST['antiquity'])) { $antiquity = $_POST['antiquity']; } else { $antiquity = ''; }
if (isset($_POST['contractStart'])) { $contractStart = $_POST['contractStart']; } else { $contractStart = ''; }

// Supervisor
/*if (isset($_POST['supervisorID'])) { 
    $supervisorId = strtoupper($_POST['supervisorID']); 
} else { 
    $supervisorId = 'NULL'; // Cambiar a 'NULL' para la consulta SQL
}*/

if (isset($_POST['supervisorID']) && $_POST['supervisorID'] !== '') { 
    if ($_POST['supervisorID'] == 'NULL') { 
        $supervisorId = 'NULL'; // Cambiar a 'NULL' para la consulta SQL
    } else { 
        $supervisorId = "'" . strtoupper($_POST['supervisorID']) . "'"; // Envolver en comillas simples
    } 
} else { 
    $supervisorId = 'NULL'; // Cambiar a 'NULL' para la consulta SQL
}

if (isset($_POST['supervisorIdAux']) && $_POST['supervisorIdAux'] !== '') { 
    if ($_POST['supervisorIdAux'] == 'NULL') { 
        $supervisorIdAux = 'NULL'; // Cambiar a 'NULL' para la consulta SQL
    } else { 
        $supervisorIdAux = "'" . strtoupper($_POST['supervisorIdAux']) . "'"; // Envolver en comillas simples
    } 
} else { 
    $supervisorIdAux = 'NULL'; // Cambiar a 'NULL' para la consulta SQL
}

$getSup = "SELECT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) SUP_N, POSITION FROM employed WHERE ID_NOM = $supervisorId";
//echo $getSup;
$resultSup = $mysqli -> query($getSup);
if ($resultSup -> num_rows > 0) {
    while ($rowSup = $resultSup -> fetch_assoc()) {
        $supervisorName = $rowSup['SUP_N'];
        $supervisorPosition = $rowSup['POSITION'];
    }
}

/*$updateEmployed = "UPDATE employed SET PAYROLL='$payroll',LAST_NAME='$lastname',LAST_NAME_PREFIX='$lastNamePrefix',NAME='$name'
                                    ,TAXPAYER_ID='$rfc',GOVERNMENT_ID='$governmentID',IMSS='$imss',AREA='$area',DEPARTMENT='$department'
                                    ,CITY='$city',NOM_SESSION='$nomSesion',JOB='$job',POSITION='$position',SCHEDULE_GROUP='$scheduleGroup'
                                    ,DAYTRIP='$daytrip',ADMISSION_DATE='$admissionDate',PERMANENT_EMP_DATE='$permanentEmpDate'
                                    ,ANTIQUITY='$antiquity',CONTRACT='$contract',CONTRACT_START='$contractStart',GENRE='$genre'
                                    ,POSITION_SUEPRVISOR='$supervisorPosition',SUPERVISOR_ID=$supervisorId,SUPERVISOR_NAME='$supervisorName'
                                    ,SUPERVISOR_ID_AUX=$supervisorIdAux,MODIFIED_BY='$user_active',MODIFIED_DATE=NOW()
                                    WHERE ID_NOM = '$idNom';";*/

// Construcción de la consulta SQL
$updateEmployed = "UPDATE employed SET PAYROLL='$payroll',LAST_NAME='$lastname',LAST_NAME_PREFIX='$lastNamePrefix',NAME='$name'
                                    ,TAXPAYER_ID='$rfc',GOVERNMENT_ID='$governmentID',IMSS='$imss',AREA='$area',DEPARTMENT='$department'
                                    ,CITY='$city',NOM_SESSION='$nomSesion',JOB='$job',POSITION='$position',SCHEDULE_GROUP='$scheduleGroup'
                                    ,DAYTRIP='$daytrip',ADMISSION_DATE='$admissionDate',PERMANENT_EMP_DATE='$permanentEmpDate'
                                    ,ANTIQUITY='$antiquity',CONTRACT='$contract',CONTRACT_START='$contractStart',GENRE='$genre'
                                    ,POSITION_SUEPRVISOR='$supervisorPosition',SUPERVISOR_ID=$supervisorId,SUPERVISOR_NAME='$supervisorName'
                                    ,SUPERVISOR_ID_AUX=$supervisorIdAux,MODIFIED_BY='$user_active',MODIFIED_DATE=NOW()
                                    WHERE ID_NOM = '$idNom';";

//echo $updateEmployed . '<br>';
if ($mysqli -> query($updateEmployed)) {
    $messageFlag = '';
    $iconFlag = '';
    $day = substr($governmentID, 8, 2);
    $month = substr($governmentID, 6, 2);
    $year = substr($governmentID, 4, 2);
    $passTemp = $day.$month.$year;
    $updateUser = "UPDATE users SET NOM_SESSION='$nomSesion',CITY='$city',PAYROLL='$payroll'
                                    ,ACCESS_LEVEL='$access',MODIFIED_BY='$user_active'
                                    ,MODIFIED_DATE=NOW() WHERE SICAVP_USER = '$idNom';";

    //echo $updateUser;
    if ($mysqli -> query($updateUser)) {        
        $messageFlag = '2';
    } else {
        $messageFlag = '1';
    }
} else {
    $messageFlag = '0';
}

switch ($messageFlag) {
    case '0':
        $message = 'Error actualizando empleado';
        $icon = 'error';
        $url = 'user_update.php?id='.$user_active;
        break;
    case '1':
        $message = 'Datos de acceso sin actualización';
        $icon = 'warning';
        $url = '../admin_users.php?id='.$user_active;
        break;
    case '2':
        $message = 'Empleado y usuario actualizados correctamente';
        $icon = 'success';
        $url = '../admin_users.php?id='.$user_active;
        break;
    default:
        # code...
        break;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Empleado</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>

    <script type="text/javascript">
        swal({
            title: "Actualizar Empleado",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "<?php echo $url ?>";
        });
    </script>

</body>

</html>