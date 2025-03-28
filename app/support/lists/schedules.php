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

$send = '0';
$day = array();

$message = '';
$icon = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $send = $_POST['send'];
    if ($send == '2') {

        $scheduleC = $_POST['schedule2'];

        foreach ($_POST['schedule'] as $index => $updSchedule) {
            $updDay = $updSchedule['day'] ?? '';
            $updStart = $updSchedule['start_time'] ?? '';
            $updEnd = $updSchedule['end_time'] ?? '';
            $updBreak = $updSchedule['break_time'] ?? '';

            $updEnd = $updSchedule['end_time'] ?? '';
            $sqlUpdateBreakTime2 = "UPDATE admin_schedules SET BREAK_TIME = $updBreak, TIME_START = '$updStart', OUT_TIME = '$updEnd', MODIFIED_BY = '$user_active', MODIFIED_DATE = NOW() WHERE CODE_SCHEDULE = '$scheduleC' AND CODE_DAY = '$updDay'";
            if ($mysqli->query($sqlUpdateBreakTime2)) {
                $message = 'Horario actualizado con éxito';
                $icon = 'success';
            } else {
                echo $sqlUpdateBreakTime2;
                $message = 'Error actualizando horario';
                $icon = 'error';
            }
        }
    }
}

$alert = '
<div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>Aún no tiene Registros en esta base</div>
</div>';


