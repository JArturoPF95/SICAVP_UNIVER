<?php

use Masterminds\HTML5\Elements;

require '../logic/conn.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.php');
    exit();
} else {
    $user_name = $_SESSION['user_name'];
    $user_active = $_SESSION['usuario'];
    $user_payroll = $_SESSION['payroll'];
    $user_access = $_SESSION['access_lev'];
    $user_sesion = $_SESSION['session'];
    $user_city = $_SESSION['city'];
}

//echo $user_city . ' ' . $user_sesion;
date_default_timezone_set('America/Mexico_City');
$codeDay = date('w');
$selectedDay = '';
$today = date('Y-m-d');
$send_flag = 0;
$payrollCode = '';
$time = date('H:i:s');
$pkSesion = '0';
$attendance = '';

echo $user_sesion . ' - ' . $user_city . '<br>';

$getSesionPK = "SELECT DISTINCT PK FROM code_sesion WHERE CODE_CITY = '$user_city' AND CODE_SESION_NOM = '$user_sesion'";
$resultSesionPK = $mysqli->query($getSesionPK);
if ($resultSesionPK->num_rows > 0) {
    while ($rowSesionPK = $resultSesionPK->fetch_assoc()) {
        $pkSesion = $rowSesionPK['PK'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['program'])) {
        $program = $_POST['program'];
    } else {
        $program = '-';
    }
    if (isset($_POST['classDate'])) {
        $selectedDate = $_POST['classDate'];
        $selectedDay = date('w', strtotime($selectedDate));
    } else {
        $selectedDate = $today;
        $selectedDay = date('w', strtotime($today));
    }
    $send_flag = $_POST['send'];

    //echo $program . ' ' . $selectedDate . ' ' . $selectedDay;
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['p'])) {
        $program = $_GET['p'];
    } else {
        $program = '-';
    }
    if (isset($_GET['sd'])) {
        $selectedDate = $_GET['sd'];
        $selectedDay = date('w', strtotime($selectedDate));
    } else {
        $selectedDate = $today;
        $selectedDay = date('w', strtotime($today));
    }
    if (isset($_GET['sn'])) {
        $send_flag = $_GET['sn'];
    } else {
        $send_flag = '-';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo1.jpg" type="image/x-icon">
    <title>Clases del Día</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <!--script src="../../static/js/select2.min.js"></script-->
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <!--link rel="stylesheet" href="../../static/css/select2.min.css"-->
    <link href="../../static/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="../../static/js/select2.min.js"></script>
</head>

<body>

    <div class="container-fluid">

        <form class="row g-3 my-1" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <input hidden type="text" name="send" value="1">
            <div class="col-auto">
                <select class="form-select" aria-label="Default select example" name="program">
                    <option selected disabled>Seleccionar Programa</option>
                    <?php
                    $sqlProgram = "SELECT DISTINCT PROGRAM, PROGRAM_DESC FROM academic_schedules";
                    $resultProgram = $mysqli->query($sqlProgram);
                    if ($resultProgram->num_rows > 0) {
                        while ($rowProgram = $resultProgram->fetch_assoc()) {
                    ?>
                            <option value="<?php echo $rowProgram['PROGRAM'] ?>"><?php echo $rowProgram['PROGRAM_DESC'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <input type="date" name="classDate" id="" value="<?php echo $today ?>" class="form-control" aria-describedby="helpId" min="<?php echo $today ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Seleccionar</button>
            </div>
            <div class="col-auto">
                <?php
                $sqlJustify = "SELECT COUNT(AAT.AttendanceId) REQUESTS FROM academic_attendance AAT WHERE AAT.JUSTIFY = 'P'";
                $resultJustify = $mysqli->query($sqlJustify);
                if ($resultJustify->num_rows > 0) {
                    while ($rowJustify = $resultJustify->fetch_assoc()) {
                        $requestJustify = $rowJustify['REQUESTS'];
                ?>
                        <a href="reports/events.php" type="button" class="btn btn-primary position-relative">
                            Justificaciones
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $requestJustify ?>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </a>
                <?php
                    }
                }
                ?>
            </div>
            <div class="col-auto">
                <form class="col-4 mx-2 my-2" role="search">
                    <input type="search" id="searchInput" class="form-control" onkeyup="searchTable()" placeholder="Buscar" aria-label="Search">
                </form>

            </div>
        </form>
        <div class="row">

            <?php
            if ($send_flag == 1) {

                require_once 'reports/process/query_reports.php';

                $sql_get_teachers;
                $result_teachers = $mysqli->query($sql_get_teachers);

                //echo $sql_get_teachers;
                if ($result_teachers->num_rows > 0) {
            ?>
                    <table class="table table-primary table-hover table-bordered table-sm" id="myTable" style="font-size: 10px;">
                        <thead class="text-white text-center">
                            <tr>
                                <th scope="col">Campus</th>
                                <th scope="col">Docente</th>
                                <th scope="col">Carrera</th>
                                <th scope="col">Aula</th>
                                <th scope="col">Materia</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Horario</th>
                                <th scope="col" colspan="2">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="table-light">
                            <?php
                            while ($rowTeachers = $result_teachers->fetch_assoc()) {
                                $teacher_id = $rowTeachers['PK'];
                                $teacher_csesion = $rowTeachers['CODE_SESSION'];
                                $teacher_sesion = $rowTeachers['ACADEMIC_SESSION'];
                                $teacher_idPwC = $rowTeachers['PERSON_CODE_ID'];
                                $teacher_name = $rowTeachers['DOC_NAME'];
                                $teacher_program = $rowTeachers['PROGRAM'];
                                $teacher_programDesc = $rowTeachers['PROGRAM_DESC'];
                                $teacher_curriculum = $rowTeachers['CURRICULUM'];
                                $teacher_formalTitle = $rowTeachers['FORMAL_TITLE'];
                                $teacher_room = $rowTeachers['ROOM_NAME'];
                                $teacher_event = $rowTeachers['PUBLICATION_NAME_1'];
                                $teacher_startClass = $rowTeachers['START_CLASS'];
                                $teacher_endClass = $rowTeachers['END_CLASS'];
                                $teacher_delay = $rowTeachers['DELAY_CLASS'];
                                $teacher_maxTime = $rowTeachers['MAX_DELAY_CLASS'];
                                $teacher_minTimeEnd = $rowTeachers['MIN_END_CLASS'];
                                $teacher_attendanceIn = $rowTeachers['ATTENDANCE_START_CLASS'];
                                $teacher_attendanceEnd = $rowTeachers['ATTENDANCE_END_CLASS'];
                                $teacher_attendanceStatus = $rowTeachers['ATTENDANCE_STATUS'];
                                $teacher_attendanceCodeStatus = $rowTeachers['ATTENDANCE_TINC'];
                                $teacher_attendanceClass = $rowTeachers['TEACHER_CLASS'];
                                $teacher_idSust = $rowTeachers['ID_SUSTITUTION'];

                                //Validamos estatus

                                //Validamos la fecha, si es igual a hoy valida entrada y estatus para suplir, si es mayor que hoy genera por default el botón suplir
                                if ($selectedDate === $today) {
                                    if ($teacher_attendanceIn === '') {

                                        //Aún no es horario de inicio de la clase
                                        if (date('H:i:s', strtotime($teacher_endClass)) < date('H:i:s', strtotime($time))) {
                                            $deleteSub = '';
                                            $attendance = '<button type="button" class="btn btn-sm btn-danger dropdown-toggle" style="width: 80px; font-size: 11px;" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Sin Captura
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li style="font-size: 11px;"><a class="dropdown-item" href="reports/process/checkNoCapture.php?id=' . $teacher_id . '&in=fa&pr=' . $program . '&sf=1">Falta Injustificada</a></li>
                                                                <li style="font-size: 11px;"><a class="dropdown-item" href="reports/process/checkNoCapture.php?id=' . $teacher_id . '&in=re&pr=' . $program . '&sf=1">Retardo</a></li>
                                                                <li style="font-size: 11px;"><a class="dropdown-item" href="reports/process/checkNoCapture.php?id=' . $teacher_id . '&in=as&pr=' . $program . '&sf=1">Asistencia</a></li>
                                                            </ul>';

                                            //Ya es horario de clase, pero no capturó
                                        } else {
                                            $attendance = '<button type="button" class="btn btn-sm btn-warning" style="width: 80px; font-size: 11px;" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $teacher_id . '">Suplente</button>';
                                            $deleteSub = '';
                                        }

                                        //Ya se capturó registro
                                    } else {

                                        //No se capturó un docente suplente
                                        if ($teacher_attendanceClass === '') {
                                            $attendance = $teacher_attendanceStatus . ' - ' . $teacher_attendanceIn;
                                            $deleteSub = '';

                                            //Se capturó un docente suplente
                                        } elseif ($teacher_attendanceClass !== '') {
                                            $attendance = 'Suplido por: <br>' . $teacher_attendanceClass;
                                            $deleteSub = '<a type="button" class="btn btn-sm btn btn-link link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" style="width: 80px; font-size: 11px;" href="attendance/process/deleteSup.php?id='.$teacher_idSust.'&pr='.$program.'&dt='.$selectedDate.'&s='.$send_flag.'">Descartar Suplente</a>';
                                        }
                                    }
                                } else { 
                                    //No se capturó un docente suplente
                                    if ($teacher_attendanceClass === '') {
                                        $attendance = '<button type="button" class="btn btn-sm btn-warning" style="width: 80px; font-size: 11px;" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="' . $teacher_id . '">Suplente</button>';
                                        $deleteSub = '';

                                        //Se capturó un docente suplente
                                    } elseif ($teacher_attendanceClass !== '') {
                                        $attendance = 'Suplido por: <br>' . $teacher_attendanceClass;
                                        $deleteSub = '<a type="button" class="btn btn-sm btn btn-link link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" style="width: 80px; font-size: 11px;" href="attendance/process/deleteSup.php?id='.$teacher_idSust.'&pr='.$program.'&dt='.$selectedDate.'&s='.$send_flag.'">Descartar Suplente</a>';
                                    }
                                }

                            ?>
                                <tr class="text-center">
                                    <td hidden><input type="text" class="form-control" id="validationCustom01" name="id" value="<?php echo $teacher_id ?>"></td>
                                    <td><?php echo $teacher_sesion ?></td>
                                    <td><?php echo $teacher_idPwC . ' - ' . $teacher_name ?></td>
                                    <td><?php echo $teacher_formalTitle ?></td>
                                    <td><?php echo $teacher_room ?></td>
                                    <td><?php echo $teacher_event ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($selectedDate)) ?></td>
                                    <td><?php echo date('H:i:s', strtotime($teacher_startClass)) ?> - <?php echo date('H:i:s', strtotime($teacher_endClass)) ?></td>
                                    <td><?php echo $attendance . '<br>' . $deleteSub; ?></td>

                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                } else {
                ?>
                    <div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i> &nbsp; &nbsp;
                        <div>
                            <h4>Sin Registros. Favor de seleccionar otra opción</h4>
                        </div>
                    </div>
            <?php
                }
            }
            ?>

        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="font-size: 13px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="attendance/process/check.php">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Asignar Suplente a la Clase</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div hidden class="mb-3">
                            <label for="recipient-name" class="col-form-label">Recipient:</label>
                            <input type="text" name="id_class" class="form-control" id="recipient-name">
                        </div>
                        <div class="mb-3">
                            <input hidden type="text" name="prog" value="<?php echo $program ?>">
                            <input hidden type="text" name="check" value="3">
                            <input hidden type="text" name="send" value="1">
                            <input hidden type="text" name="selDat" value="<?php echo $selectedDate ?>">
                            <label for="recipient-name" class="col-form-label">Suplente:</label>
                            <select class="form-select js-example-basic-single" aria-label="Default select example" name="id_teacher" style="font-size: 13px;">
                                <!--option selected disabled>Seleccionar Docente</option-->
                                <?php
                                $sqlValDoc = "SELECT DISTINCT CSA.CODE_NOM, ASH.NAME, ASH.PERSON_CODE_ID 
                                        FROM academic_schedules ASH 
                                        LEFT OUTER JOIN code_sesion_academic CSA ON CSA.CODE_VALUE_KEY = ASH.ACADEMIC_SESSION
                                        WHERE CSA.CODE_NOM = '$pkSesion';";
                                $resultValDoc = $mysqli->query($sqlValDoc);
                                if ($resultValDoc->num_rows > 0) {
                                    while ($rowValDoc = $resultValDoc->fetch_assoc()) {
                                        $idDoc = $rowValDoc['PERSON_CODE_ID'];
                                        $nameDoc = $rowValDoc['NAME'];
                                ?>
                                        <option value="<?php echo $idDoc ?>"><?php echo $idDoc . ' - ' . $nameDoc ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <!--input type="text" name='id_teacher' class="form-control" id="recipient-name"-->
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Tema:</label>
                            <textarea class="form-control" name="summary" id="message-text"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            // Inicializa Select2 al cargar la página para evitar errores si el modal ya está abierto
            $('.js-example-basic-single').select2({
                dropdownParent: $('#exampleModal') // Esto es clave para los modales de Bootstrap
            });

            // Re-inicializa Select2 cada vez que se abra el modal
            $('#exampleModal').on('shown.bs.modal', function() {
                $('.js-example-basic-single').select2({
                    dropdownParent: $('#exampleModal') // Evita que el desplegable se corte
                });
            });
        });

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

        function searchTable() {
            // Obtiene el valor del input de búsqueda
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");

            // Itera sobre todas las filas y oculta las que no coincidan con el término de búsqueda
            for (i = 1; i < tr.length; i++) {
                // Itera sobre todas las celdas de la fila
                var found = false;
                for (td of tr[i].getElementsByTagName("td")) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                if (found) {
                    tr[i].style.display = ""; // Muestra la fila si coincide con el término de búsqueda
                } else {
                    tr[i].style.display = "none"; // Oculta la fila si no coincide
                }
            }

        }
    </script>

</body>

</html>