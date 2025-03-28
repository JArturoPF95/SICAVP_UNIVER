<?php

date_default_timezone_set('America/Mexico_City');
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
}

require_once '../logic/conn.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <script src="../../sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../../sweetalert2.min.css">
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
</head>

<body>

    <?php
    $time_min = '';
    $payrollPeriodID = '';
    $valTime = date('H:i:s');
    $time_lock_check = '';
    $time_out_min  = '';
    $time_in = '';
    $time_out = '';
    $time_delay = '';
    $time_max = '';

    //Buttons status
    $inStatus = 'btn btn-success';
    $outStatus = 'btn btn-danger';
    $justifyStatus = 'hidden';


    require_once 'attendance/process/query_attendance.php';

    $sql_get_incidence;
    $result_incidenceButton = $mysqli->query($sql_get_incidence);
    if ($result_incidenceButton->num_rows > 0) {
        while ($row_IB = $result_incidenceButton->fetch_assoc()) {
            $time_min = $row_IB['MIN_TIME_START'];
            $time_max = $row_IB['AUSENCE_TIME'];
            $time_out_min = $row_IB['MIN_TIME_OUT'];
            $time_lock_check = $row_IB['LOCK_IN_TIME'];
            $time_in = $row_IB['TIME_START'];
            $time_out = $row_IB['OUT_TIME'];
            $time_delay = $row_IB['DELAY_TIME_START'];
        }
    }

    $sql_attendance_today;
    $result_attendance_today = $mysqli->query($sql_attendance_today);
    if ($result_attendance_today->num_rows > 0) {
        while ($row = $result_attendance_today->fetch_assoc()) {
            $attendance_today_date = $row['ATTENDANCE_DATE'];
            $attendance_today_checkin = $row['CHECKIN'];
            $attendance_today_checkout = $row['CHECKOUT'];
            $attendance_today_day = $row['NAME_DAY'];
            $attendance_today_tinc = $row['TINC'];
            $attendance_today_justify = $row['JUSTIFY'];
            $attendance_today = $row['CHECKS'];

            /** Generando estatus para el botón de capturar entrada */
            if ($attendance_today_checkin == null || $attendance_today_checkin == '') { //Si no se ha capturado la entrada
                if ($time_min <= $valTime && $time_lock_check >= $valTime) { //Si la hora actual es mayor o igual a la hora de entrada mínima y menor a la hora del cierre de captura
                    $inStatus = 'btn btn-success';
                } else { 
                    $inStatus = 'btn btn-outline-secondary disabled';
                }
            } else { //Si ya se capturó la entrada
                $inStatus = 'btn btn-outline-secondary disabled';
            }

            /** Generando estatus para el botón de justificar */
            if ($time_delay <= $valTime && $time_out_min >= $valTime) { //Entramos entre el tiempo de retardo o el tiempo de salida mínima
                if ($attendance_today_checkin == null || $attendance_today_checkin == '') { //Si no se ha capturado la entrada
                    $justifyStatus = '
                        <button type="submit" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $user_active . '">Justificar</button>';
                } else {
                    if ($attendance_today_tinc == 01 || $attendance_today_tinc == 02 || $attendance_today_tinc == 1 || $attendance_today_tinc == 2 || $attendance_today_tinc == 22) {
                        if ($attendance_today_justify == '' || $attendance_today_justify == null) {
                            $justifyStatus = '
                                <button type="submit" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $user_active . '">Justificar</button>';
                        } else {
                            $justifyStatus = '';
                        }
                        
                    } else {
                        $justifyStatus = '';
                    }
                }
            } else {
                $justifyStatus = '';
            }


            /** Generando estatus para el botón de capturar salida */
            if ($time_out_min <= $valTime) { //Si la hora actual es mayor o igual a la hora de salida mínima
                if ($attendance_today_checkout == null or $attendance_today_checkout == '') {
                    $outStatus = 'btn btn-danger';
                } else {
                    $outStatus = 'btn btn-outline-secondary disabled';
                }
            } else {
                $outStatus = 'btn btn-outline-secondary disabled';
            }



    ?>

            <div class="card border-light mb-3">
                <div class="card-header border-light mb-3 text-center h4">
                    Bienvenido...
                </div>
                <?php
                //Validamos las solicitudes que tiene el supervisor

                if ($user_access == '2') {
                ?>
                    <div class="btn-group" role="group" aria-label="Basic example" style="width: 18rem;">
                        <?php
                        $sqlJustify = "SELECT COUNT(EMP.ID_NOM) REQUESTS FROM employed EMP
INNER JOIN admin_attendance ADA ON EMP.ID_NOM = ADA.NOM_ID
WHERE EMP.SUPERVISOR_ID = '$user_active' AND ADA.JUSTIFY = 'P'";
                        $resultJustify = $mysqli->query($sqlJustify);
                        if ($resultJustify->num_rows > 0) {
                            while ($rowJustify = $resultJustify->fetch_assoc()) {
                                $requestJustify = $rowJustify['REQUESTS'];
                        ?>
                                <a href="reports/justify_delays.php" type="button" class="btn btn-primary position-relative">
                                    Justificaciones
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $requestJustify ?>
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                </a>
                                &nbsp; &nbsp;
                            <?php
                            }
                        }
                        $sqlVacations = "SELECT COUNT(requestId) VACATIONS FROM vacation_request WHERE IMMEDIATE_BOSS = '$user_active' AND AUTHORIZATION_FLAG = '0' AND DAYS_REQUESTED != '0'";
                        $resultVacations = $mysqli->query($sqlVacations);
                        if ($resultVacations->num_rows > 0) {
                            while ($rowVacations = $resultVacations->fetch_assoc()) {
                                $requestVacations = $rowVacations['VACATIONS'];
                            ?>
                                <a href="vacations/authorizations.php" type="button" class="btn btn-primary position-relative">
                                    Vacaciones
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $requestVacations ?>
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                </a>
                        <?php
                            }
                        }

                        ?>
                    </div>
                <?php
                }
                ?>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Hora de Entrada (<?php echo date('h:i A',strtotime($time_in)); ?>)</h5>
                                        <p class="card-text h4"><?php if($attendance_today_checkin != null or $attendance_today_checkin != ''){echo "Registro: ";}else{echo '<br>';} echo $attendance_today_checkin; ?></p>
                                        <a href="attendance/process/check.php?check=1" class="<?php echo $inStatus; ?>">Capturar Entrada</a>
                                        &nbsp;&nbsp; <?php echo $justifyStatus; ?> 
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Hora de Salida (<?php echo date('h:i A',strtotime($time_out)); ?>)</h5>
                                        <p class="card-text h4"><?php if($attendance_today_checkout != NULL or $attendance_today_checkout != ''){echo "Registro: ";}else{echo '<br>';} echo $attendance_today_checkout; ?></p>
                                        <a href="attendance/process/check.php?check=2" class="<?php echo $outStatus ?>">Capturar Salida</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="attendance/process/justify.php" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Solicitar Justificación</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div hidden class="mb-3">
                            <label for="recipient-name" class="col-form-label">Empleado:</label>
                            <input type="text" disabled name="user" class="form-control" id="recipient-name" value="<?php $user_active ?>">
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Justificación:</label>
                            <textarea class="form-control" name="comment" id="message-text"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Solicitar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        const exampleModal = document.getElementById('exampleModal')
        if (exampleModal) {
            exampleModal.addEventListener('show.bs.modal', event => {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                const recipient = button.getAttribute('data-bs-whatever')
                // If necessary, you could initiate an Ajax request here
                // and then do the updating in a callback.

                // Update the modal's content.
                const modalTitle = exampleModal.querySelector('.modal-title')
                const modalBodyInput = exampleModal.querySelector('.modal-body input')

                //modalTitle.textContent = `New message to ${recipient}`
                modalBodyInput.value = recipient
            })
        }
    </script>

</body>

</html>