<?php

require_once '../../logic/conn.php';
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

$today = date('Y-m-d');
$payrollPEndDate = '';
$payrollPStartDate = '';
$year = date('Y');
$background = 'bg-info';
$flag_send = 0;
$payrollID = 0;
$selectedDate = '';
$outFlag = '';
$horas = '';
$horasOut = '';
$minutos = '';
$minutosOut = '';

$alert = '
<div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center my-3" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>Sin registros en el periodo de nómina seleccionado</div>
</div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supervisor_id = $_POST['supervisor_id'];
    if (isset($_POST['codePP'])) {
        $payrollPeriodID = $_POST['codePP'];
    } else {
        $payrollPeriodID = '0';
    }
    $flag_send = $_POST['flag'];

    $sqlPayrollP = "SELECT * FROM payroll_period WHERE ID = $payrollPeriodID ORDER BY CODE ASC";
    $resultPayrollP = $mysqli->query($sqlPayrollP);
    if ($resultPayrollP->num_rows > 0) {
        while ($rowPayrollP = $resultPayrollP->fetch_assoc()) {
            $payrollPEndDate = $rowPayrollP['END_DATE'];
            $payrollPStartDate = $rowPayrollP['START_DATE'];
            $payrollPID = $rowPayrollP['CODE'];
            $payrollPDesc = $rowPayrollP['DESCRIPTION'];
            $payrollPYear = $rowPayrollP['YEAR'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Quincenal</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
</head>

<body>
    <div class="container my-4" style="width: 100%;">
        <h4 class="mb-3">Reporte Periodo de Nómina</h4>
        <div class="row my-2">
            <div class="col">
                <form class="row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <input hidden type="text" name="flag" id="" value="1">
                    <div class="col-sm-5">
                        <select class="form-select" id="specificSizeSelect" name="codePP">
                            <option selected disabled>Periodo de Nómina</option>
                            <?php
                            $sql_payrollTForm = "SELECT * FROM payroll_period WHERE START_DATE <= '$today' AND YEAR = '$year' ORDER BY CODE ASC";
                            $result_payrollTForm = $mysqli->query($sql_payrollTForm);
                            if ($result_payrollTForm->num_rows > 0) {
                                while ($row_payrollTForm = $result_payrollTForm->fetch_assoc()) {
                                    $id = $row_payrollTForm['ID'];
                                    $year = $row_payrollTForm['YEAR'];
                                    $code = $row_payrollTForm['CODE'];
                                    $description = $row_payrollTForm['DESCRIPTION'];
                                    $start_date = $row_payrollTForm['START_DATE'];
                                    $end_date = $row_payrollTForm['END_DATE'];
                            ?>
                                    <option value="<?php echo $id ?>"><?php echo $code . ' ' . $description ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input hidden type="text" class="form-control" id="specificSizeInputName" name="supervisor_id" value="<?php echo $supervisor_id ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Seleccionar</button>
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-secondary" href="process/downloadPayrollPeriod.php?id=<?php echo $payrollPeriodID ?>" role="button">Descargar Reporte</a>
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-secondary" href="process/downloadAbsences.php?id=<?php echo $payrollPeriodID ?>" role="button">Faltas Nom2001</a>
                    </div>
                </form>
            </div>
        </div>
        <?php
        if ($flag_send == 1) {
        ?>
            <div class="row my-2">
                <?php

                //require 'process/query_reports.php';
                $sql_paryrollPeriod_report = "SELECT DISTINCT REGS.ID, REGS.START_DATE_PP, REGS.END_DATE_PP, REGS.CALENDAR_DATE, REGS.CODE_DAY, REGS.NAME_DAY
, REGS.DAY_OF_REST, REGS.PERIOD_PAYROLL_ID, REGS.SCHEDULE, REGS.ID_NOM, REGS.NAME_EMP, REGS.JOB_NAME, REGS.SEPARATION_FLAG
, REGS.Feriado, REGS.ENTRADA, REGS.REG_ENTRADA, REGS.DES_INCIDENCE, REGS.CODE_INCIDENC, REGS.SALIDA, REGS.REG_SALIDA
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
        AND HA.ID_NOM IN (SELECT DISTINCT EMP.ID_NOM FROM employed EMP WHERE EMP.SUPERVISOR_ID = '$user_active' OR EMP.SUPERVISOR_ID_AUX = '$user_active')
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
                            INNER JOIN employed HA ON HA.ID_NOM IN (SELECT DISTINCT EMP.ID_NOM FROM employed EMP WHERE EMP.SUPERVISOR_ID = '$user_active' OR EMP.SUPERVISOR_ID_AUX = '$user_active') 
                            LEFT OUTER JOIN admin_schedules HOR ON HA.SCHEDULE_GROUP = HOR.CODE_SCHEDULE AND CAL.CODE_DAY = HOR.CODE_DAY
                            LEFT OUTER JOIN admin_attendance REG ON CAL.CALENDAR_DATE = REG.ATTENDANCE_DATE AND HA.ID_NOM = REG.NOM_ID
                                            AND CAL.CODE_DAY = REG.CODE_DAY AND REG.IN_OUT = 1
                            LEFT OUTER JOIN admin_attendance REG2 ON CAL.CALENDAR_DATE = REG2.ATTENDANCE_DATE AND HA.ID_NOM = REG2.NOM_ID
                                            AND CAL.CODE_DAY = REG2.CODE_DAY AND REG2.IN_OUT = 2
                            LEFT OUTER JOIN code_incidence CI ON REG.TINC = CI.CODE_TINC
                            INNER JOIN code_days DAYS ON DAYS.CODE_DAY = CAL.CODE_DAY
                            INNER JOIN users USR ON USR.SICAVP_USER = HA.ID_NOM
                            WHERE PN.ID = '$payrollPeriodID' 
                            AND HA.ID_NOM IN (SELECT DISTINCT EMP.ID_NOM FROM employed EMP WHERE EMP.SUPERVISOR_ID = '$user_active' OR EMP.SUPERVISOR_ID_AUX = '$user_active') 
                            AND CAL.CALENDAR_DATE <= NOW()
                            AND CONCAT(CAL.YEAR,'-',CAL.WEEK,'-',HA.ID_NOM) NOT IN (SELECT DISTINCT CONCAT(SHA.YEAR,'-',SHA.WEEK,'-',SHA.ID_NOM) FROM assigned_schedule SHA))
) AS REGS
WHERE SEPARATION_FLAG = 0
ORDER BY REGS.CALENDAR_DATE, REGS.ID_NOM;
";
                $result_payrollPReport = $mysqli->query($sql_paryrollPeriod_report);

                //echo $sql_paryrollPeriod_report;

                if ($result_payrollPReport->num_rows > 0) {
                ?>

                    <table class="table table-hover table-bordered table-sm" style="font-size: 13px;">
                        <thead class="text-center table-primary">
                            <th class="text-white fw-bold">Día</th>
                            <th class="text-white fw-bold">Fecha</th>
                            <th class="text-white fw-bold">Puesto</th>
                            <th class="text-white fw-bold">Colaborador</th>
                            <th class="text-white fw-bold">Estatus</th>
                            <th class="text-white fw-bold">Hora de <br> Entrada</th>
                            <th class="text-white fw-bold">Entrada</th>
                            <th class="text-white fw-bold">Tiempo <br> de Retraso</th>
                            <th class="text-white fw-bold">Hora de <br> Salida</th>
                            <th class="text-white fw-bold">Salida</th>                            
                            <th class="text-white fw-bold">Salida <br> Anticipada</th>
                        </thead>
                        <?php
                        while ($row_payrollPRepor = $result_payrollPReport->fetch_assoc()) {
                            $calendarDate = $row_payrollPRepor['CALENDAR_DATE'];
                            $nameDay = $row_payrollPRepor['NAME_DAY'];
                            $employed = $row_payrollPRepor['NAME_EMP'];
                            $employJob = $row_payrollPRepor['JOB_NAME'];
                            $incidence = $row_payrollPRepor['DES_INCIDENCE'];
                            $checkin = $row_payrollPRepor['REG_ENTRADA'];
                            $checkout = $row_payrollPRepor['REG_SALIDA'];
                            $code_tinc = $row_payrollPRepor['CODE_INCIDENC'];
                            $start_time = $row_payrollPRepor['ENTRADA'];
                            $out_time = $row_payrollPRepor['SALIDA'];
                            $feriado = $row_payrollPRepor['Feriado'];

                            //Validamos tiempo de retraso o salida anticipada
                            $inCheck = new DateTime($calendarDate . $checkin);
                            $start = new DateTime($calendarDate . $start_time);


                            $delay = $start -> diff($inCheck);

                                if ($checkin <= $start_time) {
                                    $delayTime = '';
                                } else {
                                    if (($delay -> h + ($delay->days * 24)) > 1 ) {
                                        $horas = $delay -> h + ($delay->days * 24) . ' horas';
                                        if ($delay -> i > 1) {
                                            $minutos = ', ' . $delay -> i . ' minutos';
                                        } elseif ($delay -> i == 1) {
                                            $minutos = ', ' . $delay -> i . ' minuto';
                                        } else {
                                            $minutos = '';
                                        }
                                    } elseif ( ($delay -> h + ($delay->days * 24)) == 1 ) {
                                        $horas = $delay -> h + ($delay->days * 24) . ' hora';
                                        if ($delay -> i > 1) {
                                            $minutos = ', ' . $delay -> i . ' minutos';
                                        } elseif ($delay -> i == 1) {
                                            $minutos = ', ' . $delay -> i . ' minuto';
                                        } else {
                                            $minutos = '';
                                        }
                                    } else {
                                        $horas = '';
                                        if ($delay -> i > 1) {
                                            $minutos = $delay -> i . ' minutos';
                                        } elseif ($delay -> i == 1) {
                                            $minutos = $delay -> i . ' minuto';
                                        } else {
                                            $minutos = '';
                                        }
                                    }

                                    $delayTime = $horas.$minutos;
                                }

                            
                            $outCheck = new DateTime($calendarDate . $checkout);
                            $end = new DateTime($calendarDate . $out_time);

                            $outBefore = $outCheck -> diff($end);

                            if ($checkin == '') {
                                $outFlag = '';
                            } elseif ($checkout == '') {
                                $outFlag = 'No se capturó salida';
                            } else {
                                    if ($checkout >= $out_time) {
                                        $outFlag = '';
                                    } else {
                                        if ($checkout != '') {
                                            if (($outBefore -> h + ($outBefore->days * 24)) > 1 ) {
                                                $horasOut = $outBefore -> h + ($outBefore->days * 24) . ' horas';
                                                if ($outBefore -> i > 1) {
                                                    $minutosOut = ', ' . $outBefore -> i . ' minutos';
                                                } elseif ($outBefore -> i == 1) {
                                                    $minutosOut = ', ' . $outBefore -> i . ' minuto';
                                                } else {
                                                    $minutosOut = '';
                                                }
                                            } elseif ( ($outBefore -> h + ($outBefore->days * 24)) == 1 ) {
                                                $horasOut = $outBefore -> h + ($outBefore->days * 24) . ' hora';
                                                if ($outBefore -> i > 1) {
                                                    $minutosOut = ', ' . $outBefore -> i . ' minutos';
                                                } elseif ($outBefore -> i == 1) {
                                                    $minutosOut = ', ' . $outBefore -> i . ' minuto';
                                                } else {
                                                    $minutosOut = '';
                                                }
                                            } else {
                                                $horasOut = '';
                                                if ($outBefore -> i > 1) {
                                                    $minutosOut = $outBefore -> i . ' minutos';
                                                } elseif ($outBefore -> i == 1) {
                                                    $minutosOut = $outBefore -> i . ' minuto';
                                                } else {
                                                    $minutosOut = '';
                                                }
                                            }
                                            $outFlag = $horasOut.$minutosOut;
                                        } 
                                    }
                            }

                            if ( (($incidence == 'FALTA INJUSTIFICADA' || $incidence == 'FALTA POR RETARDOS' || $incidence == 'PERMISO SIN GOCE' || $incidence == 'SUSPENSION') && $incidence != '') && $feriado != 'Feriado') {
                                $background = 'table-danger';
                            } elseif ($incidence == 'RETARDO') {
                                $background = 'table-warning';
                            } else {
                                $background = 'table-success';
                            } 
                        ?>
                            <tbody class="text-center <?php echo $background ?>">
                                <tr>
                                    <td>
                                        <p><?php echo $nameDay ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo date('d/m/Y', strtotime($calendarDate)) ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $employJob ?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $employed ?></p>
                                    </td>
                                    <td><?php if ($start_time == '') {
                                        echo 'DESCANSO';
                                    } elseif ($feriado == 'Feriado') {
                                        echo 'FERIADO';
                                    } else {
                                        echo $incidence;
                                    }?></td>
                                    <td>
                                        <p><?php if ($start_time !== '') { echo date("H:i:s", strtotime($start_time)); } else { echo ''; }?></p>
                                    </td>
                                    <td>
                                        <p><?php if ($checkin !== NULL) { echo date("H:i:s", strtotime($checkin)); } else { echo ''; }?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $delayTime ?></p>
                                    </td>
                                    <td>
                                        <p><?php if ($out_time !== '') { echo date("H:i:s", strtotime($out_time)); } else { echo ''; }?></p>
                                    </td>
                                    <td>
                                        <p><?php if ($checkout !== NULL) { echo date("H:i:s", strtotime($checkout)); } else { echo ''; }?></p>
                                    </td>
                                    <td>
                                        <p><?php echo $outFlag; ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        <?php
                        }
                        ?>
                    </table>
            
                    <?php
                } else {
                    echo $alert;
                }
            }
            ?>
            </div>
    </div>
</body>

</html>