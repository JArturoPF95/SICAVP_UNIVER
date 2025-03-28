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
if (isset($_GET['id'])) {
    $id_nom = $_GET['id'];
} else {
    $id_nom = '';
}

$sqlGetEmployed = "SELECT USR.ACCESS_LEVEL, EMP.* FROM employed EMP INNER JOIN users USR ON USR.SICAVP_USER = EMP.ID_NOM WHERE EMP.ID_NOM = '$id_nom'";
$resultGetEmployed = $mysqli->query($sqlGetEmployed);
if ($resultGetEmployed->num_rows > 0) {
    while ($rowEmployed = $resultGetEmployed->fetch_assoc()) {
        $id = $rowEmployed['ID_NOM'];
        $payroll = $rowEmployed['PAYROLL'];
        $name = $rowEmployed['NAME'];
        $lastname = $rowEmployed['LAST_NAME'];
        $taxpayerId = $rowEmployed['TAXPAYER_ID'];
        $governmentId = $rowEmployed['GOVERNMENT_ID'];
        $imss = $rowEmployed['IMSS'];
        $genre = $rowEmployed['GENRE'];
        $area = $rowEmployed['AREA'];
        $job = $rowEmployed['JOB'];
        $department = $rowEmployed['DEPARTMENT'];
        $position = $rowEmployed['POSITION'];
        $scheduleGroup = $rowEmployed['SCHEDULE_GROUP'];
        $daytrip = $rowEmployed['DAYTRIP'];
        $contract = $rowEmployed['CONTRACT'];
        $admissionDate = $rowEmployed['ADMISSION_DATE'];
        $permanentEmpDate = $rowEmployed['PERMANENT_EMP_DATE'];
        $antiquity = $rowEmployed['ANTIQUITY'];
        $contractStart = $rowEmployed['CONTRACT_START'];
        $supervisorId = $rowEmployed['SUPERVISOR_ID'];
        $supervisorName = $rowEmployed['SUPERVISOR_NAME'];
        $positionSup = $rowEmployed['POSITION_SUEPRVISOR'];
        $lastnamePrefix = $rowEmployed['LAST_NAME_PREFIX'];
        $nomSesion = $rowEmployed['NOM_SESSION'];
        $city = $rowEmployed['CITY'];
        $accessLevel = $rowEmployed['ACCESS_LEVEL'];
        $idSupAux = $rowEmployed['SUPERVISOR_ID_AUX'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src='https://code.jquery.com/jquery-3.6.3.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
    <script src="../../../static/js/select2.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h4 class="mb-3">Actualizar Usuario</h4>
        <main>
            <div class="col-md-6 col-lg-12">
                <form class="needs-validation" novalidate action='updEmployed.php' method='POST'>
                    <div class="row g-3">

                        <!-- Personal -->
                        <h5>Personal</h5>
                        <div class="col-sm-4">
                            <label for="idNom" class="form-label">ID Nom2001</label>
                            <input type="text" class="form-control" id="idNom" name="idNom" placeholder="0000001" value="<?php echo $id ?>" minlength="7" maxlength="7" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="firstName" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="firstName" name="name" placeholder="NOMBRE" value="<?php echo $name ?>" maxlength="25" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="lastName" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="APELLIDO" value="<?php echo $lastname ?>" maxlength="25" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="lastNamePrefix" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="lastNamePrefix" name="lastNamePrefix" placeholder="APELLIDO MATERNO" value="<?php echo $lastnamePrefix ?>" maxlength="40" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="genre" class="form-label">Género</label>
                            <div class="my-3">
                                <?php if ($genre == 'F') {
                                    echo '
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="genre" id="inlineRadio1" value="F" checked>
                                        <label class="form-check-label" for="inlineRadio1">Femenino</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="genre" id="inlineRadio2" value="M">
                                        <label class="form-check-label" for="inlineRadio2">Masculino</label>
                                    </div>';
                                } else {
                                    echo '<div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="genre" id="inlineRadio1" value="F">
                                        <label class="form-check-label" for="inlineRadio1">Femenino</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="genre" id="inlineRadio2" value="M" checked>
                                        <label class="form-check-label" for="inlineRadio2">Masculino</label>
                                    </div>';
                                }
                                ?>

                            </div>
                        </div>

                        <!-- Fiscal -->
                        <div class="col-sm-4">
                            <label for="curp" class="form-label">CURP</label>
                            <input type="text" class="form-control" id="curp" name="governmentID" placeholder="ABCD000102HJLXYZ01" value="<?php echo $governmentId ?>" maxlength="18" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="taxpayerID" class="form-label">RFC</label>
                            <input type="text" class="form-control" id="taxpayerID" name="taxpayerID" placeholder="ABCD000102XY1" value="<?php echo $taxpayerId ?>" maxlength="13" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="imss" class="form-label">Número de Seguridad Social (NSS)</label>
                            <input type="text" class="form-control" id="imss" name="imss" placeholder="01010101010" value="<?php echo $imss ?>" maxlength="11" required>
                        </div>

                        <!-- Ubicación -->
                        <hr class="my-4">
                        <h5>Ubicación</h5>
                        <div class="col-md-4">
                            <label for="city" class="form-label">Ciudad</label>
                            <select class="form-select" id="city" name="city" required>
                                <?php
                                $sqlCityA = "SELECT DISTINCT CODE_CITY, CITY FROM code_sesion WHERE CODE_CITY = '$city' ORDER BY CITY ASC";
                                $resultCityA = $mysqli->query($sqlCityA);
                                if ($resultCityA->num_rows > 0) {
                                    while ($rowCityA = $resultCityA->fetch_assoc()) {
                                        echo '<option selected value="' . $rowCityA['CODE_CITY'] . '">' . $rowCityA['CITY'] . '</option>';
                                    }
                                }
                                ?>
                                <?php
                                $sqlCity = "SELECT DISTINCT CODE_CITY, CITY FROM code_sesion WHERE CODE_CITY <> '$city' ORDER BY CITY ASC";
                                $resultCity = $mysqli->query($sqlCity);
                                if ($resultCity->num_rows > 0) {
                                    while ($rowCity = $resultCity->fetch_assoc()) {
                                        echo '<option value="' . $rowCity['CODE_CITY'] . '">' . $rowCity['CITY'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="nomSesion" class="form-label">Campus</label>
                            <select class="form-select" name="nomSesion" id="nomSesion" required>
                                <?php
                                // Obtener el campus actual desde la base de datos
                                $sqlSesA = "SELECT DISTINCT CODE_SESION_NOM, NOM_SESION FROM code_sesion WHERE CODE_SESION_NOM = '$nomSesion' ORDER BY NOM_SESION";
                                $resultSesA = $mysqli->query($sqlSesA);

                                if ($resultSesA->num_rows > 0) {
                                    while ($rowSesA = $resultSesA->fetch_assoc()) {
                                        echo '<option selected value="' . $rowSesA['CODE_SESION_NOM'] . '">' . $rowSesA['NOM_SESION'] . '</option>';
                                    }
                                }

                                // También obtener el resto de los campus (sin seleccionar el actual)
                                $sqlCampusExtra = "SELECT DISTINCT CODE_SESION_NOM, NOM_SESION FROM code_sesion WHERE CODE_SESION_NOM <> '$nomSesion' ORDER BY NOM_SESION";
                                $resultCampusExtra = $mysqli->query($sqlCampusExtra);

                                if ($resultCampusExtra->num_rows > 0) {
                                    while ($rowCampusExtra = $resultCampusExtra->fetch_assoc()) {
                                        echo '<option value="' . $rowCampusExtra['CODE_SESION_NOM'] . '">' . $rowCampusExtra['NOM_SESION'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Contrato -->
                        <hr class="my-4">
                        <h5>Laboral</h5>
                        <div class="col-md-4">
                            <label for="area" class="form-label">Área</label>
                            <select class="form-select" id="area" name="area" required>
                                <?php
                                $sqlAreaA = "SELECT DISTINCT CODE_AREA, NAME_AREA FROM code_area WHERE CODE_AREA = '$area' ORDER BY NAME_AREA ASC;";
                                $resultAreaA = $mysqli->query($sqlAreaA);
                                if ($resultAreaA->num_rows > 0) {
                                    while ($rowAreaA = $resultAreaA->fetch_assoc()) {
                                        echo '<option selected value="' . $rowAreaA['CODE_AREA'] . '">' . $rowAreaA['NAME_AREA'] . '</option>';
                                    }
                                }
                                ?>
                                <?php
                                $sqlArea = "SELECT DISTINCT CODE_AREA, NAME_AREA FROM code_area WHERE CODE_AREA <> '0' AND CODE_AREA <> '$area' ORDER BY NAME_AREA ASC;";
                                $resultArea = $mysqli->query($sqlArea);
                                if ($resultArea->num_rows > 0) {
                                    while ($rowArea = $resultArea->fetch_assoc()) {
                                        echo '<option value="' . $rowArea['CODE_AREA'] . '">' . $rowArea['NAME_AREA'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="department" class="form-label">Departamento</label>
                            <select class="form-select" name="department" id="department" required>
                                <?php
                                // Obtener el campus actual desde la base de datos
                                $sqlDepA = "SELECT DISTINCT CODE_DEPRTMENT, DEPARTMENT FROM code_department WHERE CODE_DEPRTMENT = '$department' ORDER BY DEPARTMENT ASC";
                                $resultDepA = $mysqli->query($sqlDepA);

                                if ($resultDepA->num_rows > 0) {
                                    while ($rowDepA = $resultDepA->fetch_assoc()) {
                                        echo '<option selected value="' . $rowDepA['CODE_DEPRTMENT'] . '">' . $rowDepA['DEPARTMENT'] . '</option>';
                                    }
                                }

                                // También obtener el resto de los campus (sin seleccionar el actual)
                                $sqlDepartmentExtra = "SELECT DISTINCT CODE_DEPRTMENT, DEPARTMENT FROM code_department WHERE CODE_DEPRTMENT NOT IN ('$department','0') ORDER BY DEPARTMENT ASC";
                                $resultDepartmentExtra = $mysqli->query($sqlDepartmentExtra);

                                if ($resultDepartmentExtra->num_rows > 0) {
                                    while ($rowDepartmentExtra = $resultDepartmentExtra->fetch_assoc()) {
                                        echo '<option value="' . $rowDepartmentExtra['CODE_DEPRTMENT'] . '">' . $rowDepartmentExtra['DEPARTMENT'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="job" class="form-label">Puesto</label>
                            <select class="form-select" id="job" name="job" required>
                                <?php
                                $sqlJobA = "SELECT DISTINCT CODE_JOB, JOB_NAME FROM code_jobs WHERE CODE_JOB = '$job' ORDER BY JOB_NAME ASC;";
                                $resultJobA = $mysqli->query($sqlJobA);
                                $jobA = $resultJobA->num_rows;
                                while ($rowJobA = $resultJobA->fetch_assoc()) {
                                    echo '<option selected value="' . $rowJobA['CODE_JOB'] . '">' . $rowJobA['JOB_NAME'] . '</option>';
                                }
                                ?>
                                <?php
                                $sqlJob = "SELECT DISTINCT CODE_JOB, JOB_NAME FROM code_jobs WHERE CODE_JOB <> '$job' ORDER BY JOB_NAME ASC;";
                                $resultJob = $mysqli->query($sqlJob);
                                $job = $resultJob->num_rows;
                                while ($rowJob = $resultJob->fetch_assoc()) {
                                    echo '<option value="' . $rowJob['CODE_JOB'] . '">' . $rowJob['JOB_NAME'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="position" class="form-label">Posición</label>
                            <select class="form-select" name="position" id="position" required>
                                <?php
                                // Obtener el campus actual desde la base de datos
                                $sqlPosA = "SELECT DISTINCT CODE_POSITION, POSITION_DESCRIPTION FROM code_position WHERE CODE_POSITION = '$position' ORDER BY POSITION_DESCRIPTION ASC";
                                $resultPosA = $mysqli->query($sqlPosA);

                                if ($resultPosA->num_rows > 0) {
                                    while ($rowPosA = $resultPosA->fetch_assoc()) {
                                        echo '<option selected value="' . $rowPosA['CODE_POSITION'] . '">' . $rowPosA['POSITION_DESCRIPTION'] . '</option>';
                                    }
                                }

                                // También obtener el resto de los campus (sin seleccionar el actual)
                                $sqlPositionExtra = "SELECT DISTINCT CODE_POSITION, POSITION_DESCRIPTION FROM code_position WHERE CODE_POSITION NOT IN ('$position','0') ORDER BY POSITION_DESCRIPTION ASC";
                                $resultPositionExtra = $mysqli->query($sqlPositionExtra);

                                if ($resultPositionExtra->num_rows > 0) {
                                    while ($rowPositionExtra = $resultPositionExtra->fetch_assoc()) {
                                        echo '<option value="' . $rowPositionExtra['CODE_POSITION'] . '">' . $rowPositionExtra['POSITION_DESCRIPTION'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="payroll" class="form-label">Tipo de Nómina</label>
                            <select class="form-select" id="payroll" name="payroll" required>
                                <?php
                                $sqlPayrollA = "SELECT DISTINCT CODE_PAYROLL, DESCRIPTION FROM code_payroll WHERE CODE_PAYROLL = '$payroll';";
                                $resultPayrollA = $mysqli->query($sqlPayrollA);
                                $payroll = $resultPayrollA->num_rows;
                                while ($rowPayrollA = $resultPayrollA->fetch_assoc()) {
                                    echo '<option selected value="' . $rowPayrollA['CODE_PAYROLL'] . '">' . $rowPayrollA['DESCRIPTION'] . '</option>';
                                }
                                ?>
                                <?php
                                $sqlPayroll = "SELECT DISTINCT CODE_PAYROLL, DESCRIPTION FROM code_payroll WHERE CODE_PAYROLL <> '0' AND CODE_PAYROLL <> '$payroll';";
                                $resultPayroll = $mysqli->query($sqlPayroll);
                                $payroll = $resultPayroll->num_rows;
                                while ($rowPayroll = $resultPayroll->fetch_assoc()) {
                                    echo '<option value="' . $rowPayroll['CODE_PAYROLL'] . '">' . $rowPayroll['DESCRIPTION'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="access" class="form-label">Nivel de Acceso</label>
                            <select class="form-select" id="access" name="access" required>
                                <?php
                                $sqlaccessLevelsA = "SELECT * FROM code_accesslevels WHERE CODE_LEVEL = '$accessLevel'";
                                $resultaccessLevelsA = $mysqli->query($sqlaccessLevelsA);
                                $accessLevelsA = $resultaccessLevelsA->num_rows;
                                while ($rowaccessLevelsA = $resultaccessLevelsA->fetch_assoc()) {
                                    echo '<option selected value="' . $rowaccessLevelsA['CODE_LEVEL'] . '">' . $rowaccessLevelsA['LEVEL_DESCRIPTION'] . '</option>';
                                }
                                ?>
                                <?php
                                $sqlaccessLevels = "SELECT CODE_LEVEL, LEVEL_DESCRIPTION FROM code_accesslevels WHERE CODE_LEVEL <> '$accessLevel'";
                                $resultaccessLevels = $mysqli->query($sqlaccessLevels);
                                $accessLevels = $resultaccessLevels->num_rows;
                                while ($rowaccessLevels = $resultaccessLevels->fetch_assoc()) {
                                    echo '<option value="' . $rowaccessLevels['CODE_LEVEL'] . '">' . $rowaccessLevels['LEVEL_DESCRIPTION'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="contract" class="form-label">Contrato</label>
                            <select class="form-select" id="contract" name="contract" required>
                                <?php
                                if ($contract == '01') {
                                    echo '
                                    <option selected value="01">CONTRATO INDETERMINADO</option>
                                    <option value="02">CONTRATO DETERMINADO</option>';
                                } elseif ($contract == '02') {
                                    echo '
                                    <option selected value="02">CONTRATO DETERMINADO</option>
                                    <option value="01">CONTRATO INDETERMINADO</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Horarios -->
                        <div class="col-md-4">
                            <label for="scheduleGroup" class="form-label">Grupo Horario</label>
                            <select class="form-select" id="scheduleGroup" name="scheduleGroup" required>
                                <?php
                                $sqlScheduleGroupA = "SELECT DISTINCT CODE_GROUP, GROUP_NAME FROM groups_daytrip WHERE CODE_GROUP = '$scheduleGroup' ORDER BY GROUP_NAME ASC;";
                                $resultScheduleGroupA = $mysqli->query($sqlScheduleGroupA);
                                $scheduleGroupA = $resultScheduleGroupA->num_rows;
                                while ($rowScheduleGroupA = $resultScheduleGroupA->fetch_assoc()) {
                                    echo '<option value="' . $rowScheduleGroupA['CODE_GROUP'] . '">' . $rowScheduleGroupA['GROUP_NAME'] . '</option>';
                                }
                                ?>
                                <?php
                                $sqlScheduleGroup = "SELECT DISTINCT CODE_GROUP, GROUP_NAME FROM groups_daytrip WHERE CODE_GROUP NOT IN ('0','$scheduleGroup') ORDER BY GROUP_NAME ASC;";
                                $resultScheduleGroup = $mysqli->query($sqlScheduleGroup);
                                $scheduleGroup = $resultScheduleGroup->num_rows;
                                while ($rowScheduleGroup = $resultScheduleGroup->fetch_assoc()) {
                                    echo '<option value="' . $rowScheduleGroup['CODE_GROUP'] . '">' . $rowScheduleGroup['GROUP_NAME'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="daytrip" class="form-label">Jornada</label>
                            <select class="form-select" id="daytrip" name="daytrip" required>
                                <?php
                                // Obtener el campus actual desde la base de datos
                                $sqlDyTA = "SELECT DISTINCT CODE_DAYTRIP, DAYTRIP FROM groups_daytrip WHERE CODE_DAYTRIP = '$daytrip' ORDER BY DAYTRIP ASC";
                                $resultDyTA = $mysqli->query($sqlDyTA);

                                if ($resultDyTA->num_rows > 0) {
                                    while ($rowDyTA = $resultDyTA->fetch_assoc()) {
                                        echo '<option selected value="' . $rowDyTA['CODE_DAYTRIP'] . '">' . $rowDyTA['DAYTRIP'] . '</option>';
                                    }
                                }

                                // También obtener el resto de los campus (sin seleccionar el actual)
                                $sqlDaytripExtra = "SELECT DISTINCT CODE_DAYTRIP, DAYTRIP FROM groups_daytrip WHERE CODE_DAYTRIP NOT IN ('$daytrip','0') ORDER BY DAYTRIP";
                                $resultDaytripExtra = $mysqli->query($sqlDaytripExtra);

                                if ($resultDaytripExtra->num_rows > 0) {
                                    while ($rowDaytripExtra = $resultDaytripExtra->fetch_assoc()) {
                                        echo '<option value="' . $rowDaytripExtra['CODE_DAYTRIP'] . '">' . $rowDaytripExtra['DAYTRIP'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Horarios -->
                        <div class="col-md-4">
                            <label for="daytrip" class="form-label">Jornada</label>
                            <select class="form-select" id="daytrip" name="daytrip" required>
                                <?php
                                $sqlScheduleGroupA = "SELECT DISTINCT CODE_DAYTRIP, DAYTRIP FROM groups_daytrip WHERE CODE_DAYTRIP = '$daytrip' ORDER BY DAYTRIP ASC;";
                                $resultScheduleGroupA = $mysqli -> query($sqlScheduleGroupA);
                                $scheduleGroupA = $resultScheduleGroupA -> num_rows;
                                while ($rowScheduleGroupA = $resultScheduleGroupA -> fetch_assoc()) {
                                    echo '<option selected value="'.$rowScheduleGroupA['CODE_DAYTRIP'].'">'.$rowScheduleGroupA['DAYTRIP'].'</option>';
                                }
                                $sqlScheduleGroup = "SELECT DISTINCT CODE_DAYTRIP, DAYTRIP FROM groups_daytrip WHERE CODE_DAYTRIP <> '0' AND CODE_DAYTRIP <> '$daytrip' ORDER BY DAYTRIP ASC;";
                                $resultScheduleGroup = $mysqli -> query($sqlScheduleGroup);
                                $scheduleGroup = $resultScheduleGroup -> num_rows;
                                while ($rowScheduleGroup = $resultScheduleGroup -> fetch_assoc()) {
                                    echo '<option value="'.$rowScheduleGroup['CODE_DAYTRIP'].'">'.$rowScheduleGroup['DAYTRIP'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="scheduleGroup" class="form-label">Grupo Horario</label>
                            <select class="form-select" id="scheduleGroup" name="scheduleGroup" required>
                                <?php
                                $sqlScheduleA = "SELECT DISTINCT CODE_NOM, DAYTRIP FROM code_schedule WHERE CODE_NOM = '$scheduleGroup';";
                                $resultScheduleA = $mysqli -> query($sqlScheduleA);
                                $scheduleA = $resultScheduleA -> num_rows;
                                while ($rowScheduleA = $resultScheduleA -> fetch_assoc()) {
                                    echo '<option selected value="'.$rowScheduleA['CODE_NOM'].'">'.$rowScheduleA['DAYTRIP'].'</option>';
                                }
                                $sqlSchedule = "SELECT DISTINCT CODE_NOM, DAYTRIP FROM code_schedule WHERE CODE_NOM <> '0' AND CODE_NOM <> '$scheduleGroup' AND FLEX_SCHEDULE = 0 ORDER BY DAYTRIP ASC;";
                                $resultSchedule = $mysqli -> query($sqlSchedule);
                                $schedule = $resultSchedule -> num_rows;
                                while ($rowSchedule = $resultSchedule -> fetch_assoc()) {
                                    echo '<option value="'.$rowSchedule['CODE_NOM'].'">'.$rowSchedule['DAYTRIP'].'</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Fechas -->
                        <div class="col-sm-3">
                            <label for="admissionDate" class="form-label">Fecha de Alta</label>
                            <input type="date" class="form-control" id="admissionDate" name="admissionDate" placeholder="" value="<?php echo $admissionDate ?>" maxlength="11" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="permanentEmpDate" class="form-label">Fecha de Planta</label>
                            <input type="date" class="form-control" id="permanentEmpDate" name="permanentEmpDate" placeholder="" value="<?php echo $permanentEmpDate ?>" maxlength="11" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="antiquity" class="form-label">Fecha de Antigüedad</label>
                            <input type="date" class="form-control" id="antiquity" name="antiquity" placeholder="" value="<?php echo $antiquity ?>" maxlength="11" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="contractStart" class="form-label">Inicio de Contrato</label>
                            <input type="date" class="form-control" id="contractStart" name="contractStart" placeholder="" value="<?php echo $contractStart ?>" maxlength="11" required>
                        </div>

                        <!-- Supervisor -->
                        <hr class="my-4">
                        <h5>Supervisor(es)</h5>
                        <div class="col-sm-6">
                            <label for="supervisorID" class="form-label">Supervisor</label>
                            <select class="form-select js-example-basic-single" name="supervisorID" id="supervisorID">
                                <?php
                                if ($supervisorId == NULL || $supervisorId == '') {
                                    echo '<option selected disabled>Seleccionar Supervisor Auxiliar</option>';
                                } else {
                                    $sqlSupA = "SELECT DISTINCT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) AS SUP_NAME FROM employed WHERE ID_NOM = '$supervisorId' ORDER BY SUP_NAME ASC;";
                                    $resultSupA = $mysqli->query($sqlSupA);

                                    while($rowSupA = $resultSupA->fetch_assoc()) {
                                        echo "<option selected value='".$rowSupA['ID_NOM']."'>".$rowSupA['ID_NOM']. " - " . $rowSupA['SUP_NAME']."</option>";
                                    }
                                }
                                $sqlSup = "SELECT DISTINCT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) AS SUP_NAME FROM employed WHERE ID_NOM NOT IN ('$idSupAux','$supervisorId','$id') ORDER BY SUP_NAME ASC;";
                                $resultSup = $mysqli->query($sqlSup);

                                while($rowSup = $resultSup->fetch_assoc()) {
                                    echo "<option value='".$rowSup['ID_NOM']."'>".$rowSup['ID_NOM']. " - " . $rowSup['SUP_NAME']."</option>";
                                }
                                ?>
                            </select>
                        </div>       

                        <!-- Auxiliar --> <!-- Descomentar cuando de habilite-->
                        <div class="col-sm-6">
                            <label for="supervisorIdAux" class="form-label">Supervisor Auxiliar</label>
                            <select class="form-select js-example-basic-single" name="supervisorIdAux" id="supervisorIdAux">
                                <?php
                                
                                if ($idSupAux == NULL || $idSupAux == '') {
                                    echo '<option selected disabled>Seleccionar Supervisor Auxiliar</option>';
                                } else {
                                    $sqlSupAuxA = "SELECT DISTINCT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) AS SUP_NAME FROM employed WHERE ID_NOM = '$idSupAux' ORDER BY SUP_NAME ASC;";
                                    $resultSupAuxA = $mysqli->query($sqlSupAuxA);
                                    while($rowSupAuxA = $resultSupAuxA->fetch_assoc()) {
                                        if ($rowSupAuxA['ID_NOM'] == NULL || $rowSupAuxA['ID_NOM'] == '') {
                                            echo "<option disabled selected>Seleccionar Supervisor Auxiliar</option>";
                                        } else {
                                            echo "<option selected value='".$rowSupAuxA['ID_NOM']."'>".$rowSupAuxA['ID_NOM']. " - " . $rowSupAuxA['SUP_NAME']."</option>";
                                        }
                                    }
                                }

                                $sqlSupAux = "SELECT DISTINCT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) AS SUP_NAME FROM employed WHERE ID_NOM NOT IN ('$idSupAux','$supervisorId','$id') ORDER BY SUP_NAME ASC;";
                                $resultSupAux = $mysqli->query($sqlSupAux);                                
                                echo '<option value="NULL">Sin Supervisor Auxiliar</option>';
                                while($rowSupAux = $resultSupAux->fetch_assoc()) {
                                    echo "<option value='".$rowSupAux['ID_NOM']."'>".$rowSupAux['ID_NOM']. " - " . $rowSupAux['SUP_NAME']."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm" style="width: 200px;" type="submit">Actualizar</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="../admin_users.php?id=<?php echo $user_active ?>" class="btn btn-secondary btn-sm" style="width: 200px;">Regresar</a>
                        </div>
                </form>
            </div>
        </main>
    </div>

    <script>

        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });

        //Formulario
        $(document).ready(function() {
            $("#city").change(function() {
                let city = $(this).val(); // Obtener ciudad seleccionada
                let nomSesionActual = $("#nomSesion").val(); // Obtener el valor actual del select

                $.post("includes/getCampusUpd.php", {
                    city: city,
                    nomSesion: nomSesionActual
                }, function(data) {
                    $("#nomSesion").html(data);
                });
            });
        });

        //Obtener el el departamento por el área
        $(document).ready(function() {
            $("#area").change(function() {
                let area = $(this).val(); // Obtener ciudad seleccionada
                let departmentActual = $("#department").val(); // Obtener el valor actual del select

                $.post("includes/getDepartmentUpd.php", {
                    area: area,
                    department: departmentActual
                }, function(data) {
                    $("#department").html(data);
                });
            });
        });

        //Obtener la posición 
        $(document).ready(function() {
            let area = "";
            let department = "";

            // Deshabilitar los selects dependientes al inicio
            $("#department, #job, #position").prop("disabled", false);

            // Evento al cambiar el área
            $("#area").change(function() {
                area = $(this).val();
                $("#job, #position").prop("disabled", false).html("<option selected>Seleccionar</option>");

                if (area) {
                    $("#department").prop("disabled", false);
                    $.post("includes/getDepartmentUpd.php", {
                        area: area
                    }, function(data) {
                        $("#department").html(data);
                    });
                } else {
                    $("#department").prop("disabled", false).html("<option selected>Seleccionar</option>");
                }
            });

            // Evento al cambiar el departamento
            $("#department").change(function() {
                department = $(this).val();
                $("#position").prop("disabled", false).html("<option selected>Seleccionar</option>");

                if (department) {
                    $("#job").prop("disabled", false);
                } else {
                    $("#job").prop("disabled", false).html("<option selected>Seleccionar</option>");
                }
            });

            // Evento al cambiar el puesto
            $("#job").change(function() {
                let job = $(this).val();

                if (area && department && job) {
                    $.post("includes/getPositionUpd.php", {
                        job: job,
                        department: department,
                        area: area
                    }, function(data) {
                        $("#position").html(data).prop("disabled", false);
                    });
                } else {
                    alert("Debes seleccionar primero el área y el departamento antes de elegir un puesto.");
                    $("#position").prop("disabled", true).html("<option selected>Seleccionar</option>");
                }
            });
        });

        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

</body>

</html>