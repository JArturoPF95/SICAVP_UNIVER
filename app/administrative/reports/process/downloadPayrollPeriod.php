<?php
require '../../../../lib/vendor/autoload.php';
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
    $user_city = $_SESSION['city'];
}

$payrollPeriodID = $_GET['id'];
$contCells = 1;
$contCells2 = 0;
$selectedDate = date('d/m/Y');
$status = '';
$id_nom_employed  = '';
$reportePeriodoNomina = '';

$sql_payrollPeriod = "SELECT PRP.DESCRIPTION, PRP.START_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID'";
$result_pPeriod = $mysqli->query($sql_payrollPeriod);
while ($rowcodePP = $result_pPeriod->fetch_assoc()) {
    $code = $rowcodePP['START_DATE'];
    $description = $rowcodePP['DESCRIPTION'];
}

require 'query_reports.php';

$sql_payrollPeriod_calendar;
$result_cellsDate = $mysqli->query($sql_payrollPeriod_calendar);

if ($result_cellsDate->num_rows > 0) {
    while ($rowCells = $result_cellsDate->fetch_assoc()) {
        $contCells++;
    }
}

$sqlGetSesion = "SELECT * FROM code_sesion WHERE CODE_SESION_NOM = '$user_sesion' AND CODE_CITY = '$user_city'";
$resultGetSesion = $mysqli->query($sqlGetSesion);
if ($resultGetSesion->num_rows > 0) {
    while ($rowSession = $resultGetSesion->fetch_assoc()) {
        $sesionName = $rowSession['NOM_SESION'];
    }
}

// Librerías Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill};

// EncabezadoPrincipal
$head = [
    'font' => [
        'color' => ['rgb' => 'FFFFFF'],
        'bold' => true,
        'size' => 14
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '010440']
    ],
];

// Encabezado Tabla
$tableHead = [
    'font' => [
        'color' => ['rgb' => 'FFFFFF'],
        'bold' => true,
        'size' => 11
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '5176A6']
    ],
];

$nombredeSpreadsheet = new Spreadsheet();
$nombredeSpreadsheet->getProperties()->setCreator("TIC NACER")->setTitle("Periodo " . $description);

$hojaActiva = $nombredeSpreadsheet->getActiveSheet();
$hojaActiva->setTitle("Periodo " . $description);

$columnLetter = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($contCells);

