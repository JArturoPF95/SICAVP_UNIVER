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
    $user_sesion = $_SESSION['session'];
}

$payrollPeriodID = $_GET['id'];
$selectedDate = date('d/m/Y');

require 'query_reports.php';

$sql_payrollPeriod;
$result_payollPeriod = $mysqli -> query($sql_payrollPeriod);
while ($row_payrollPeriod = $result_payollPeriod -> fetch_assoc()) {
    $code = $row_payrollPeriod['START_DATE'];
    $description = $row_payrollPeriod['DESCRIPTION'];
}

// Ruta donde deseas guardar el archivo
$ruta_carpeta = "../files/";

// Nombre del archivo
$file_name = "Periodo ".$code.' '.$description." ".$user_sesion.".txt";

// Abre o crea el archivo en modo escritura
$file = fopen($ruta_carpeta . $file_name, "w") or die("No se pudo abrir el archivo.");

$sql_getAbsences = "SELECT DISTINCT REGS.ID, REGS.START_DATE_PP, REGS.END_DATE_PP, REGS.CALENDAR_DATE, REGS.CODE_DAY, REGS.NAME_DAY
, REGS.DAY_OF_REST, REGS.PERIOD_PAYROLL_ID, REGS.SCHEDULE, REGS.ID_NOM, REGS.NAME_EMP, REGS.JOB_NAME, REGS.INSTITUTION, REGS.PAYROLL
, REGS.Feriado, REGS.ENTRADA, REGS.REG_ENTRADA, REGS.DES_INCIDENCE, REGS.CODE_INCIDENC, REGS.SALIDA, REGS.REG_SALIDA, REGS.SEPARATION_FLAG
FROM (
    (SELECT
        PN.ID
        , PN.START_DATE START_DATE_PP
        , PN.END_DATE END_DATE_PP
        , CAL.YEAR
        , CAL.CALENDAR_DATE
        , CAL.WEEK
        , CAL.CODE_DAY
        , DAYS.NAME_DAY
        , CAL.DAY_OF_REST
        , CAL.PERIOD_PAYROLL_ID
        , HA.SCHEDULE
        , HA.ID_NOM
        , USR.SEPARATION_FLAG
        , (SELECT CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) FROM employed EMP WHERE EMP.ID_NOM = HA.ID_NOM) NAME_EMP
        , (SELECT JOB.JOB_NAME FROM employed EMP INNER JOIN code_jobs JOB ON JOB.CODE_JOB = EMP.JOB WHERE EMP.ID_NOM = HA.ID_NOM) JOB_NAME
        , (SELECT EMP.INSTITUTION FROM employed EMP WHERE EMP.ID_NOM = HA.ID_NOM) INSTITUTION
                            , (SELECT EMP.PAYROLL FROM employed EMP WHERE EMP.ID_NOM = HA.ID_NOM) PAYROLL
        , HA.START_DATE START_DATE_SH
        , HA.END_DATE END_DATE_SH
        , IF(CAL.DAY_OF_REST = 0, 'Laboral', 'Feriado') Feriado
        , IFNULL(HOR.TIME_START, '') ENTRADA
        , REG.ATTENDANCE_TIME REG_ENTRADA
        , IF(HOR.TIME_START IS NOT NULL AND REG.ATTENDANCE_TIME IS NULL AND CI.CODE_TINC IS NULL, 'FALTA INJUSTIFICADA', CI.DESCRIP_TINC) DES_INCIDENCE
        , IF(HOR.TIME_START IS NOT NULL AND REG.ATTENDANCE_TIME IS NULL AND CI.CODE_TINC IS NULL, 01, CI.CODE_TINC) CODE_INCIDENC
        , IFNULL(HOR.OUT_TIME, '') SALIDA
        , REG2.ATTENDANCE_TIME REG_SALIDA
    FROM 
        payroll_period PN
        INNER JOIN calendar CAL ON CAL.CALENDAR_DATE BETWEEN PN.START_DATE AND PN.END_DATE AND PN.ID = CAL.PERIOD_PAYROLL_ID
        LEFT OUTER JOIN assigned_schedule HA ON CAL.CALENDAR_DATE BETWEEN HA.START_DATE AND HA.END_DATE
        LEFT OUTER JOIN admin_schedules HOR ON HA.SCHEDULE = HOR.CODE_SCHEDULE AND CAL.CODE_DAY = HOR.CODE_DAY
        LEFT OUTER JOIN admin_attendance REG ON CAL.CALENDAR_DATE = REG.ATTENDANCE_DATE AND HA.ID_NOM = REG.NOM_ID
                                            AND CAL.CODE_DAY = REG.CODE_DAY AND REG.IN_OUT = 1
        LEFT OUTER JOIN admin_attendance REG2 ON CAL.CALENDAR_DATE = REG2.ATTENDANCE_DATE AND HA.ID_NOM = REG2.NOM_ID
                                            AND CAL.CODE_DAY = REG2.CODE_DAY AND REG2.IN_OUT = 2
        LEFT OUTER JOIN code_incidence CI ON REG.TINC = CI.CODE_TINC
        INNER JOIN code_days DAYS ON DAYS.CODE_DAY = CAL.CODE_DAY
        INNER JOIN users USR ON USR.SICAVP_USER = HA.ID_NOM
    WHERE 
        PN.ID = '$payrollPeriodID'
        AND HA.ID_NOM IN (SELECT DISTINCT EMP.ID_NOM FROM employed EMP WHERE (EMP.SUPERVISOR_ID = '$user_active' OR EMP.SUPERVISOR_ID_AUX = '$user_active'))
        AND CAL.CALENDAR_DATE <= NOW())
    UNION
