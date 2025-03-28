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

$payrollPeriodID = '';

$sqlScheduleWeek = "SELECT * FROM assigned_schedule WHERE NOW() BETWEEN START_DATE AND END_DATE AND ID_NOM = '$user_active'";
        $resultWeek = $mysqli -> query($sqlScheduleWeek);
        if ($resultWeek -> num_rows > 0) {
            while ($rowWeek = $resultWeek -> fetch_assoc()) {
                $selectedWeek = $rowWeek['ASSIGNMENT_ID'];
            }
        } else {
            $selectedWeek = '0';
        }
require 'process/query_attendance.php';

$alert = '
<div class="alert alert-warning my-3 d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>No cuenta con ningún horario asignado o la información de horarios es incorrecta.</div>
</div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['week'])) {
        $selectedWeek = $_POST['week'];
    } else {
        $selectedWeek = 0;
    }
    

}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoario</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>
    <div class="container-fluid" style="width: 85%; height: 100%">
        <h4 class="mb-3">Horario</h4>

        <div class="row">
        <div class="col">
                <form class="row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <input hidden type="text" name="flag" id="" value="1">
                    <div class="col-sm-4">
                        <label class="visually-hidden" for="specificSizeSelect">Semana</label>
                        <select class="form-select" id="specificSizeSelect" name="week">
                            <option disabled selected>Semana</option>
                            <?php
                            $sqlAssignedSchedule = "SELECT DISTINCT CAL.WEEK,
                                (SELECT DISTINCT CAL2.CALENDAR_DATE FROM calendar CAL2 WHERE CAL2.WEEK = CAL.WEEK AND CAL2.YEAR = CAL.YEAR ORDER BY CALENDAR_DATE ASC LIMIT 1) START_DATE,
                                (SELECT DISTINCT CAL2.CALENDAR_DATE FROM calendar CAL2 WHERE CAL2.WEEK = CAL.WEEK AND CAL2.YEAR = CAL.YEAR ORDER BY CALENDAR_DATE DESC LIMIT 1) END_DATE
                                FROM calendar CAL 
                                WHERE CAL.YEAR = YEAR(NOW()) AND (SELECT DISTINCT CAL2.CALENDAR_DATE FROM calendar CAL2 WHERE CAL2.WEEK = CAL.WEEK AND CAL2.YEAR = CAL.YEAR ORDER BY CALENDAR_DATE ASC LIMIT 1) >= DATE(NOW());";
                            $resultAssignedSchedule = $mysqli->query($sqlAssignedSchedule);
                            if ($resultAssignedSchedule->num_rows > 0) {
                                while ($rowAssignedSchedule = $resultAssignedSchedule->fetch_assoc()) {
                                    $id = $rowAssignedSchedule['WEEK'];
                                    $start_date = $rowAssignedSchedule['START_DATE'];
                                    $end_date = $rowAssignedSchedule['END_DATE'];
                            ?>
                                    <option value="<?php echo $id ?>"><?php echo date('d/m/Y', strtotime($start_date)) . ' al ' . date('d/m/Y', strtotime($end_date)) ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Seleccionar</button>
                </form>
            </div>
        </div>
        <div class="row">
                <?php

                /** Horario */

                $sql_schedule = "SELECT DISTINCT * FROM assigned_schedule ASH
                LEFT OUTER JOIN admin_schedules ADS ON ADS.CODE_SCHEDULE = ASH.SCHEDULE
                INNER JOIN code_days DYS ON DYS.CODE_DAY = ADS.CODE_DAY
                INNER JOIN calendar CAL ON CAL.WEEK = ASH.WEEK AND CAL.YEAR = ASH.YEAR AND ADS.CODE_DAY = CAL.CODE_DAY
                WHERE ASH.ID_NOM = '$user_active' AND ASH.WEEK = '$selectedWeek'";

                $result_schedule = $mysqli->query($sql_schedule);
                if ($result_schedule->num_rows > 0) {
                ?>
                
        <table class="table my-3 table-hover table-bordered">
            <thead>
                <tr class="text-white text-center table-primary">
                    <th scope="col">Día</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Entrada</th>
                    <th scope="col">Retardo</th>
                    <th scope="col">Falta</th>
                    <th scope="col">Salida</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while ($row_schedule = $result_schedule->fetch_assoc()) {
                        $schedule_day = $row_schedule['NAME_DAY'];
                        $schedule_date = date('d/m/Y', strtotime($row_schedule['CALENDAR_DATE']));
                        $schedule_in = substr($row_schedule['TIME_START'], 0, 8);
                        $schedule_delay = substr($row_schedule['DELAY_TIME_START'], 0, 8);
                        $schedule_lock = substr($row_schedule['AUSENCE_TIME'], 0, 8);
                        $schedule_end = substr($row_schedule['OUT_TIME'], 0, 8);
                ?>
                        <tr class="text-center">
                            <td><?php  echo $schedule_day ?></td>
                            <td><?php  echo $schedule_date ?></td>
                            <td><?php  echo $schedule_in ?></td>
                            <td><?php if( $schedule_delay == '00:00:00' ) { echo '-'; } else { echo $schedule_delay; }?></td>
                            <td><?php if( $schedule_lock == '00:00:00' ) { echo '-'; } else { echo $schedule_lock; }?></td>
                            <td><?php  echo $schedule_end ?></td>
                        </tr>
                <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
                } else {
                    $sqlDefSchedule = "SELECT * FROM employed EMP 
                        INNER JOIN admin_schedules ASH ON ASH.CODE_SCHEDULE = EMP.SCHEDULE_GROUP
                        INNER JOIN code_days DYS ON DYS.CODE_DAY = ASH.CODE_DAY
                        INNER JOIN calendar CAL ON ASH.CODE_DAY = CAL.CODE_DAY
                        WHERE EMP.ID_NOM = '$user_active' AND CAL.WEEK = '$selectedWeek' AND CAL.YEAR = YEAR(NOW());";
                    $resultDefSchedule = $mysqli -> query($sqlDefSchedule);
                    if ($resultDefSchedule -> num_rows > 0) {
                        ?>
                        <table class="table my-3 table-hover table-bordered">
            <thead>
                <tr class="text-white text-center table-primary">
                    <th scope="col">Día</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Entrada</th>
                    <th scope="col">Retardo</th>
                    <th scope="col">Falta</th>
                    <th scope="col">Salida</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while ($rowDefSchedule = $resultDefSchedule->fetch_assoc()) {
                        $schedule_day = $rowDefSchedule['NAME_DAY'];
                        $schedule_date = date('d/m/Y', strtotime($rowDefSchedule['CALENDAR_DATE']));
                        $schedule_in = substr($rowDefSchedule['TIME_START'], 0, 8);
                        $schedule_delay = substr($rowDefSchedule['DELAY_TIME_START'], 0, 8);
                        $schedule_lock = substr($rowDefSchedule['AUSENCE_TIME'], 0, 8);
                        $schedule_end = substr($rowDefSchedule['OUT_TIME'], 0, 8);
                ?>
                        <tr class="text-center">
                            <td><?php  echo $schedule_day ?></td>
                            <td><?php  echo $schedule_date ?></td>
                            <td><?php  echo $schedule_in ?></td>
                            <td><?php if( $schedule_delay == '00:00:00' ) { echo '-'; } else { echo $schedule_delay; }?></td>
                            <td><?php if( $schedule_lock == '00:00:00' ) { echo '-'; } else { echo $schedule_lock; }?></td>
                            <td><?php  echo $schedule_end ?></td>
                        </tr>
                <?php
                    }
                    ?>
                    </tbody>
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