$sqlSchedule = "SELECT * FROM code_schedule WHERE SCHEDULE != ''";
$resultSchedule = $mysqli->query($sqlSchedule);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jornadas</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <header>
        <div class="px-3 py-2 border-bottom">
            <div class="px-3 mb-3">
                <div class="container d-flex flex-wrap justify-content-end">
                    <div class="text-end">
                        <a href="process/insertSchedule.php" type="button" class="btn btn-primary btn-sm">
                            Nuevo Horario &nbsp; <i class="bi bi-clock-fill"></i>
                        </a>
                        <!--Carga Masiva de Layout-->
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalSchedules">
                            Carga Archivo Jornada &nbsp; <i class="bi bi-upload"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalDaytrip">
                            Carga Archivo Grupo Jornda &nbsp; <i class="bi bi-upload"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalGroups">
                            Carga Archivo Grupos &nbsp; <i class="bi bi-upload"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <h4 class="my-3">Horarios</h4>

    <div class="container"> <!--Div Principal-->
        <?php
        if ($resultSchedule->num_rows > 0) {
        ?>
            <table id="myTable" class="table table-hover table-bordered table-sm" style="font-size: 13px;">
                <thead class="text-center">
                    <tr>
                        <th scope="col" style="width: 5%" class="text-white table-primary">Clave</th>
                        <th scope="col" style="width: 25%" class="text-white table-primary">Jornada</th>
                        <th scope="col" style="width: 50%" class="text-white table-primary">Horario</th>
                        <th scope="col" style="width: 20%" class="text-white table-primary"></th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    while ($rowSchedule = $resultSchedule->fetch_assoc()) {
                        $scheduleCode = $rowSchedule['CODE_NOM'];
                        $scheduleName = $rowSchedule['DAYTRIP'];
                        $scheduleData = $rowSchedule['SCHEDULE'];
                    ?>

                        <tr>
                            <td><?php echo $scheduleCode ?></td>
                            <td><?php echo $scheduleName ?></td>
                            <td><?php echo $scheduleData ?></td>
                            <td>
                                <div class="dropstart">
                                    <a href="process/updateSchedule.php?id=<?php echo $scheduleCode ?>" class="btn btn-warning btn-sm" type="button">
                                        Modificar
                                    </a>
                                    <a href="process/deleteSchedule.php?id=<?php echo $scheduleCode ?>" class="btn btn-danger btn-sm" type="button">
                                        Eliminar
                                    </a>
                                    <ul class="dropdown-menu" style="width: 40rem;">
                                        <li class="dropdown-item text-center">
                                            <h6>Modificar Horario</h6>
                                        </li>
                                        <li class="dropdown-item text-center">

                                            <h6> <?php echo $scheduleName ?></h6>
                                        </li>
                                        <li class="px-3 text-center">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                                                        <input hidden type="text" name="send" value="2">
                                                        <input hidden type="text" name="schedule2" value="<?php echo $scheduleCode ?>">
                                                        <?php
                                                        for ($dayCode = 0; $dayCode <= 6; $dayCode++) {

                                                            $sqlDays = "SELECT * FROM code_days WHERE CODE_DAY = '$dayCode'";
                                                            $resultDays = $mysqli->query($sqlDays);
                                                            if ($resultDays->num_rows > 0) {
                                                                while ($rowDays = $resultDays->fetch_assoc()) {
                                                                    $nameDay = $rowDays['NAME_DAY'];

                                                                    $sqlScheduleDetail = "SELECT DISTINCT TIME_START, OUT_TIME, MIN_TIME_START, DELAY_TIME_START, AUSENCE_TIME, LOCK_IN_TIME, MIN_TIME_OUT FROM admin_schedules WHERE CODE_SCHEDULE = '$scheduleCode' AND CODE_DAY = '$dayCode'";
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
                                                                                    <input type="time" name="schedule[<?php echo $dayCode ?>][beforeStart]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeBefore)) ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                                                                    <input type="time" name="schedule[<?php echo $dayCode ?>][start_time]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeStart)) ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <label for="inputEmail4" class="form-label">Registro Retardo</label>
                                                                                    <input type="time" name="schedule[<?php echo $dayCode ?>][delayTime]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeDelay)) ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <label for="inputEmail4" class="form-label">Registro Falta</label>
                                                                                    <input type="time" name="schedule[<?php echo $dayCode ?>][absenceTime]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeAbsence)) ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <label for="inputEmail4" class="form-label">Cierre Asistencia</label>
                                                                                    <input type="time" name="schedule[<?php echo $dayCode ?>][lockTome]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeLock)) ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <label for="inputEmail4" class="form-label">Salida Previa</label>
                                                                                    <input type="time" name="schedule[<?php echo $dayCode ?>][outBefore]" class="form-control form-control-sm" value="<?php echo date('H:i:s', strtotime($timeBeforeOut)) ?>">
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
                                                            <td><button type="submit" class="btn btn-lg btn-warning">Actualizar</button></td>
                                                        </tr>
                                                    </form>
                                                </tbody>
                                            </table>
                                        </li>
                                    </ul>
                                </div>
                            </td>
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
        ?>
    </div> <!--Cierre Div principal-->

    <!-- Modal Carga Layouts Jornada-->
    <div class="modal fade" id="exampleModalSchedules" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Jornadas Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadSchedules.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <input type="file" name="archivo_xlsx" accept=".csv" class="form-control" id="inputGroupFile01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Carga Layouts Grupo Jornada-->
    <div class="modal fade" id="exampleModalDaytrip" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Grupo Jornada Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadDaytrip.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <input type="file" name="archivo_xlsx" accept=".csv" class="form-control" id="inputGroupFile01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Carga Layouts Grupos-->
    <div class="modal fade" id="exampleModalGroups" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Excel Grupos Horarios Nom2001</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="csvLoads/loadGroups.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <input type="file" name="archivo_xlsx" accept=".csv" class="form-control" id="inputGroupFile01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php
    if ($send == '1') {
        //echo $sqlUpdateTolerances;
    ?>
        <script type="text/javascript">
            swal({
                title: "Generar Tiempos de Comida",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "schedules.php?id=<?php echo $user_active ?>";
            });
        </script>
    <?php
    } elseif ($send == '2') {
    ?>
        <script type="text/javascript">
            swal({
                title: "Horario Actualizado con éxito",
                text: "<?php echo $message; ?>",
                icon: "<?php echo $icon ?>",
                button: "Volver",
            }).then(function() {
                window.location = "schedules.php?id=<?php echo $user_active ?>";
            });
        </script>
    <?php
    }
    ?>

    <script>
        const dropdownElementList = document.querySelectorAll('.dropdown-toggle')
        const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl))
    </script>

</body>

</html>