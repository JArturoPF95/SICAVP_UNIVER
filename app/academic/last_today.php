<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
    $user_so = $_SESSION['sistema_operativo'];
}

//Obtenemos IP
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
//$ip = file_get_contents('https://api.ipify.org'); //Otra opción para IP Pública -- Validar que se tenga activa esa librería
$device = $_SERVER['HTTP_USER_AGENT'];

date_default_timezone_set('America/Mexico_City');

$time = date('H:i:s');
$today = date('Y-m-d');
$payrollPeriodID = '';

//echo $time;

$button_CheckIn = '';
$button_CheckOut = '';
$button_justify = '';
$textAreaSummaryIn = '';

$valCond = '';

require_once '../logic/conn.php';

/** Validar el sistema operativo */
if ($user_so == 'Windows' || $user_so == 'Mac OS X' || $user_so == 'MacOS' || $user_so == 'ChromeOS') {
    /** Validamos acceso por IP */
    $valMyIP = "SELECT * FROM code_ip WHERE IP = '$ip'";
    $resultValIP = $mysqli->query($valMyIP);
    if ($resultValIP->num_rows > 0) {
        while ($rowValIP = $resultValIP->fetch_assoc()) {
            $flagClinic = '0';
        }
    } else {
        $flagClinic = '1';
    }
} else {
    $flagClinic = '1';
}
require_once 'attendance/process/query.php';
//echo $flagClinic;


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
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body id="contenido">

    <?php

    $sql_academic_check;
    $result_attendance_today = $mysqli->query($sql_academic_check);

    //echo $sql_academic_check;
    ?>
    <div class="card border-light mb-3">
        <div class="card-header border-light mb-3 text-center h4">
            Bienvenido...
        </div>
        <?php
        if ($result_attendance_today->num_rows > 0) {
            while ($row_check_event = $result_attendance_today->fetch_assoc()) {
                $check_event_classID = $row_check_event['PK'];
                $check_event_id = $row_check_event['ID'];
                $check_event_day = $row_check_event['DY'];
                $check_event_term = $row_check_event['ACADEMIC_TERM'];
                $check_event_session = $row_check_event['SESION'];
                $check_event_program = $row_check_event['PROGRAM'];
                $check_event_curriculum = $row_check_event['CURRICULUM'];
                $check_event_programDesc = $row_check_event['PROGRAM_DESC'];
                $check_event_FormalTitle = $row_check_event['FORMAL_TITLE'];
                $check_event_roomId = $row_check_event['ROOM'];
                $check_event_roomName = $row_check_event['ROOM_NAME'];
                $check_event_eventId = $row_check_event['EVENT_ID'];
                $check_event_eventName = $row_check_event['MAT'];
                $check_event_atteDate = $row_check_event['ATTENDANCE_DATE'];
                $check_event_checkIn = $row_check_event['CHECK_IN'];
                $check_event_checkOut = $row_check_event['CHECK_OUT'];
                $check_event_before = $row_check_event['MAX_BEFORE_CLASS'];
                $check_event_start = $row_check_event['START_CLASS'];
                $check_event_delay = $row_check_event['DELAY_CLASS'];
                $check_event_absence = $row_check_event['MAX_DELAY_CLASS'];
                $check_event_min_end = $row_check_event['MIN_END_CLASS'];
                $check_event_end = $row_check_event['END_CLASS'];
                $check_event_summary = $row_check_event['CLASS_SUMMARY'];
                $check_event_tinc = $row_check_event['TINC'];
                $check_event_justify = $row_check_event['JUSTIFY'];

                //Si no tiene asistencia ni salida Registrada
                if ($check_event_checkIn == '' and $check_event_checkOut == '') {
                    //$valCond = 'Sin Entrada Sin Salida';
                    if ($time < $check_event_before) {
                        //$valCond = 'Check Antes de la hora permitida';
                        $button_CheckIn = '<button class="btn btn-outline-success disabled" type="submit">Capturar Entrada</button>';
                        $button_CheckOut = '<button class="btn btn-outline-danger disabled" type="submit">Capturar Salida</button>';
                        $textAreaSummaryIn = '';
                        $button_justify = '';
                    } elseif ($time >= $check_event_before and $time <= $check_event_delay) {
                        //$valCond = 'Check con asistencia correcta';
                        $button_CheckIn = '<button class="btn btn-success" type="submit">Capturar Entrada</button>';
                        $button_CheckOut = '<button class="btn btn-outline-danger disabled" type="submit">Capturar Salida</button>';
                        $textAreaSummaryIn = '<input type="textarea" placeholder="Tema de la Clase" class="form-control my-2" id="validationCustom01" name="summary" value="' . $check_event_summary . '" required>';
                        $button_justify = '';
                    } elseif ($time > $check_event_delay and $time <= $check_event_absence) {
                        //$valCond = 'Check con reterdo';
                        $button_CheckIn = '<button class="btn btn-success" type="submit">Capturar Entrada</button>';
                        $button_CheckOut = '<button class="btn btn-outline-danger disabled" type="submit">Capturar Salida</button>';
                        $textAreaSummaryIn = '<input type="textarea" placeholder="Tema de la Clase" class="form-control my-2" id="validationCustom01" name="summary" value="' . $check_event_summary . '" required>';
                        $button_justify = '&nbsp;&nbsp;<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $check_event_classID . '">Justificar</button>';
                    } elseif ($time > $check_event_absence) {
                        //$valCond = 'Se bloquea el check';
                        $button_CheckIn = '<button class="btn btn-outline-success disabled" type="submit">Capturar Entrada</button>';
                        $button_CheckOut = '<button class="btn btn-outline-danger disabled" type="submit">Capturar Salida</button>';
                        $textAreaSummaryIn = '';
                        $button_justify = '&nbsp;&nbsp;<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $check_event_classID . '">Justificar</button>';
                    }
                    //Entrada pero sin salida Registrada
                } elseif ($check_event_checkIn != '' and $check_event_checkOut == '') {
                    //$valCond = 'Entrada Sin Salida';
                    if ($time < $check_event_min_end) {
                        //$valCond = 'Salida antes del cierre permitidio';
                        $button_CheckIn = '<button class="btn btn-outline-success disabled" type="submit">Capturar Entrada</button>';
                        $button_CheckOut = '<button class="btn btn-outline-danger disabled" type="submit">Capturar Salida</button>';
                        $textAreaSummaryIn = '';
                        if ($check_event_tinc == 21 or $check_event_justify != '') {
                            $button_justify = '';
                        } else {
                            $button_justify = '&nbsp;&nbsp;<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $check_event_classID . '">Justificar</button>';
                        }
                    } elseif ($time >= $check_event_min_end) {
                        //$valCond = 'Cierre de clase en tiempo permitido';
                        $button_CheckIn = '<button class="btn btn-outline-success disabled" type="submit">Capturar Entrada</button>';
                        $button_CheckOut = '<button class="btn btn-danger" type="submit">Capturar Salida</button>';
                        $textAreaSummaryIn = '';
                        if ($check_event_tinc == 21 or $check_event_justify != '') {
                            $button_justify = '';
                        } else {
                            $button_justify = '&nbsp;&nbsp;<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $check_event_classID . '">Justificar</button>';
                        }
                    }
                    //Entrada y Salida Registradas
                } else {
                    //$valCond = 'Entrada y Salida checados';
                    $button_CheckIn = '<button class="btn btn-outline-success disabled" type="submit">Capturar Entrada</button>';
                    $button_CheckOut = '<button class="btn btn-outline-danger disabled" type="submit">Capturar Salida</button>';
                    $textAreaSummaryIn = '';
                    $button_justify = '';
                }



        ?>
                <div class="card-body text-center">
                    <h4 class="card-title"><?php echo $check_event_eventName . ' - ' . substr($check_event_start, 0, 8) . ' a ' . substr($check_event_end, 0, 8) ?></h4>
                    <h5 class="card-text">Carrera: <?php echo $check_event_FormalTitle . ' - Aula: ' . $check_event_roomName ?></h5>
                    <h6 class="card-text">Tema visto: <?php echo $check_event_summary ?></h4>
                        <!--p class="card-text">Vals: <?php echo 'Min: ' . $check_event_before . ' Ini ' . $check_event_start . ' Del ' . $check_event_delay . ' Abs ' . $check_event_absence . ' MinO ' . $check_event_min_end . ' End ' . $check_event_end . ' Act ' . $time ?></p-->
                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <form action="attendance/process/check.php" method="post">
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="text" hidden class="form-control" id="validationCustom01" name="id_class" value="<?php echo $check_event_classID ?>">
                                            <input type="text" hidden class="form-control" id="validationCustom01" name="check" value="1">
                                            <h4 class="card-title">Hora de Entrada</h5>
                                                <p class="card-text h4"><?php echo substr($check_event_checkIn, 0, 8) ?></p>
                                                <?php echo $textAreaSummaryIn; ?>
                                                <?php echo $button_CheckIn . $button_justify; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <form action="attendance/process/check.php" method="post">
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="text" hidden class="form-control" id="validationCustom01" name="id_class" value="<?php echo $check_event_classID ?>">
                                            <input type="text" hidden class="form-control" id="validationCustom01" name="check" value="2">
                                            <h4 class="card-title">Hora de Salida</h5>
                                                <p class="card-text h4"><?php echo substr($check_event_checkOut, 0, 8) ?></p>
                                                <?php echo $button_CheckOut; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    Usted no tiene clases registradas el día de hoy
                </div>
            </div>
        <?php
        }
        ?>

    </div>

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
                            <label for="recipient-name" class="col-form-label">Clase:</label>
                            <input type="text" name="classID" class="form-control" id="recipient-name" value="<?php echo $check_event_classID ?>">
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

        function actualizarContenido() {
            fetch("last_today.php") 
                .then(response => response.text())
                .then(data => {
                    document.getElementById("contenido").innerHTML = data;
                })
                .catch(error => console.error("Error:", error));
        }

        // Llamar a la función cada 5 segundos sin recargar la página
        setInterval(actualizarContenido, 30000);

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