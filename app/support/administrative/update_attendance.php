<?php 
ini_set('max_execution_time', -1); // 10 minutos para ejecutar el script

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

if(isset($_GET['f'])){


    $registros_lote = $_GET['r'];
    // Contadores
    $incidencias_NO_identificdas = 0;
    $entradas_ok = 0;
    $errores_entradas = 0;
    $act_entradas_ok = 0;
    $error_act_entradas = 0;
    $reg_bio_NO_ent_sal = 0;
    $salidas_ok = 0;
    $errores_salidas = 0;
    $act_salidas_ok = 0;
    $error_act_salidas = 0;

    $lote = $_GET['f'];
    //echo $lote;

    $sql_day_emp = "SELECT DISTINCT ID_NOM, RECORD_DATE, COUNT(PK) REG_DAY  FROM `biometricTimeClock` WHERE CREATED_DATE = '$lote' GROUP BY ID_NOM, RECORD_DATE ORDER BY ID_NOM, RECORD_DATE;";
    $res_day_emp = $mysqli->query($sql_day_emp);
    $val_day_emp = $res_day_emp->num_rows;

    if($val_day_emp > 0){
        while($row_day_emp = mysqli_fetch_assoc($res_day_emp)){

            $empleado = $row_day_emp['ID_NOM'];
            $fecha = $row_day_emp['RECORD_DATE'];
            $reg_day = $row_day_emp['REG_DAY'];

            if($reg_day == 1){
                $sql_en_sal = "SELECT ID_BIOUNIVER, ID_NOM2001, B.RECORD_DATE, B.RECORD_TIME, B.STATUS, B.CREATED_DATE, C.CODE_DAY FROM biometricTimeClock B
                            INNER JOIN mapping_bioadminemploy M ON B.ID_NOM = M.ID_BIOUNIVER
                            INNER JOIN calendar C ON B.RECORD_DATE = C.CALENDAR_DATE AND C.DAY_OF_REST = 0
                            WHERE B.ID_NOM = '$empleado' AND B.RECORD_DATE = '$fecha' AND B.CREATED_DATE = '$lote'
                            AND (SELECT COUNT(requestId) CONT_VAC FROM vacation_request V WHERE '$fecha' >= START_DATE  and '$fecha' <= END_DATE AND AUTHORIZATION_FLAG = 1 AND V.ID_NOM = M.ID_NOM2001) = 0
                            ORDER BY RECORD_TIME ASC LIMIT 1";

            }elseif($reg_day > 1){
                $sql_en_sal = "(SELECT ID_BIOUNIVER, ID_NOM2001, B.RECORD_DATE, B.RECORD_TIME, B.STATUS, B.CREATED_DATE, C.CODE_DAY FROM biometricTimeClock B
                            INNER JOIN mapping_bioadminemploy M ON B.ID_NOM = M.ID_BIOUNIVER
                            INNER JOIN calendar C ON B.RECORD_DATE = C.CALENDAR_DATE AND C.DAY_OF_REST = 0
                            WHERE B.ID_NOM = '$empleado' AND B.RECORD_DATE = '$fecha' AND B.CREATED_DATE = '$lote'
                            AND (SELECT COUNT(requestId) CONT_VAC FROM vacation_request V WHERE '$fecha' >= START_DATE  and '$fecha' <= END_DATE AND AUTHORIZATION_FLAG = 1 AND V.ID_NOM = M.ID_NOM2001) = 0
                            ORDER BY RECORD_TIME ASC LIMIT 1) 
                            UNION ALL 
                            (SELECT M.ID_BIOUNIVER, M.ID_NOM2001, B.RECORD_DATE, B.RECORD_TIME, B.STATUS, B.CREATED_DATE, C.CODE_DAY FROM biometricTimeClock B
                            INNER JOIN mapping_bioadminemploy M ON B.ID_NOM = M.ID_BIOUNIVER
                            INNER JOIN calendar C ON B.RECORD_DATE = C.CALENDAR_DATE AND C.DAY_OF_REST = 0
                            WHERE B.ID_NOM = '$empleado' AND B.RECORD_DATE = '$fecha' AND B.CREATED_DATE = '$lote'
                            AND (SELECT COUNT(requestId) CONT_VAC FROM vacation_request V WHERE '$fecha' >= START_DATE  and '$fecha' <= END_DATE AND AUTHORIZATION_FLAG = 1 AND V.ID_NOM = M.ID_NOM2001) = 0
                            ORDER BY RECORD_TIME DESC LIMIT 1);";
            } 
                        
            $res_en_sal = $mysqli->query($sql_en_sal);
            $val_en_sal = $res_en_sal->num_rows;

            if($val_en_sal > 0){

                $cont_reg_day = 0;

                while($row_en_sal = mysqli_fetch_assoc($res_en_sal)){

                    $idNom2001 = $row_en_sal['ID_NOM2001'];
                    $codeDay = $row_en_sal['CODE_DAY'];

                    if($cont_reg_day == 0){

                        $entrada = $row_en_sal['RECORD_TIME'];

                        $sql_get_incidence_bio = "SELECT DISTINCT EMP.ID_NOM AS ID_NOM2001
                        , COALESCE(ASH.MIN_TIME_START, AS2.MIN_TIME_START) MIN_TIME_START
                        , COALESCE(ASH.TIME_START, AS2.TIME_START) TIME_START
                        , COALESCE(ASH.AUSENCE_TIME, AS2.AUSENCE_TIME) AUSENCE_TIME
                        , COALESCE(ASH.MIN_TIME_OUT, AS2.MIN_TIME_OUT) MIN_TIME_OUT
                        , COALESCE(ASH.LOCK_IN_TIME, AS2.LOCK_IN_TIME) LOCK_IN_TIME
                        , COALESCE(ASH.OUT_TIME, AS2.OUT_TIME) OUT_TIME
                        , COALESCE(ASH.DELAY_TIME_START, AS2.DELAY_TIME_START) DELAY_TIME_START
                        FROM employed EMP 
                        LEFT OUTER JOIN assigned_schedule AAS ON EMP.ID_NOM = AAS.ID_NOM AND DATE($fecha) BETWEEN AAS.START_DATE AND AAS.END_DATE
                        LEFT OUTER JOIN admin_schedules ASH ON ASH.CODE_SCHEDULE = AAS.SCHEDULE
                        LEFT OUTER JOIN admin_schedules AS2 ON AS2.CODE_SCHEDULE = EMP.SCHEDULE_GROUP
                        WHERE EMP.ID_NOM = '$idNom2001' AND COALESCE(ASH.CODE_DAY, AS2.CODE_DAY) = '$codeDay'";

                        $res_incidence = $mysqli->query($sql_get_incidence_bio);

                        $val_res_incidence = $res_incidence->num_rows;

                        if($val_res_incidence == 1){

                            $row_incidence = mysqli_fetch_assoc($res_incidence);
                            
                            
                            $incidence_time_start = $row_incidence['TIME_START'];
                            $incidence_delay_time = $row_incidence['DELAY_TIME_START'];
                            $incidence_ausence_time = $row_incidence['AUSENCE_TIME'];
                            $incidence_time_out = $row_incidence['OUT_TIME'];
                            $incidence_before_out = $row_incidence['MIN_TIME_OUT'];

                            $sql_countDelays_BIO = "SELECT AAT.ATTENDANCE_DATE,
                                (SELECT DISTINCT ATE.TINC FROM admin_attendance ATE WHERE ATE.NOM_ID = AAT.NOM_ID AND ATE.ATTENDANCE_DATE = AAT.ATTENDANCE_DATE ORDER BY ATE.ATTENDANCE_TIME ASC LIMIT 1) TINC
                            FROM admin_attendance AAT
                            INNER JOIN payroll_period PRP ON AAT.ATTENDANCE_DATE BETWEEN PRP.START_DATE AND PRP.END_DATE
                            INNER JOIN code_incidence INCI ON INCI.CODE_TINC = AAT.TINC
                            WHERE '$fecha' BETWEEN PRP.START_DATE AND PRP.END_DATE AND INCI.DESCRIP_TINC IN ('FALTA POR RETARDOS','RETARDO') AND AAT.IN_OUT = 1 AND NOM_ID = '$idNom2001'
                            ORDER BY AAT.ATTENDANCE_DATE";

                            //$res_sql_countDelays_BIO = $mysqli->query($sql_countDelays_BIO);
                            //$delays = $res_sql_countDelays_BIO->num_rows;

                            require_once 'check_attendance.php';

                            /*
                            echo $idNom2001," || ";
                            echo $incidence_time_start," || ";
                            echo $incidence_delay_time," || ";
                            echo $incidence_ausence_time," || ";
                            echo $incidence_time_out," || ";
                            echo $incidence_before_out," || ";
                            echo $delays," || ";
                            echo $entrada," || ";
                            echo $incidence," || ";
                            echo $fecha," <br> ";
                            */

                            $sql_val_attendance = "SELECT AttendanceId, NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT 
                                                    FROM admin_attendance 
                                                    where NOM_ID = $idNom2001 AND ATTENDANCE_DATE = '$fecha' AND CODE_DAY = $codeDay AND IN_OUT = 1;";

                            $res_val_attendance = $mysqli->query($sql_val_attendance);
                            $val_res_attendance = $res_val_attendance->num_rows;

                            if($val_res_attendance == 0){

                                $sql_insert_attendance = "INSERT INTO admin_attendance (NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG, JUSTIFY, COMMENTS) 
                                VALUES ('$idNom2001','$codeDay','$fecha','$entrada','$incidence','1', 'B', '', '');";

                                if($mysqli->query($sql_insert_attendance) === true){
                                    $entradas_ok ++;
                                }else{
                                    $errores_entradas ++;
                                }
                            
                            }elseif($val_res_attendance == 1){

                                $row_val_attendance = mysqli_fetch_assoc($res_val_attendance);

                                if($entrada < $row_val_attendance['ATTENDANCE_TIME']){

                                    $id_reg_attendance = $row_val_attendance['AttendanceId'];

                                    $sql_update_attendance = "UPDATE admin_attendance SET ATTENDANCE_TIME = '$entrada', TINC = '$incidence', BIO_SIC_FLAG = 'B'
                                            WHERE AttendanceId = '$id_reg_attendance' AND NOM_ID = $idNom2001 AND ATTENDANCE_DATE = '$fecha' AND CODE_DAY = '$codeDay' AND IN_OUT = 1;";

                                        if($mysqli->query($sql_update_attendance) === true){
                                            // echo $sql_update_attendance;
                                            $act_entradas_ok ++;
                                        }else{
                                            $error_act_entradas ++;
                                            // echo $sql_update_attendance;
                                            } // Cierre ejecución actualización de registro de entrada
                                
                            } // Cierre validacion actualización de registro de entrada


                            } // Cierre validación de existecia de registro de entrada en la misma fecha   
                        
                        }else{
                            $incidencias_NO_identificdas ++;
                        } // Cierre validación de existencia de incidencias

                    }elseif($cont_reg_day == 1 AND $reg_day > 1){
                        
                        $salida = $row_en_sal['RECORD_TIME'];

                        $sql_val_attendance = "SELECT AttendanceId, NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT 
                                                    FROM admin_attendance 
                                                    where NOM_ID = $idNom2001 AND ATTENDANCE_DATE = '$fecha' AND CODE_DAY = '$codeDay' AND IN_OUT = 2;";

                            $res_val_attendance = $mysqli->query($sql_val_attendance);
                            $val_res_attendance = $res_val_attendance->num_rows;

                            if($val_res_attendance == 0){

                                $sql_insert_attendance = "INSERT INTO admin_attendance (NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG, JUSTIFY, COMMENTS) 
                                VALUES ('$idNom2001','$codeDay','$fecha','$salida','00','2', 'B', '', '');";

                                if($mysqli->query($sql_insert_attendance) === true){
                                    $salidas_ok ++;
                                }else{
                                    $errores_salidas ++;
                                }
                            
                            }elseif($val_res_attendance == 1){

                                $row_val_attendance = mysqli_fetch_assoc($res_val_attendance);

                                if($salida < $row_val_attendance['ATTENDANCE_TIME']){

                                    $id_reg_attendance = $row_val_attendance['AttendanceId'];

                                    $sql_update_attendance = "UPDATE admin_attendance SET ATTENDANCE_TIME = '$salida', BIO_SIC_FLAG = 'B'
                                            WHERE AttendanceId = '$id_reg_attendance' AND NOM_ID = $idNom2001 AND ATTENDANCE_DATE = '$fecha' AND CODE_DAY = '$codeDay' AND IN_OUT = 2;";

                                        if($mysqli->query($sql_update_attendance) === true){
                                            // echo $sql_update_attendance;
                                            $act_salidas_ok ++;
                                        }else{
                                            $error_act_salidas ++;
                                            // echo $sql_update_attendance;
                                            } // Cierre ejecución actualización de registro de entrada
                                
                            } // Cierre validacion actualización de registro de entrada


                            } // Cierre validación de existecia de registro de entrada en la misma fecha   




                    } // Cierre proceso de salida

                    $cont_reg_day ++;

                }   // Cierra while update registros de entrada y salida por fecha y empleado

            }else{
                $reg_bio_NO_ent_sal ++;
            } // Cierra validación de registros de entrada y salida por fecha y empleado


        } // Cierra while de registros de empleados en lote de carga por fecha


    }   // Cierre validación de registros en lote de carga

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización Asistencia</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="card text-center mb-4">
                <div class="card-header">
                    <h3>Actualización de Asistencia desde registros de checador, lote "<?php echo $lote; ?>"</h3>
                </div>
                <div class="card-body">
                <table class="table table-sm table-striped table-bordered">
                        <thead>
                            <tr class="table-primary">
                                <th>Validación</th>
                                <th>Registros</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Registros cargados en lote</td>
                                <td><?php echo $registros_lote; ?></td>
                            </tr>
                            <tr>
                                <td>Entradas registradas</td>
                                <td><?php echo $entradas_ok; ?></td>
                            </tr>
                            <tr>
                                <td>Errores registro de entradas</td>
                                <td><?php echo $errores_entradas; ?></td>
                            </tr>
                            <tr>
                                <td>Entradas Actualizadas</td>
                                <td><?php echo $act_entradas_ok; ?></td>
                            </tr>
                            <tr>
                                <td>Errores actualización de Entradas</td>
                                <td><?php echo $error_act_entradas; ?></td>
                            </tr>
                            <tr>
                                <td>Registro de empleados con incidencias no identificadas</td>
                                <td><?php echo $incidencias_NO_identificdas; ?></td>
                            </tr>
                            <tr>
                                <td>Registros no identificados o no clasificados como entrada/salida</td>
                                <td><?php echo $reg_bio_NO_ent_sal; ?></td>
                            </tr>
                            <tr>
                                <td>Salidas Registradas</td>
                                <td><?php echo $salidas_ok; ?></td>
                            </tr>
                            <tr>
                                <td>Errores registro de Salidas</td>
                                <td><?php echo $errores_salidas; ?></td>
                            </tr>
                            <tr>
                                <td>Salidas Actualizadas</td>
                                <td><?php echo $act_salidas_ok; ?></td>
                            </tr>
                            <tr>
                                <td>Errores actualización de Salidas</td>
                                <td><?php echo $error_act_salidas; ?></td>
                            </tr>
                            <tr class="table-info">
                                <td colspan="2" style="color: white;"><b>Clic en inicio</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>