<?php
session_start(); // ¡IMPORTANTE! Sin esto, $_SESSION no funcionará

require '../conn.php';
require_once 'access_denied.php';

// Obtener IP
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// $ip = file_get_contents('https://api.ipify.org'); // Otra opción para IP pública
$device = $_SERVER['HTTP_USER_AGENT'];

if (isset($_POST['so'])) {
    $_SESSION['sistema_operativo'] = $_POST['so'];
} else {
    $_SESSION['sistema_operativo'] = 'Desconocido';
}

$so = $_SESSION['sistema_operativo'];

//echo $ip . '<br>' . $device. '<br>' . $so;

$flagSesion = 0; // Bandera para validar el acceso desde dispositivos móviles o computadoras de escritorio

if (!empty($_POST)) {
    $usuario = mysqli_real_escape_string($mysqli, $_POST['usuario']);
    $password = mysqli_real_escape_string($mysqli, $_POST['password']);
    $error = '';

    //Obtenemos la información del usuario
    $sql_user = "SELECT
            DISTINCT
            USR.*, 
            IF(USR.PAYROLL = 1 AND USR.ACCESS_LEVEL != '3', 
                CONCAT(SUBSTRING(EMP.GOVERNMENT_ID,9,2),SUBSTRING(EMP.GOVERNMENT_ID,7,2),SUBSTRING(EMP.GOVERNMENT_ID,5,2)),
                IF(USR.PAYROLL = 3 AND USR.ACCESS_LEVEL = 4, ASH.PERSON_CODE_ID, USR.PASS_TEMP)) TEMP,
            CASE
                WHEN USR.ACCESS_LEVEL IN ('1','2') THEN CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX)
                WHEN USR.ACCESS_LEVEL = '4' THEN ASH.NAME
                WHEN USR.ACCESS_LEVEL = '3' THEN CONCAT('ADMINISTRADOR ',USR.SICAVP_USER)
                WHEN USR.ACCESS_LEVEL = '5' THEN CONCAT('SUPERVISOR DOCENTE ',USR.SICAVP_USER)
            END USER_NAME
        FROM users USR
        LEFT OUTER JOIN employed EMP ON EMP.ID_NOM = USR.SICAVP_USER
        LEFT OUTER JOIN academic_schedules ASH ON ASH.PERSON_CODE_ID = USR.SICAVP_USER
        WHERE USR.SICAVP_USER = '$usuario'";

    $result_user = $mysqli->query($sql_user);
    $rows = $result_user->num_rows;

    if ($rows == 1) {

        // Comparas nivel de acceso si es 4
        // Buscar que tenga clases de practicas clinicas
        // Bandera acceso por campos clinicos
        // caso contrario bandera por IP
        // si es por IP 



        $row = $result_user->fetch_assoc();
        if ($row['ACCESS_LEVEL'] == '1' or $row['ACCESS_LEVEL'] == '2' or $row['ACCESS_LEVEL'] == '4') {
            $temp = $row['TEMP'];
        } else {
            $temp = 'ABCD1234';
        }
        $pass_temp = $row['PASS_TEMP'];
        $pass = $row['PASSWORD'];

        //Validamos la IP desde donde ingresa (Si se solicita no permitiría acceso desde una ubicación no registrada en catálogo)
        /*$sqlValIp = "SELECT * FROM code_ip WHERE IP = '$ip'";
        $resultIP = $mysqli -> query($sqlValIp);
        if ($resultIP -> num_rows > 0) {*/

        if ($row['SEPARATION_FLAG'] != 1) { //Validamos una posible baja            

            if ($password === $pass_temp && $pass === '') {  //Valida si tiene la contraseña temporal

                $user = $row['SICAVP_USER'];
                echo '<script type="text/javascript">window.location.href="update_password.php?u_12345=' . $user . '"</script>';

                //echo $user.' '.$password.' - '.$pass.' - '.$pass_temp;

            } elseif (password_verify($password, $pass)) {   //Validamos la contraseña si ya la modificó

                if ($row['FLAG_CONFIRM'] == 1) {

                    /** Si el nivel de acceso es docente permitimos acceso */
                    if ($row['ACCESS_LEVEL'] == 4) {

                        $user = $row['SICAVP_USER'];

                        /** Validamos las materias, que tenga Campos Clínicos */
                        $sqlValClinic = "SELECT DISTINCT ASH.ACADEMIC_YEAR, ASH.ACADEMIC_TERM, ASH.ACADEMIC_SESSION, ASH.START_DATE, ASH.END_DATE, ASH.PERSON_CODE_ID, ASH.NAME, ASH.EVENT_ID, ASH.PUBLICATION_NAME_1, DATE(NOW())  FROM academic_schedules ASH
    WHERE DATE(NOW()) BETWEEN ASH.START_DATE AND ASH.END_DATE AND ASH.FLAG_CLINIC = '1'
    AND PERSON_CODE_ID = '$user'";

                        $resultValClinic = $mysqli->query($sqlValClinic);
                        /** Docente con materias de campo clínico */
                        if ($resultValClinic->num_rows >= 1) {

                            session_start();

                            $_SESSION['usuario'] = $row['SICAVP_USER'];
                            $_SESSION['access_lev'] = $row['ACCESS_LEVEL'];
                            $_SESSION['user_name'] = $row['USER_NAME'];
                            $_SESSION['payroll'] = $row['PAYROLL'];
                            $_SESSION['session'] = $row['NOM_SESSION'];
                            $_SESSION['city'] = $row['CITY'];

                            $user_active = $row['SICAVP_USER'];

                            $sqlUpdate = "UPDATE users_last_access SET LAST_ACCESS = NOW(), UBICATION = '$ip' WHERE USER = '$user_active'";
                            if ($mysqli->query($sqlUpdate) === true) {
                                header("location: ../../index.php");
                            } else {
                                $error = 5;
                                header("Location:../../../index.php?error=$error");
                                exit();
                            }

                            /** Docente sin materias campo clínico */
                        } else {
                            /** Validar el sistema operativo */
                            if ($so == 'Windows' || $so == 'Mac OS X' || $so == 'MacOS') {
                                /** Validamos acceso por IP */
                                $valMyIP = "SELECT * FROM code_ip WHERE IP = '$ip'";
                                $resultValIP = $mysqli->query($valMyIP);
                                if ($resultValIP->num_rows > 0) {
                                    while ($rowValIP = $resultValIP->fetch_assoc()) {
                                        session_start();
                                        $_SESSION['usuario'] = $row['SICAVP_USER'];
                                        $_SESSION['access_lev'] = $row['ACCESS_LEVEL'];
                                        $_SESSION['user_name'] = $row['USER_NAME'];
                                        $_SESSION['payroll'] = $row['PAYROLL'];
                                        $_SESSION['session'] = $row['NOM_SESSION'];
                                        $_SESSION['city'] = $row['CITY'];

                                        $user_active = $row['SICAVP_USER'];

                                        $sqlUpdate = "UPDATE users_last_access SET LAST_ACCESS = NOW(), UBICATION = '$ip' WHERE USER = '$user_active'";
                                        if ($mysqli->query($sqlUpdate) === true) {
                                            header("location: ../../index.php");
                                        } else {
                                            $error = 5;
                                            header("Location:../../../index.php?error=$error");
                                            exit();
                                        }
                                    }
                                } else {
                                    echo $messageIP;
                                }
                            } else {
                                echo $messageDevice;
                            }
                        }

                        /** Sino Validamos acceso por IP y dispositivo móvil */
                    } else {
                        /** Validar el sistema operativo */
                        if ($so == 'Windows' || $so == 'Mac OS X' || $so == 'MacOS') {
                            /** Validamos acceso por IP */
                            $valMyIP = "SELECT * FROM code_ip WHERE IP = '$ip'";
                            $resultValIP = $mysqli->query($valMyIP);
                            if ($resultValIP->num_rows > 0) {
                                while ($rowValIP = $resultValIP->fetch_assoc()) {
                                    session_start();
                                    $_SESSION['usuario'] = $row['SICAVP_USER'];
                                    $_SESSION['access_lev'] = $row['ACCESS_LEVEL'];
                                    $_SESSION['user_name'] = $row['USER_NAME'];
                                    $_SESSION['payroll'] = $row['PAYROLL'];
                                    $_SESSION['session'] = $row['NOM_SESSION'];
                                    $_SESSION['city'] = $row['CITY'];

                                    $user_active = $row['SICAVP_USER'];

                                    $sqlUpdate = "UPDATE users_last_access SET LAST_ACCESS = NOW(), UBICATION = '$ip' WHERE USER = '$user_active'";
                                    if ($mysqli->query($sqlUpdate) === true) {
                                        header("location: ../../index.php");
                                    } else {
                                        $error = 5;
                                        header("Location:../../../index.php?error=$error");
                                        exit();
                                    }
                                }
                            } else {
                                echo $messageIP;
                            }
                        } else {
                            echo $messageDevice;
                        }
                    }
                } else {
                    $error = 4;
                    header("Location:../../../index.php?error=$error");
                    exit();
                }
            } else {

                $error = 1;
                header("Location:../../../index.php?error=$error");
                exit();
            } //cierra if de validación de accesos

        } else {
            $error = 3;
            header("Location:../../../index.php?error=$error");
            exit();
        }

        /*} else {
            $error = 4;
            header("Location:../../../index.php?error=$error");
            exit();
        } //Termina if de ubicación*/
    } else {

        $error = 2;
        header("Location:../../../index.php?error=$error");
        exit();
    } //Cierra if de query

} else {
    header("Location:../../../");
    exit();
} //Cierra el if del empty
