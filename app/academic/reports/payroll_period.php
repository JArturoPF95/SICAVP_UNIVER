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
    $user_sesion = $_SESSION['session'];
    $user_city = $_SESSION['city'];
}

$program = '';
$codeDay = '';
$today = '';
$selectedDay = '';
$payrollCode = '';
$pkSesion = '0';
$selectedDate = NULL;

$getSesionPK = "SELECT DISTINCT PK FROM code_sesion WHERE CODE_CITY = '$user_city' AND CODE_SESION_NOM = '$user_sesion'";
$resultSesionPK = $mysqli -> query($getSesionPK);
if ($resultSesionPK -> num_rows > 0) {
    while ($rowSesionPK = $resultSesionPK -> fetch_assoc()) {
        $pkSesion = $rowSesionPK['PK'];
    }
}

$send_flag = 0;
$year = date('Y');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $send_flag = $_POST['flag'];
    if (isset($_POST['payrollCode'])) {
        $payrollCode = $_POST['payrollCode'];
    } else {
        $payrollCode = '0';
    }

    require 'process/query_reports.php';

    $sqlTeachersReport;
    $resultTeacherReport = $mysqli->query($sqlTeachersReport);
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
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <h4 class="mb-3">Reporte Periodo de Nómina Docente</h4>
    <div class="container"> <!-- Div Principal-->
        <div class="row my-2">
            <form class="col-10 row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                <input hidden type="text" name="flag" id="" value="1">
                <div class="col-6">
                    <select class="form-select" id="specificSizeSelect" name="payrollCode">
                        <option selected disabled value="0">Seleccionar Periodo</option>
                        <?php
                        $sqlValPayrollPeriod = "SELECT * FROM payroll_period WHERE YEAR = '$year'";
                        $resultValPayrollPeriod = $mysqli->query($sqlValPayrollPeriod);
                        if ($resultValPayrollPeriod->num_rows > 0) {
                            while ($rowPayrollPeriod = $resultValPayrollPeriod->fetch_assoc()) {
                        ?>
                                <option value="<?php echo $rowPayrollPeriod['ID'] ?>"><?php echo $rowPayrollPeriod['CODE'] . ' ' . $rowPayrollPeriod['DESCRIPTION'] ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Seleccionar</button>
                </div>
                <div class="col-auto">
                    <a href="process/downloadPayrollPeriod.php?id=<?php echo $payrollCode ?>" type="button" class="btn btn-secondary">Descargar</a>
                </div>
            </form>
            <div class="col-2">        
                <form role="search">
                    <input type="search" id="searchInput" class="form-control" onkeyup="searchTable()" placeholder="Buscar" aria-label="Search">
                </form>
            </div>
        </div>
        <div class="row my-2">

            <?php

            if ($send_flag == 1) {
                if ($resultTeacherReport->num_rows > 0) {
            ?>
                    <table class="table table-hover table-bordered" id="myTable" style="font-size: 9px;">
                        <thead class="text-center table-primary">
                            <th class="text-white fw-bold">Fecha</th>
                            <th class="text-white fw-bold">Periodo</th>
                            <th class="text-white fw-bold">Campus</th>
                            <th class="text-white fw-bold">Día</th>
                            <th class="text-white fw-bold">ID Docente</th>
                            <th class="text-white fw-bold">Nombre</th>
                            <th class="text-white fw-bold">Programa</th>
                            <th class="text-white fw-bold">Curriculum</th>
                            <th class="text-white fw-bold">Tipo Materia</th>
                            <th class="text-white fw-bold">Grupo</th>
                            <th class="text-white fw-bold">Sección</th>
                            <th class="text-white fw-bold">Aula</th>
                            <th class="text-white fw-bold">Materia</th>
                            <th class="text-white fw-bold">Horario</th>
                            <th class="text-white fw-bold">Entrada</th>
                            <th class="text-white fw-bold">Salida</th>
                            <th class="text-white fw-bold">Estatus</th>
                        </thead>
                        <tbody>
                            <?php
                            while ($rowTeachersReport = $resultTeacherReport->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($rowTeachersReport['CALENDAR_DATE'])) ?></td>
                                    <td><?php echo $rowTeachersReport['ACADEMIC_TERM'] ?></td>
                                    <td><?php echo $rowTeachersReport['ACADEMIC_SESSION'] ?></td>
                                    <td><?php echo $rowTeachersReport['NAME_DAY'] ?></td>
                                    <td><?php echo $rowTeachersReport['PERSON_CODE_ID'] ?></td>
                                    <td><?php echo $rowTeachersReport['ACA_NAME'] ?></td>
                                    <td><?php echo $rowTeachersReport['PROGRAM_DESC'] ?></td>
                                    <td><?php echo $rowTeachersReport['FORMAL_TITLE'] ?></td>
                                    <td><?php echo $rowTeachersReport['GENERAL_ED'] ?></td>
                                    <td><?php echo $rowTeachersReport['SERIAL_ID'] ?></td>
                                    <td><?php echo $rowTeachersReport['SECTION'] ?></td>
                                    <td><?php echo $rowTeachersReport['ROOM_NAME'] ?></td>
                                    <td><?php echo $rowTeachersReport['PUBLICATION_NAME_1'] ?></td>
                                    <td><?php echo substr($rowTeachersReport['START_CLASS'], 0, 8) . ' - ' . substr($rowTeachersReport['END_CLASS'], 0, 8) ?></td>
                                    <td><?php echo $rowTeachersReport['CLASS_IN'] ?></td>
                                    <td><?php echo $rowTeachersReport['CLASS_OUT'] ?></td>
                                    <td>
                                        <?php
                                        if ($rowTeachersReport['TEACHER_CLASS'] == '') {
                                            echo $rowTeachersReport['CLASS_INCIDENCE'];
                                        } else {
                                            echo $rowTeachersReport['CLASS_INCIDENCE'] . ': ' . $rowTeachersReport['TEACHER_CLASS'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    echo '
<div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
<i class="bi bi-exclamation-circle-fill"></i>
<div>
<h4>No cuenta con docentes con horario registrados en sistema esa quincena</h4>
</div>
</div>';
                }
            } else {
                if ($payrollCode = '') {
                ?>
                    <div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                        <div>
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <h4>&nbsp; &nbsp; Favor de seleccionar otro Periodo</h4>
                        </div>
                    </div>
            <?php
                }
            }

            ?>
        </div>
    </div>

    <script>
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
    </script>
</body>

</html>