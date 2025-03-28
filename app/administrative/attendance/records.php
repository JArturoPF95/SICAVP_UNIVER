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

$year2 = date('Y');
$today = date('Y-m-d');
$payrollPeriodID = '';
$flag_send = 0;
$flagJust = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['forms'])) {
        if ($_POST['forms'] == 'formView') {
            $_SESSION['payrollPeriodID'] = $_POST['payrollPeriodID']; // Guardamos la selección en sesión
            $_SESSION['flag_send'] = $_POST['flag'];

            $payrollPeriodID = $_SESSION['payrollPeriodID'];
            $flag_send = $_SESSION['flag_send'];

            require 'process/query_attendance.php';
            $result_attendance_records = $mysqli->query($sql_attendance_records);
        } elseif ($_POST['forms'] == 'justifForm') {
            $dateJust = $_POST['Date'];
            $commentJust = $_POST['comment'];
            $idJust = $_POST['idNom'];
            $idAtt = $_POST['idAtt'];

            $codeDay = date('w', strtotime($dateJust));

            if ($idAtt == '') {
                $sql_insertJustify = "INSERT INTO admin_attendance 
                (NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG, COMMENTS, JUSTIFY) 
                VALUES ('$idJust','$codeDay','$dateJust','12:00:00', '01', 1, 'S', '$commentJust','P');";
                $mysqli->query($sql_insertJustify);
            } else {
                $sql_UpdateJustif = "UPDATE admin_attendance SET JUSTIFY = 'P', COMMENTS = '$commentJust' WHERE AttendanceId = '$idAtt'";
                $mysqli->query($sql_UpdateJustif);
            }

            // Redirigir a la misma página para evitar reenvíos y mantener datos de sesión
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

// Recuperar datos de sesión si existen
$payrollPeriodID = $_SESSION['payrollPeriodID'] ?? '';
$flag_send = $_SESSION['flag_send'] ?? 0;
require 'process/query_attendance.php';
$result_attendance_records = $mysqli->query($sql_attendance_records);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <div class="container-fluid" style="height: 100%; width: 100%;">
        <h4 class="mb-3">Historial de Asistencias</h4>
        <!--Formulario-->
        <div class="row my-2">
            <div class="col">
                <form class="row gx-3 gy-2 align-items-center" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <input hidden type="text" name="flag" id="" value="1">
                    <div class="col-sm-4">
                        <label class="visually-hidden" for="specificSizeSelect">Preference</label>
                        <select class="form-select" id="specificSizeSelect" name="payrollPeriodID">
                            <option value="">Periodo de Nómina</option>
                            <?php
                            $sql_payroll_period_form = "SELECT * FROM payroll_period WHERE START_DATE <= '$today' AND YEAR = '$year2'
                                AND END_DATE >= (SELECT ADMISSION_DATE FROM employed WHERE ID_NOM = '$user_active')";
                            $result_payroll_period_form = $mysqli->query($sql_payroll_period_form);
                            if ($result_payroll_period_form->num_rows > 0) {
                                while ($rowPayrollPeriod = $result_payroll_period_form->fetch_assoc()) {
                                    $id = $rowPayrollPeriod['ID'];
                                    $code = $rowPayrollPeriod['CODE'];
                                    $description = $rowPayrollPeriod['DESCRIPTION'];
                                    $selected = ($payrollPeriodID == $id) ? 'selected' : ''; // Si coincide, se selecciona
                                    echo "<option value='$id' $selected>$code $description</option>";
                                }
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-auto">
                        <button type="submit" name="forms" value="formView" class="btn btn-primary">Seleccionar</button>
                </form>
            </div>
        </div>
        <!--Tabla de Historial-->
        <hr class="my-4">
        <?php
        if ($flag_send == 1) {
        ?>
            <table class="table table-primary table-hover table-bordered table-sm">
                <thead class="text-white text-center">
                    <tr>
                        <th scope="col" style="width: 15%;">Día</th>
                        <th scope="col" style="width: 15%;">Fecha</th>
                        <th scope="col" style="width: 17%;">Hora Entrada</th>
                        <th scope="col" style="width: 18%;">Hora Salida</th>
                        <th scope="col" style="width: 30%;">Estatus</th>
                        <th scope="col" style="width: 5%;">Justificado</th>
                    </tr>
                </thead>
                <tbody class="table-light">
                    <?php
                    if ($result_attendance_records->num_rows > 0) {
                        while ($row_attendance_records = $result_attendance_records->fetch_assoc()) {
                            $attendanceRecord_date = $row_attendance_records['CALENDAR_DATE'];
                            $attendanceRecord_day = $row_attendance_records['NAME_DAY'];
                            $attendanceRecord_start = substr($row_attendance_records['REG_ENTRADA'], 0, 8);
                            $attendanceRecord_end = substr($row_attendance_records['REG_SALIDA'], 0, 8);
                            $attendanceRecord_incidence = $row_attendance_records['DES_INCIDENCE'];
                            $attendanceRecord_justify = $row_attendance_records['JUSTIFY'];
                            $attendanceRecord_id = $row_attendance_records['AttendanceId'];
                            $attendanceRecord_entrada = $row_attendance_records['ENTRADA'];
                            $attendanceRecord_fer = $row_attendance_records['Feriado'];

                    ?>
                            <tr class="text-center">
                                
                                <td style="width: 15%;"><?php echo $attendanceRecord_day ?></td>
                                <td style="width: 15%;"><?php echo date("d/m/Y", strtotime($attendanceRecord_date)) ?></td>
                                <td style="width: 17%;"><?php echo $attendanceRecord_start ?></td>
                                <td style="width: 18%;"><?php echo $attendanceRecord_end ?></td>
                                <td style="width: 30%;"><?php if ($attendanceRecord_entrada == '') {
                                    echo 'DESCANSO';
                                } elseif ($attendanceRecord_fer == 'Feriado') {
                                    echo 'FERIADO';
                                } else {
                                    echo $attendanceRecord_incidence;
                                }?></td>
                                <td style="width: 5%;">
                                    <?php
                                    //Enviamos un ícono dependiendo si fue justificado
                                    if ($attendanceRecord_justify == 'Y') {
                                        echo '<i class="bi-check-circle-fill" style="color: green;"></i>';
                                    } elseif ($attendanceRecord_justify == 'N') {
                                        echo '<i class="bi-x-circle-fill" style="color: red;"></i>';
                                    } elseif ( ($attendanceRecord_incidence == 'FALTA INJUSTIFICADA' || $attendanceRecord_incidence == 'FALTA POR RETARDOS') && $attendanceRecord_justify == '' && $attendanceRecord_fer != 'Feriado' && $attendanceRecord_entrada != '') {
                                        ?>
                                        <div class="btn-group">
                                        <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Justificar
                                        </button>
                                        <div class="dropdown-menu p-4 text-body-secondary" style="width: 350px;">
                                            <p>
                                                Justificar
                                            </p>
                                            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                                                        <input hidden type="text" name="idAtt" id="" value="<?php echo $attendanceRecord_id ?>">
                                                        <input hidden class="form-control" name="idNom" id="message-text" value="<?php echo $user_active ?>"></input>
                                                        <input hidden class="form-control" name="Date" id="message-text" value="<?php echo $attendanceRecord_date ?>"></input>
                                                    <div class="mb-3">
                                                        <label for="message-text" class="col-form-label">Justificación:</label>
                                                        <textarea class="form-control" name="comment" id="message-text"></textarea>
                                                    </div>
                                                <button type="submit" name="forms" value="justifForm" class="btn btn-primary">Justificar</button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
        ?>
    </div>

    <!-- Incluye la biblioteca de iconos de Bootstrap Icons -->
    <script src="../../../static/css/bootstrap-icons/font/bootstrap-icons.css"></script>

</body>

</html>