(SELECT
                            PN.ID
                            , PN.START_DATE START_DATE_PP
                            , PN.END_DATE END_DATE_PP
                            , CAL.YEAR
                            , CAL.CALENDAR_DATE
                            , CAL.WEEK
                            , CAL.CODE_DAY
                            , DAYS.NAME_DAY
                            , CAL.DAY_OF_REST
                            , CAL.PERIOD_PAYROLL_ID
                            , HA.SCHEDULE_GROUP
                            , HA.ID_NOM
                            , USR.SEPARATION_FLAG
                            , (SELECT CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) FROM employed EMP WHERE EMP.ID_NOM = HA.ID_NOM) NAME_EMP
                            , (SELECT JOB.JOB_NAME FROM employed EMP INNER JOIN code_jobs JOB ON JOB.CODE_JOB = EMP.JOB WHERE EMP.ID_NOM = HA.ID_NOM) JOB_NAME
                            , (SELECT EMP.INSTITUTION FROM employed EMP WHERE EMP.ID_NOM = HA.ID_NOM) INSTITUTION
                            , (SELECT EMP.PAYROLL FROM employed EMP WHERE EMP.ID_NOM = HA.ID_NOM) PAYROLL
                            , '' START_DATE_SH
                            , '' END_DATE_SH
                            , IF(CAL.DAY_OF_REST = 0, 'Laboral', 'Feriado') Feriado
                            , IFNULL(HOR.TIME_START, '') ENTRADA
                            , REG.ATTENDANCE_TIME REG_ENTRADA
                            , IF(HOR.TIME_START IS NOT NULL AND REG.ATTENDANCE_TIME IS NULL AND CI.CODE_TINC IS NULL, 'FALTA INJUSTIFICADA', CI.DESCRIP_TINC) DES_INCIDENCE
                            , IF(HOR.TIME_START IS NOT NULL AND REG.ATTENDANCE_TIME IS NULL AND CI.CODE_TINC IS NULL, 01, CI.CODE_TINC) CODE_INCIDENC
                            , IFNULL(HOR.OUT_TIME, '') SALIDA
                            , REG2.ATTENDANCE_TIME REG_SALIDA
                            FROM 
                            payroll_period PN
                            INNER JOIN calendar CAL ON CAL.CALENDAR_DATE BETWEEN PN.START_DATE AND PN.END_DATE AND PN.ID = CAL.PERIOD_PAYROLL_ID
                            INNER JOIN employed HA ON HA.ID_NOM IN (SELECT DISTINCT EMP.ID_NOM FROM employed EMP WHERE (EMP.SUPERVISOR_ID = '$user_active' OR EMP.SUPERVISOR_ID_AUX = '$user_active')) 
                            LEFT OUTER JOIN admin_schedules HOR ON HA.SCHEDULE_GROUP = HOR.CODE_SCHEDULE AND CAL.CODE_DAY = HOR.CODE_DAY
                            LEFT OUTER JOIN admin_attendance REG ON CAL.CALENDAR_DATE = REG.ATTENDANCE_DATE AND HA.ID_NOM = REG.NOM_ID
                                            AND CAL.CODE_DAY = REG.CODE_DAY AND REG.IN_OUT = 1
                            LEFT OUTER JOIN admin_attendance REG2 ON CAL.CALENDAR_DATE = REG2.ATTENDANCE_DATE AND HA.ID_NOM = REG2.NOM_ID
                                            AND CAL.CODE_DAY = REG2.CODE_DAY AND REG2.IN_OUT = 2
                            LEFT OUTER JOIN code_incidence CI ON REG.TINC = CI.CODE_TINC
                            INNER JOIN code_days DAYS ON DAYS.CODE_DAY = CAL.CODE_DAY
                            INNER JOIN users USR ON USR.SICAVP_USER = HA.ID_NOM
                            WHERE PN.ID = '$payrollPeriodID' 
                            AND HA.ID_NOM IN (SELECT DISTINCT EMP.ID_NOM FROM employed EMP WHERE (EMP.SUPERVISOR_ID = '$user_active' OR EMP.SUPERVISOR_ID_AUX = '$user_active')) 
                            AND CAL.CALENDAR_DATE <= NOW()
                            AND CONCAT(CAL.YEAR,'-',CAL.WEEK,'-',HA.ID_NOM) NOT IN (SELECT DISTINCT CONCAT(SHA.YEAR,'-',SHA.WEEK,'-',SHA.ID_NOM) FROM assigned_schedule SHA))
) AS REGS
WHERE REGS.CODE_INCIDENC IN (1,2,3,4)
AND REGS.SEPARATION_FLAG = 0 AND REGS.Feriado = 'Laboral' AND REGS.ENTRADA <> '' AND REGS.SALIDA <> ''
ORDER BY REGS.CALENDAR_DATE, REGS.ID_NOM;
";
$resultAbsences = $mysqli->query($sql_getAbsences);

