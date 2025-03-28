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
}

$send = '0';
$day = array();

if (isset($_GET['id'])) {
    $scheduleC = $_GET['id'];
} else {
    $scheduleC = '';
}

$message = '';
$icon = '';

$getDaytrip = "SELECT * FROM code_schedule WHERE CODE_NOM = '$scheduleC'";
$resultDaytrip = $mysqli->query($getDaytrip);
if ($resultDaytrip->num_rows > 0) {
    while ($rowDaytrip = $resultDaytrip->fetch_assoc()) {
        $scheduleDaytrip = $rowDaytrip['DAYTRIP'];
        $scheduleData = $rowDaytrip['SCHEDULE'];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        $id = '';
    }

    $send = $_POST['send'];
    if ($send == '1') {

        foreach ($_POST['schedule'] as $index => $updSchedule) {
            $updDay = $updSchedule['day'] ?? '';
            $updPrevIn = $updSchedule['prevIn'] ?? '';
            $updStart = $updSchedule['start_time'] ?? '';
            $updDelay = $updSchedule['delay'] ?? '';
            $updAbsence = $updSchedule['absence'] ?? '';
            $updPrevEnd = $updSchedule['outPrev'] ?? '';
            $updEnd = $updSchedule['end_time'] ?? '';
            $sqlUpdate = "UPDATE admin_schedules SET MIN_TIME_START = '$updPrevIn', TIME_START = '$updStart', DELAY_TIME_START = '$updDelay', AUSENCE_TIME = '$updAbsence', LOCK_IN_TIME = '$updAbsence', MIN_TIME_OUT = '$updPrevEnd', OUT_TIME = '$updEnd', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE CODE_SCHEDULE = '$id' AND CODE_DAY = '$updDay';";
            if ($mysqli->query($sqlUpdate)) {
                //echo $sqlUpdate . '<br>';
                $message = 'Horario actualizado con éxito';
                $icon = 'success';
            } else {
                //echo $sqlUpdate . '<br>';
                $message = 'Error actualizando horario';
                $icon = 'error';
            }
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
    <title>IPs</title>
    <script src="../../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php
    if ($send == '0') {
    ?>

        <div class="container my-4" style="width: 100%;">
            <h4 class="mb-3">Actualizar Horario</h4>
            <div class="row my-2">
                <h5 class="mb-2"><?php echo $scheduleDaytrip ?></h5>
                <h6 class="mb-2"><?php echo $scheduleData ?></h6>
                <div class="row my-2">
                    <table class="table table-striped" style="font-size: 13px;">
                        <tbody>
                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                                <input hidden type="text" name="id" value="<?php echo $scheduleC ?>">
                                <input hidden type="text" name="send" value="1">
                                <tr>
                                    <td>
                                        <?php
                                        for ($dayCode = 0; $dayCode <= 6; $dayCode++) {

                                            $sqlDays = "SELECT * FROM code_days WHERE CODE_DAY = '$dayCode'";
                                            $resultDays = $mysqli->query($sqlDays);
                                            if ($resultDays->num_rows > 0) {
                                                while ($rowDays = $resultDays->fetch_assoc()) {
                                                    $nameDay = $rowDays['NAME_DAY'];

                                                    $sqlScheduleDetail = "SELECT DISTINCT TIME_START, OUT_TIME, MIN_TIME_START, DELAY_TIME_START, AUSENCE_TIME, LOCK_IN_TIME, MIN_TIME_OUT, OUT_TIME FROM admin_schedules WHERE CODE_SCHEDULE = '$scheduleC' AND CODE_DAY = '$dayCode'";
                                                    $resultScheduleDetail = $mysqli->query($sqlScheduleDetail);
                                                    if ($resultScheduleDetail->num_rows > 0) {
                                                        while ($rowScheduleD = $resultScheduleDetail->fetch_assoc()) {
                                                            $timeStart = $rowScheduleD['TIME_START'];
                                                            $timeEnd = $rowScheduleD['OUT_TIME'];
                                                            $timeBefore = $rowScheduleD['MIN_TIME_START'];
                                                            $timeDelay = $rowScheduleD['DELAY_TIME_START'];
                                                            $timeAbsence = $rowScheduleD['AUSENCE_TIME'];
                                                            $timeLock = $rowScheduleD['LOCK_IN_TIME'];
                                                            $timeBeforeOut = $rowScheduleD['MIN_TIME_OUT'];

                                        ?>
                                <tr>
                                    <td>
                                        <label for="disabledTextInput" class="form-label"><?php echo $nameDay ?></label>
                                        <input hidden type="text" name="schedule[<?php echo $dayCode ?>][day]" id="disabledTextInput" class="form-control form-control-sm" value="<?php echo $dayCode ?>">
                                    </td>
                                    <td>
                                        <label for="inputEmail4" class="form-label">Ingreso Previo</label>
                                        <input type="time" name="schedule[<?php echo $dayCode ?>][prevIn]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeBefore)) ?>">
                                    </td>
                                    <td>
                                        <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                        <input type="time" name="schedule[<?php echo $dayCode ?>][start_time]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeStart)) ?>">
                                    </td>
                                    <td>
                                        <label for="inputEmail4" class="form-label">Registro Retardo</label>
                                        <input type="time" name="schedule[<?php echo $dayCode ?>][delay]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeDelay)) ?>">
                                    </td>
                                    <td>
                                        <label for="inputEmail4" class="form-label">Registro Falta</label>
                                        <input type="time" name="schedule[<?php echo $dayCode ?>][absence]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeAbsence)) ?>">
                                    </td>
                                    <td>
                                        <label for="inputEmail4" class="form-label">Salida Previa</label>
                                        <input type="time" name="schedule[<?php echo $dayCode ?>][outPrev]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeBeforeOut)) ?>">
                                    </td>
                                    <td>
                                        <label for="inputEmail4" class="form-label">Hora Salida</label>
                                        <input type="time" name="schedule[<?php echo $dayCode ?>][end_time]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeEnd)) ?>">
                                    </td>
                                </tr>
            <?php
                                                        }
                                                    }
                                                }
                                            }
                                        }
            ?>


            <tr class="flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0 my-3">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="../schedules.php" type="button" class="btn btn-md btn-secondary"><i class="bi bi-arrow-left-circle-fill"></i> &nbsp; Volver</a></td>
                <td><button type="submit" class="btn btn-md btn-warning">Actualizar</button></td>
            </tr>
                            </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    } elseif ($send == '1') {
    ?>

        <script type="text/javascript">
            swal({
                title: "Nuevo Horario",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "../schedules.php?id=<?php echo $user_active ?>";
            });
        </script>
    <?php
    }
    ?>

</body>

</html>