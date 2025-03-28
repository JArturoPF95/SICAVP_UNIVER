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

//Fijas
$institution = '003';
$clasif = 'NNN';
$country = '001';
$status = 'A';

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

//Supervisor
if (isset($_POST['supervisorID'])) { $supervisorId = strtoupper($_POST['supervisorID']); } else { $supervisorId = NULL; }
if (isset($_POST['supervisorIdAux'])) { $supervisorIdAux = strtoupper($_POST['supervisorIdAux']); } else { $supervisorIdAux = NULL; }

$getSup = "SELECT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) SUP_N, POSITION FROM employed WHERE ID_NOM = '$supervisorId'";
$resultSup = $mysqli -> query($getSup);
if ($resultSup -> num_rows > 0) {
    while ($rowSup = $resultSup -> fetch_assoc()) {
        $supervisorName = $rowSup['SUP_N'];
        $supervisorPosition = $rowSup['POSITION'];
    }
}

/* echo 'idNom: ' . $idNom . '<br>' .
    'name: ' . $name . '<br>' .
    'lastName: ' . $lastname . '<br>' .
    'lastNamePrefix: ' . $lastNamePrefix . '<br>' .
    'genre: ' . $genre . '<br>' .
    'governmentID: ' . $governmentID . '<br>' .
    'taxpayerID: ' . $rfc . '<br>' .
    'imss: ' . $imss . '<br>' .
    'city: ' . $city . '<br>' .
    'nomSesion: ' . $nomSesion . '<br>' .
    'area: ' . $area . '<br>' .
    'department: ' . $department . '<br>' .
    'job: ' . $job . '<br>' .
    'position: ' . $position . '<br>' .
    'scheduleGroup: ' . $scheduleGroup . '<br>' .
    'daytrip: ' . $daytrip . '<br>' .
    'contract: ' . $contract . '<br>' .
    'payroll: ' . $payroll . '<br>' .
    'access: ' . $access . '<br>' .
    'admissionDate: ' . $admissionDate . '<br>' .
    'permanentEmpDate: ' . $permanentEmpDate . '<br>' .
    'antiquity: ' . $antiquity . '<br>' .
    'contractStart: ' . $contractStart . '<br>' .
    'supervisorId: ' . $supervisorId . '<br>' .
    'supervisorName: ' . $supervisorName . '<br>' .
    'supervisorPosition: ' . $supervisorPosition . '<br>' .
    'supervisorIdAux: ' . $supervisorIdAux . '<br>' .
    'supervisorNameAux: ' . $supervisorNameAux . '<br>' .
    'supervisorPositionAux: ' . $supervisorPositionAux . '<br>'; */

$insertEmployed = "INSERT INTO employed (INSTITUTION, ID_NOM, PAYROLL, LAST_NAME, LAST_NAME_PREFIX, NAME, TAXPAYER_ID
                                        , GOVERNMENT_ID, IMSS, STATUS, AREA, DEPARTMENT, COUNTRY, CITY, NOM_SESSION
                                        , JOB, POSITION, SCHEDULE_GROUP, DAYTRIP, ADMISSION_DATE, PERMANENT_EMP_DATE
                                        , ANTIQUITY, CLASIF, SEPARATION_DATE, SEPARATION_COMMENTS, CONTRACT, CONTRACT_START
                                        , CONTRACT_END, GENRE, POSITION_SUEPRVISOR, SUPERVISOR_ID, SUPERVISOR_NAME
                                        , SUPERVISOR_ID_AUX, MODIFIED_BY, MODIFIED_DATE) VALUES 
                                        ('$institution','$idNom','$payroll','$lastname','$lastNamePrefix','$name','$rfc'
                                        ,'$governmentID','$imss','$status','$area','$department','$country','$city','$nomSesion'
                                        ,'$job','$position','$scheduleGroup','$daytrip','$admissionDate','$permanentEmpDate'
                                        ,'$antiquity','$clasif',NULL,'','$contract','$contractStart'
                                        ,NULL,'$genre','$supervisorPosition',$supervisorId,'$supervisorName'
                                        ,$supervisorIdAux,'$user_active',NOW())";
//echo $insertEmployed;
if ($mysqli -> query($insertEmployed)) {
    $messageFlag = '1';
    $iconFlag = '1';
    $day = substr($governmentID, 8, 2);
    $month = substr($governmentID, 6, 2);
    $year = substr($governmentID, 4, 2);
    $passTemp = $day.$month.$year;
    $createUser = "INSERT INTO users (SICAVP_USER, PASS_TEMP, PASSWORD, NOM_SESSION, CITY
                                    , PAYROLL, ACCESS_LEVEL, SEPARATION_FLAG, EMAIL, FLAG_CONFIRM
                                    , CREATED_BY, CREATED_DATE, MODIFIED_BY, MODIFIED_DATE) VALUES 
                                    ('$idNom','$passTemp','','$nomSesion','$city'
                                    ,'$payroll','$access','0',NULL,'0'
                                    ,'$user_active',NOW(),NULL,NULL);";

    //echo $createUser;
    if ($mysqli -> query($createUser)) {        
        $messageFlag = '2';
    } else {
        $messageFlag = '1';
    }
} else {
    $messageFlag = '0';
}

switch ($messageFlag) {
    case '0':
        $message = 'Error creando empleado';
        $icon = 'error';
        $url = '../newEmp.php?id='.$user_active;
        break;
    case '1':
        $message = 'Error creando usuario. Validar que el empleado no exista en la base de datos';
        $icon = 'warning';
        $url = '../admin_users.php?id='.$user_active;
        break;
    case '2':
        $message = 'Empleado y usuario creados correctamente';
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
            title: "Nuevo Empleado",
            text: "<?php echo $message; ?>",
            icon: "<?php echo $icon ?>",
            button: "Volver",
        }).then(function() {
            window.location = "<?php echo $url ?>";
        });
    </script>

</body>

</html>