if ($resultAbsences->num_rows > 0) {
    while ($rowAbsences = $resultAbsences->fetch_assoc()) {
        $organization = str_pad($rowAbsences['INSTITUTION'], 3, '0', STR_PAD_LEFT);
        $employed = str_pad($rowAbsences['ID_NOM'], 7, '0', STR_PAD_LEFT);
        $date = $rowAbsences['CALENDAR_DATE'];
        $payroll = str_pad($rowAbsences['PAYROLL'], 5, '0', STR_PAD_LEFT);
        $incidence = str_pad($rowAbsences['CODE_INCIDENC'], 2, '0', STR_PAD_LEFT);
        
        // Contenido que deseas escribir en el archivo
        $content = $organization . ',' . $employed . ',' . date('d-m-Y', strtotime($date)) . ',' . $payroll . ',' . $incidence . ',';
        
        // Escribe el contenido en el archivo
        fwrite($file, $content . "\n"); // Añadir nueva línea al final de cada entrada
    }

    // Cierra el archivo
    fclose($file);

    header("Content-disposition: attachment; filename=".$ruta_carpeta.$file_name);
    header("Content-type: MIME");
    readfile($ruta_carpeta.$file_name);
} else {
        //echo 'No se encontraron resultados';
        fwrite($file, 'No se encontraron resultados en el periodo seleccionado');

        // Cierra el archivo
        fclose($file);

        header("Content-disposition: attachment; filename=".$ruta_carpeta.$file_name);
        header("Content-type: MIME");
        readfile($ruta_carpeta.$file_name);
}
?>