//Combinamos y Centramos
$hojaActiva->mergeCells('A1:' . $columnLetter . '1');
$hojaActiva->getStyle('A1:' . $columnLetter . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$hojaActiva->getStyle('A1:' . $columnLetter . '1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$hojaActiva->setCellValue('A1', $code . ' ' . $description . ' ' . $sesionName);

//Encabezados de tabla
$hojaActiva->getColumnDimension('A')->setAutoSize(true);
$hojaActiva->setCellValue('A2', 'Colaborador');

// Definir la letra de columna inicial
$columnLetter2 = 'B';

if ($result_cellsDate = $mysqli->query($sql_payrollPeriod_calendar)) {
    while ($rowCells = $result_cellsDate->fetch_assoc()) {

        $cellDate = $rowCells['CALENDAR_DATE'];
        $format_cellDate = date('d/m/Y', strtotime($cellDate));

        // Establecer el tamaño de la columna automáticamente
        $hojaActiva->getColumnDimension($columnLetter2)->setAutoSize(true);

        // Escribir la fecha en la celda correspondiente
        $hojaActiva->setCellValue($columnLetter2 . '2', $format_cellDate);

        // Avanzar a la siguiente letra de columna
        $columnLetter2++;
    }
}


$hojaActiva->getStyle('A1:' . $columnLetter . '1')->applyFromArray($head);
$hojaActiva->getStyle('A2:' . $columnLetter . '2')->applyFromArray($tableHead);

$row = 3;
$rowHead = 2;

$sql_employed_report = "SELECT 
DISTINCT
EMP.ID_NOM,
CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) NAME_EMP 
FROM employed EMP
WHERE (EMP.SUPERVISOR_ID = '$user_active' OR EMP.SUPERVISOR_ID_AUX = '$user_active')
AND (EMP.STATUS = 'A' OR EMP.SEPARATION_DATE BETWEEN (SELECT PRP.START_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID') AND (SELECT PRP.END_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID'))
AND (EMP.STATUS = 'A' OR EMP.ADMISSION_DATE > (SELECT PRP.START_DATE FROM payroll_period PRP WHERE PRP.ID = '$payrollPeriodID'))
ORDER BY EMP.ID_NOM ASC;";
$result_employedReport = $mysqli->query($sql_employed_report);
while ($rowEmpRep = $result_employedReport->fetch_assoc()) {

    $id_nom_employed = $rowEmpRep['ID_NOM'];

    $hojaActiva->setCellValue('A' . $row, $id_nom_employed . ' - ' . $rowEmpRep['NAME_EMP']); //Rellenamos la primer columna con el nombre de los empleados

    //require_once 'query_reports.php';

    $sql_paryrollPeriod_report_xlsx = "SELECT DISTINCT REGS.ID, REGS.START_DATE_PP, REGS.END_DATE_PP, REGS.CALENDAR_DATE, REGS.CODE_DAY, REGS.NAME_DAY
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
WHERE REGS.ID_NOM = '$id_nom_employed'  
AND REGS.SEPARATION_FLAG = 0
ORDER BY REGS.CALENDAR_DATE, REGS.ID_NOM;
";
    $result_incidenceAttendance = $mysqli->query($sql_paryrollPeriod_report_xlsx);
    if ($result_incidenceAttendance->num_rows > 0) {
        while ($rowIncAtt = $result_incidenceAttendance->fetch_assoc()) {
            // Recorremos las fechas en la fila 2 del Excel

            $columnLetter2 = 'B'; // Empezamos en la columna B
            while ($columnLetter2 <= $columnLetter) {
                $headerDate = $hojaActiva->getCell($columnLetter2 . $rowHead)->getValue(); // Obtener valor de la celda    

                // Comparar la fecha de asistencia con la fecha en la celda del Excel
                if ($headerDate == date('d/m/Y', strtotime($rowIncAtt['CALENDAR_DATE']))) {

                    $dateAtt = $rowIncAtt['CALENDAR_DATE'];
                    $status = $rowIncAtt['DES_INCIDENCE'];
                    $timeIn = $rowIncAtt['ENTRADA'];
                    $feriado = $rowIncAtt['Feriado'];
                    $timeOut = $rowIncAtt['SALIDA'];
                    $checkin = $rowIncAtt['REG_ENTRADA'];
                    $checkOut = $rowIncAtt['REG_SALIDA'];

                    //Validamos tiempo de retraso
                    $inCheck = new DateTime($dateAtt . $checkin);
                    $start = new DateTime($dateAtt . $timeIn);

                    $delay = $start->diff($inCheck);

                    if ($checkin <= $timeIn) {
                        $delayTime = '';
                    } else {
                        if (($delay->h + ($delay->days * 24)) > 1) {
                            $horas = "\n" . 'Retraso: ' . $delay->h + ($delay->days * 24) . ' horas';
                            if ($delay->i > 1) {
                                $minutos = ', ' . $delay->i . ' minutos';
                            } elseif ($delay->i == 1) {
                                $minutos = ', ' . $delay->i . ' minuto';
                            } else {
                                $minutos = '';
                            }
                        } elseif (($delay->h + ($delay->days * 24)) == 1) {
                            $horas = "\n" . 'Retraso: ' . $delay->h + ($delay->days * 24) . ' hora';
                            if ($delay->i > 1) {
                                $minutos = ', ' . $delay->i . ' minutos';
                            } elseif ($delay->i == 1) {
                                $minutos = ', ' . $delay->i . ' minuto';
                            } else {
                                $minutos = '';
                            }
                        } else {
                            $horas = '';
                            if ($delay->i > 1) {
                                $minutos = $delay->i . ' minutos';
                            } elseif ($delay->i == 1) {
                                $minutos = $delay->i . ' minuto';
                            } else {
                                $minutos = '';
                            }
                        }

                        $delayTime = $horas . $minutos;
                    }

                    //Validamos si salió antes de tiempo.
                    $outCheck = new DateTime($dateAtt . $checkOut);
                    $end = new DateTime($dateAtt . $timeOut);

                    $outBefore = $outCheck->diff($end);

                    if ($checkin == '') {
                        $outFlag = '';
                    } elseif ($checkOut == '') {
                        $outFlag = "\n".'No se capturó salida';
                    } else {
                        if ($checkOut >= $timeOut) {
                            $outFlag = '';
                        } else {
                            if ($checkOut != '') {
                                if (($outBefore->h + ($outBefore->days * 24)) > 1) {
                                    $horasOut = "\n".'Salida Anticipada: ' . $outBefore->h + ($outBefore->days * 24) . ' horas';
                                    if ($outBefore->i > 1) {
                                        $minutosOut = ', ' . $outBefore->i . ' minutos';
                                    } elseif ($outBefore->i == 1) {
                                        $minutosOut = ', ' . $outBefore->i . ' minuto';
                                    } else {
                                        $minutosOut = '';
                                    }
                                } elseif (($outBefore->h + ($outBefore->days * 24)) == 1) {
                                    $horasOut = "\n".'Salida Anticipada: ' . $outBefore->h + ($outBefore->days * 24) . ' hora';
                                    if ($outBefore->i > 1) {
                                        $minutosOut = ', ' . $outBefore->i . ' minutos';
                                    } elseif ($outBefore->i == 1) {
                                        $minutosOut = ', ' . $outBefore->i . ' minuto';
                                    } else {
                                        $minutosOut = '';
                                    }
                                } else {
                                    $horasOut = "\n".'Salida Anticipada: ' . '';
                                    if ($outBefore->i > 1) {
                                        $minutosOut = $outBefore->i . ' minutos';
                                    } elseif ($outBefore->i == 1) {
                                        $minutosOut = $outBefore->i . ' minuto';
                                    } else {
                                        $minutosOut = '';
                                    }
                                }
                                $outFlag = $horasOut . $minutosOut;
                            }
                        }
                    }

                    if ($timeIn == '') {
                        $incidence = "Descanso\n";
                    } elseif ($feriado == 'Feriado') {
                        $incidence = "Feriado\n";
                    } else {
                        $incidence = $status."\n";
                    }

                    if ($timeIn == '') {
                        $schedule = '';
                    } elseif ($feriado == 'Feriado') {
                        $schedule = '';
                    } else {
                        $schedule = 'Horario: ' . substr($timeIn, 0, 8) . ' a ' . substr($timeOut, 0, 8);
                    }

                    if ($timeIn == '') {
                        $jobTime = '';
                    } elseif ($feriado == 'Feriado') {
                        $jobTime = '';
                    } else {
                        $jobTime = "\n" . 'Entrada: ' . substr($checkin, 0, 8) . "\n" . 'Salida: ' . substr($checkOut, 0, 8);
                    }
                    $delays = $delayTime . $outFlag;

                    $hojaActiva->setCellValue($columnLetter2 . $row, $incidence . $schedule . $jobTime . $delays);
                }
                $columnLetter2++;
            }
        }
    }

    $row++;
}

$firstRow = 2;
$lastRow = $row - 1;
$hojaActiva->setAutoFilter("A" . $firstRow . ":" . $columnLetter . $lastRow);
$hojaActiva->getStyle("A" . $firstRow . ":" . $columnLetter . $lastRow)->getAlignment()->setWrapText(true); // Ajuste de texto
$hojaActiva->getStyle("A" . $firstRow . ":" . $columnLetter . $lastRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Alineación vertical
$hojaActiva->getStyle("A" . $firstRow . ":" . $columnLetter . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT); // Alineación horizontal


// Configura los encabezados antes de enviar la salida
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $description . ' ' . $user_active . '.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

// Envía el archivo Excel directamente al navegador
$writer = new Xlsx($nombredeSpreadsheet);
$writer->save('php://output');
exit;
