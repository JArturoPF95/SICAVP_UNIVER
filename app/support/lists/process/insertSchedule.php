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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['schedule_name'])) {
        $schedule = $_POST['schedule_name'];
    } else {
        $schedule = '';
    }

    if (isset($_POST['payroll'])) {
        $payroll = $_POST['payroll'];
    } else {
        $payroll = '';
    }

    $send = $_POST['send'];

    if (isset($_POST['schedule'])) {

        //Insertamos el horario en la tabla code_schedule
        $sqlInsertSchedule = "INSERT INTO code_schedule (DAYTRIP, SCHEDULE, FLEX_SCHEDULE, CREATED_BY, CREATED_DATE) VALUES ('$payroll','$schedule',1,'$user_active',NOW());";
        if ($mysqli->query($sqlInsertSchedule)) {

            //Validamos último regitro insertado para obtener el código
            $getCodeSchedule = "SELECT CODE_NOM FROM code_schedule ORDER BY CODE_NOM DESC LIMIT 1";
            $resultCode = $mysqli->query($getCodeSchedule);
            if ($resultCode->num_rows > 0) {
                while ($rowCode = $resultCode->fetch_assoc()) {
                    $codeNomSch = $rowCode['CODE_NOM'];
                }
            }

            //Insertamos el horario por días en admin_schedules
            foreach ($_POST['schedule'] as $index => $newSchedule) {
                $newDay = $newSchedule['day'] ?? '';
                $newPrevIn = $newSchedule['prevIn'] ?? '';
                $newStart = $newSchedule['start_time'] ?? '';
                $newDelay = $newSchedule['delay'] ?? '';
                $newAbsence = $newSchedule['absence'] ?? '';
                $newPrevOut = $newSchedule['prevOut'] ?? '';
                $newEnd = $newSchedule['end_time'] ?? '';

                if ($newPrevOut == '') {
                    $newPrevOut = $newEnd;
                }

                if ($newPrevIn == '') {
                    $newPrevIn = $newStart;
                }

                $sqlInsert = "INSERT INTO admin_schedules (CODE_DAY, CODE_SCHEDULE, MIN_TIME_START, TIME_START, DELAY_TIME_START, AUSENCE_TIME, LOCK_IN_TIME, MIN_TIME_OUT, OUT_TIME, CREATED_BY, CREATED_DATE)
                VALUES ('$newDay', '$codeNomSch', '$newPrevIn', '$newStart', '$newDelay', '$newAbsence', '$newAbsence', '$newPrevOut', '$newEnd', '$user_active', NOW());";
                //echo $sqlInsert . '<br>';
                if ($mysqli->query($sqlInsert)) {
                    $sqlDeleteExt = "DELETE FROM admin_schedules WHERE CODE_SCHEDULE = '$codeNomSch' AND TIME_START = '00:00:00' AND OUT_TIME = '00:00:00'";
                    if ($mysqli->query($sqlDeleteExt)) {
                        $message = 'Nuevo Horario creado con éxito';
                        $icon = 'success';
                    }
                } else {
                    $message = 'Error creando de nuevo horario';
                    $icon = 'error';
                }
            } //Cerramos foreach que inserta en admin_schedules

        } //Cerramos el if de insertar en code_schedule

    } else {
        $message = 'Error enviando datos de nuevo horario';
        $icon = 'error';
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
            <h4 class="mb-3">Nuevo Horario</h4>
            <div class="row my-2">
                <table class="table table-striped" style="font-size: 13px;">
                    <tbody>
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="text" name="send" value="1" hidden>
                                <tr>
                                    <td colspan="4">
                                        <label for="inputEmail4" class="form-label">Nombre Horario (Informativo)</label>
                                        <input type="text" name="payroll" class="form-control form-control-md" placeholder="Horario de Ejemplo Matutino Ventas Guardia" maxlength="40" required>
                                    </td>
                                    <td colspan="4">
                                        <label for="inputEmail4" class="form-label">Jornada (Informativo)</label>
                                        <input type="text" name="schedule_name" class="form-control form-control-md" placeholder="Lun a Vie 14:00 a 15:00, Sab 08:00 a 10:00" maxlength="200" required>
                                    </td>
                                </tr>
                                <?php
                                for ($dayCodeIns = 0; $dayCodeIns <= 6; $dayCodeIns++) {

                                    $sqlDays2 = "SELECT * FROM code_days WHERE CODE_DAY = '$dayCodeIns'";
                                    $resultDays2 = $mysqli->query($sqlDays2);
                                    if ($resultDays2->num_rows > 0) {
                                        while ($rowDays2 = $resultDays2->fetch_assoc()) {
                                            $nameDay2 = $rowDays2['NAME_DAY'];
                                ?>
                                            <tr>
                                                <td>
                                                    <label for="disabledTextInput" class="form-label"><?php echo $nameDay2 ?></label>
                                                    <input hidden type="text" name="schedule[<?php echo $dayCodeIns ?>][day]" id="disabledTextInput" class="form-control form-control-sm" value="<?php echo $dayCodeIns ?>">
                                                </td>
                                                <td>
                                                    <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                                    <input type="time" name="schedule[<?php echo $dayCodeIns ?>][prevIn]" class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                                    <input type="time" name="schedule[<?php echo $dayCodeIns ?>][start_time]" class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <label for="inputEmail4" class="form-label">Retardo</label>
                                                    <input type="time" name="schedule[<?php echo $dayCodeIns ?>][delay]" class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <label for="inputEmail4" class="form-label">Falta</label>
                                                    <input type="time" name="schedule[<?php echo $dayCodeIns ?>][absence]" class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <label for="inputEmail4" class="form-label">Salida Previa</label>
                                                    <input type="time" name="schedule[<?php echo $dayCodeIns ?>][prevOut]" class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <label for="inputEmail4" class="form-label">Hora Salida</label>
                                                    <input type="time" name="schedule[<?php echo $dayCodeIns ?>][end_time]" class="form-control form-control-sm">
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <tr class="flex-column align-items-right w-100 gap-2 pb-3 border-top-0 my-3">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><a href="../schedules.php" type="button" class="btn btn-md btn-secondary"><i class="bi bi-arrow-left-circle-fill"></i> &nbsp; Volver</a></td>
                                <td><button type="submit" class="btn btn-md btn-primary">Agregar</button></td>
                            </tr>
                        </form>
                    </tbody>
                </table>
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