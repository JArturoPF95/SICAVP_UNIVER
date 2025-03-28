<?php

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
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos</title>
    <script src="../../static/js/popper.min.js"></script>
    <script src="../../static/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src='https://code.jquery.com/jquery-3.6.3.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
    <script src="../../static/js/select2.min.js"></script>
    <link rel="stylesheet" href="../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../static/css/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../static/css/styles/tables.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h4 class="mb-3">Nuevo Empleado</h4>
        <main>
            <div class="col-md-6 col-lg-12">
                <form class="needs-validation" novalidate action='administrative/newEmployed.php' method='POST'>
                    <div class="row g-3">

                        <!-- Personal -->
                        <h5>Personal</h5>
                        <div class="col-sm-4">
                            <label for="idNom" class="form-label">ID Nom2001</label>
                            <input type="text" class="form-control" id="idNom" name="idNom" placeholder="0000001" minlength="7" maxlength="7" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="firstName" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="firstName" name="name" placeholder="NOMBRE" maxlength="25" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="lastName" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="APELLIDO" maxlength="25" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="lastNamePrefix" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="lastNamePrefix" name="lastNamePrefix" placeholder="APELLIDO MATERNO" maxlength="40" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="genre" class="form-label">Género</label>
                            <div class="my-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="genre" id="inlineRadio1" value="F">
                                    <label class="form-check-label" for="inlineRadio1">Femenino</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="genre" id="inlineRadio2" value="M">
                                    <label class="form-check-label" for="inlineRadio2">Masculino</label>
                                </div>
                            </div>
                        </div>

                        <!-- Fiscal -->
                        <div class="col-sm-4">
                            <label for="curp" class="form-label">CURP</label>
                            <input type="text" class="form-control" id="curp" name="governmentID" placeholder="ABCD000102HJLXYZ01" maxlength="18" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="taxpayerID" class="form-label">RFC</label>
                            <input type="text" class="form-control" id="taxpayerID" name="taxpayerID" placeholder="ABCD000102XY1" maxlength="13" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="imss" class="form-label">Número de Seguridad Social (NSS)</label>
                            <input type="text" class="form-control" id="imss" name="imss" placeholder="01010101010" maxlength="11" required>
                        </div>

                        <!-- Ubicación -->
                        <hr class="my-4">
                        <h5>Ubicación</h5>
                        <div class="col-md-4">
                            <label for="city" class="form-label">Ciudad</label>
                            <select class="form-select" id="city" name="city" required>
                                <option selected disabled>Seleccionar Ciudad</option>
                                <?php 
                                $sqlCity = "SELECT DISTINCT CODE_CITY, CITY FROM code_sesion ORDER BY CITY ASC";
                                $resultCity = $mysqli -> query($sqlCity);
                                if ($resultCity -> num_rows > 0) {
                                    while ($rowCity = $resultCity -> fetch_assoc()) {
                                        echo '<option value="'.$rowCity['CODE_CITY'].'">'.$rowCity['CITY'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="nomSesion" class="form-label">Campus</label>
                            <select class="form-select" name="nomSesion" id="nomSesion" required>
                                <option selected disabled>Seleccione</option>
                            </select>
                        </div>

                        <!-- Contrato -->
                        <hr class="my-4">
                        <h5>Laboral</h5>
                        <div class="col-md-4">
                            <label for="area" class="form-label">Área</label>
                            <select class="form-select" id="area" name="area" required>
                                <option selected disabled>Seleccionar Área</option>
                                <?php 
                                    $sqlArea = "SELECT DISTINCT CODE_AREA, NAME_AREA FROM code_area WHERE CODE_AREA <> '0' ORDER BY NAME_AREA ASC;";
                                    $resultArea = $mysqli -> query($sqlArea);
                                    if ($resultArea -> num_rows > 0) {
                                        while ($rowArea = $resultArea -> fetch_assoc()) {
                                            echo '<option value="'.$rowArea['CODE_AREA'].'">'.$rowArea['NAME_AREA'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="department" class="form-label">Departamento</label>
                            <select class="form-select" name="department" id="department" required>
                                <option selected disabled>Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="job" class="form-label">Puesto</label>
                            <select class="form-select" id="job" name="job" required>
                                <option selected disabled>Seleccionar Puesto</option>
                                <?php
                                $sqlJob = "SELECT DISTINCT CODE_JOB, JOB_NAME FROM code_jobs WHERE CODE_JOB <> '0' ORDER BY JOB_NAME ASC;";
                                $resultJob = $mysqli -> query($sqlJob);
                                $job = $resultJob -> num_rows;
                                while ($rowJob = $resultJob -> fetch_assoc()) {
                                    echo '<option value="'.$rowJob['CODE_JOB'].'">'.$rowJob['JOB_NAME'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="position" class="form-label">Posición</label>
                            <select class="form-select" name="position" id="position" required>
                                <option selected disabled>Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="payroll" class="form-label">Tipo de Nómina</label>
                            <select class="form-select" id="payroll" name="payroll" required>
                                <option selected disabled>Seleccionar Tipo de Nómina</option>
                                <?php
                                $sqlPayroll = "SELECT DISTINCT CODE_PAYROLL, DESCRIPTION FROM code_payroll WHERE CODE_PAYROLL <> '0';";
                                $resultPayroll = $mysqli -> query($sqlPayroll);
                                $payroll = $resultPayroll -> num_rows;
                                while ($rowPayroll = $resultPayroll -> fetch_assoc()) {
                                    echo '<option value="'.$rowPayroll['CODE_PAYROLL'].'">'.$rowPayroll['DESCRIPTION'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="access" class="form-label">Nivel de Acceso</label>
                            <select class="form-select" id="access" name="access" required>
                                <option selected disabled>Seleccionar Nivel de Acceso</option>
                                <?php
                                $sqlaccessLevels = "SELECT CODE_LEVEL, LEVEL_DESCRIPTION FROM code_accesslevels";
                                $resultaccessLevels = $mysqli -> query($sqlaccessLevels);
                                $accessLevels = $resultaccessLevels -> num_rows;
                                while ($rowaccessLevels = $resultaccessLevels -> fetch_assoc()) {
                                    echo '<option value="'.$rowaccessLevels['CODE_LEVEL'].'">'.$rowaccessLevels['LEVEL_DESCRIPTION'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="contract" class="form-label">Contrato</label>
                            <select class="form-select" id="contract" name="contract" required>
                                <option selected disabled>Seleccionar Contrato</option>
                                <option value="01">CONTRATO INDETERMINADO</option>
                                <option value="02">CONTRATO DETERMINADO</option>
                            </select>
                        </div>

                        <!-- Horarios -->
                        <div class="col-md-4">
                            <label for="daytrip" class="form-label">Jornada</label>
                            <select class="form-select" id="daytrip" name="daytrip" required>
                                <option selected disabled>Seleccionar Jornada</option>
                                <?php
                                $sqlScheduleGroup = "SELECT DISTINCT CODE_DAYTRIP, DAYTRIP FROM groups_daytrip WHERE CODE_DAYTRIP <> '0' ORDER BY DAYTRIP ASC;";
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
                                <option selected disabled>Seleccionar Grupo Horario</option>
                                <?php
                                $sqlSchedule = "SELECT DISTINCT CODE_NOM, DAYTRIP FROM code_schedule WHERE CODE_NOM <> '0' AND FLEX_SCHEDULE = 0 ORDER BY DAYTRIP ASC;";
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
                            <input type="date" class="form-control" id="admissionDate" name="admissionDate" placeholder="" value="<?php echo date('Y-m-d') ?>" maxlength="11" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="permanentEmpDate" class="form-label">Fecha de Planta</label>
                            <input type="date" class="form-control" id="permanentEmpDate" name="permanentEmpDate" placeholder="" value="<?php echo date('Y-m-d') ?>" maxlength="11" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="antiquity" class="form-label">Fecha de Antigüedad</label>
                            <input type="date" class="form-control" id="antiquity" name="antiquity" placeholder="" value="<?php echo date('Y-m-d') ?>" maxlength="11" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="contractStart" class="form-label">Inicio de Contrato</label>
                            <input type="date" class="form-control" id="contractStart" name="contractStart" placeholder="" value="<?php echo date('Y-m-d') ?>" maxlength="11" required>
                        </div>
                        
                        <!-- Supervisor -->
                        <hr class="my-4">
                        <h5>Supervisor(es)</h5>
                        <div class="col-sm-6">
                            <label for="supervisorID" class="form-label">Supervisor</label>
                            <select class="form-select js-example-basic-single" name="supervisorID" id="supervisorID">
                                <option value="">Seleccione un supervisor</option>
                                <?php
                                $sqlSup = "SELECT DISTINCT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) AS SUP_NAME FROM employed ORDER BY SUP_NAME ASC;";
                                $resultSup = $mysqli->query($sqlSup);

                                while($rowSup = $resultSup->fetch_assoc()) {
                                    echo "<option value='".$rowSup['ID_NOM']."'>".$rowSup['ID_NOM']. " - " . $rowSup['SUP_NAME']."</option>";
                                }
                                ?>
                            </select>
                        </div>       

                        <!-- Auxiliar --> <!-- Descomentar cuando de habilite-->
                        <div class="col-sm-6">
                            <label for="supervisorIDAux" class="form-label">Supervisor Auxiliar</label>
                            <select class="form-select js-example-basic-single" name="supervisorIDAux" id="supervisorIDAux">
                                <option value="">Seleccione un supervisor</option>
                                <?php
                                $sqlSupAux = "SELECT DISTINCT ID_NOM, CONCAT(NAME,' ',LAST_NAME,' ',LAST_NAME_PREFIX) AS SUP_NAME FROM employed ORDER BY SUP_NAME ASC;";
                                $resultSupAux = $mysqli->query($sqlSupAux);

                                while($rowSupAux = $resultSupAux->fetch_assoc()) {
                                    echo "<option value='".$rowSupAux['ID_NOM']."'>".$rowSupAux['ID_NOM']. " - " . $rowSupAux['SUP_NAME']."</option>";
                                }
                                ?>
                            </select>
                        </div>                  

                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm" style="width: 200px;" type="submit">Nuevo</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="admin_users.php?id=<?php echo $user_active ?>" class="btn btn-secondary btn-sm" style="width: 200px;">Regresar</a>
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
        //Obtener el campus por la ciudad
        $(document).ready(function(){
            $("#city").change(function () {             
                $("#city option:selected").each(function () {
                    city = $(this).val();
                    $.post("administrative/includes/getCampus.php", { city: city }, function(data){
                        $("#nomSesion").html(data);
                    });            
                });
            })
        });

        //Obtener el el departamento por el área
        $(document).ready(function(){
            $("#area").change(function () {                
                $("#area option:selected").each(function () {
                    area = $(this).val();
                    $.post("administrative/includes/getDepartment.php", { area: area }, function(data){
                        $("#department").html(data);
                    });            
                });
            })
        });
        //Obtener la posición 
        $(document).ready(function(){
            let area = ""; // Definir las variables globalmente
            let department = ""; 

            // Obtener el departamento por el área
            $("#area").change(function () {                
                area = $(this).val(); // Guardar el valor en la variable global
                $.post("administrative/includes/getDepartment.php", { area: area }, function(data){
                    $("#department").html(data);
                });            
            });

            // Guardar el departamento cuando se seleccione
            $("#department").change(function(){
                department = $(this).val(); // Guardar el valor en la variable global
            });

            // Obtener la posición cuando se seleccione un puesto
            $("#job").change(function () {               
                let job = $(this).val(); // Obtener el valor del puesto
                
                // Verificar si area y department tienen valores
                if(area && department) {
                    $.post("administrative/includes/getPosition.php", { job: job, department: department, area: area }, function(data){
                        $("#position").html(data);
                    });
                } else {
                    console.error("Falta seleccionar área y/o departamento antes de elegir el puesto.");
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