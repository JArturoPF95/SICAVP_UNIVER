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

$sqlSchedulesTable = "SELECT DISTINCT CSC.CODE_NOM, CSC.DAYTRIP
    , IFNULL( (SELECT DISTINCT ASH.TIME_START FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '1'), '') LunesIn
    , IFNULL( (SELECT DISTINCT ASH.TIME_START FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '2'), '') MartesIn
    , IFNULL( (SELECT DISTINCT ASH.TIME_START FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '3'), '') MiercolesIn
    , IFNULL( (SELECT DISTINCT ASH.TIME_START FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '4'), '') JuevesIn
    , IFNULL( (SELECT DISTINCT ASH.TIME_START FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '5'), '') ViernesIn
    , IFNULL( (SELECT DISTINCT ASH.TIME_START FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '6'), '') SábadoIn
    , IFNULL( (SELECT DISTINCT ASH.TIME_START FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '0'), '') DomingoIn
    , IFNULL( (SELECT DISTINCT ASH.OUT_TIME FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '1'), '') LunesOut
    , IFNULL( (SELECT DISTINCT ASH.OUT_TIME FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '2'), '') MartesOut
    , IFNULL( (SELECT DISTINCT ASH.OUT_TIME FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '3'), '') MiercolesOut
    , IFNULL( (SELECT DISTINCT ASH.OUT_TIME FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '4'), '') JuevesOut
    , IFNULL( (SELECT DISTINCT ASH.OUT_TIME FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '5'), '') ViernesOut
    , IFNULL( (SELECT DISTINCT ASH.OUT_TIME FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '6'), '') SábadoOut
    , IFNULL( (SELECT DISTINCT ASH.OUT_TIME FROM admin_schedules ASH WHERE ASH.CODE_SCHEDULE = CSC.CODE_NOM AND ASH.CODE_DAY = '0'), '') DomingoOut
    FROM code_schedule CSC;";
$resultSchedulesTable = $mysqli -> query($sqlSchedulesTable);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoario</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
</head>

<body>
    <div class="container-fluid" style="width: 100%; height: 100%">
        <h4 class="mb-3">Horarios Disponibles</h4>

        <?php 
        if ($resultSchedulesTable -> num_rows > 0) {
        ?>
        <table class="table my-3 table-hover table-bordered" style="font-size: 13px;">
        <thead>
            <tr class="text-white text-center table-primary">
                <th scope="col">Nombre</th>
                <th scope="col">Lunes</th>
                <th scope="col">Martes</th>
                <th scope="col">Miércoles</th>
                <th scope="col">Jueves</th>
                <th scope="col">Viernes</th>
                <th scope="col">Sábado</th>
                <th scope="col">Domingo</th>
            </tr>
        </thead>

        <?php
            while ($rowSchedules = $resultSchedulesTable -> fetch_assoc()) {
                $schedulesCode = $rowSchedules['CODE_NOM'];
                $schedulesDaytrip = $rowSchedules['DAYTRIP'];
                $schedulesLunesIn = $rowSchedules['LunesIn'];
                $schedulesLunesOut = $rowSchedules['LunesOut'];
                $schedulesMartesIn = $rowSchedules['MartesIn'];
                $schedulesMartesOut = $rowSchedules['MartesOut'];
                $schedulesMiercolesIn = $rowSchedules['MiercolesIn'];
                $schedulesMiercolesOut = $rowSchedules['MiercolesOut'];
                $schedulesJuevesIn = $rowSchedules['JuevesIn'];
                $schedulesJuevesOut = $rowSchedules['JuevesOut'];
                $schedulesViernesIn = $rowSchedules['ViernesIn'];
                $schedulesViernesOut = $rowSchedules['ViernesOut'];
                $schedulesSábadoIn = $rowSchedules['SábadoIn'];
                $schedulesSábadoOut = $rowSchedules['SábadoOut'];
                $schedulesDomingoIn = $rowSchedules['DomingoIn'];
                $schedulesDomingoOut = $rowSchedules['DomingoOut'];
        ?>
            <tbody>
                <tr class="text-center">
                    <td><?php  echo $schedulesDaytrip ?></td>
                    <td><?php if($schedulesLunesIn != '') { echo substr( $schedulesLunesIn, 0, 5) . ' a ' . substr( $schedulesLunesOut, 0, 5); } else { echo ''; }?></td>
                    <td><?php if($schedulesMartesIn != '') { echo substr( $schedulesMartesIn, 0, 5) . ' a ' . substr( $schedulesMartesOut, 0, 5); } else { echo ''; }?></td>
                    <td><?php if($schedulesMiercolesIn != '') { echo substr( $schedulesMiercolesIn, 0, 5) . ' a ' . substr( $schedulesMiercolesOut, 0, 5); } else { echo ''; }?></td>
                    <td><?php if($schedulesJuevesIn != '') { echo substr( $schedulesJuevesIn, 0, 5) . ' a ' . substr( $schedulesJuevesOut, 0, 5); } else { echo ''; }?></td>
                    <td><?php if($schedulesViernesIn != '') { echo substr( $schedulesViernesIn, 0, 5) . ' a ' . substr( $schedulesViernesOut, 0, 5); } else { echo ''; }?></td>
                    <td><?php if($schedulesSábadoIn != '') { echo substr( $schedulesSábadoIn, 0, 5) . ' a ' . substr( $schedulesSábadoOut, 0, 5); } else { echo ''; }?></td>
                    <td><?php if($schedulesDomingoIn != '') { echo substr( $schedulesDomingoIn, 0, 5) . ' a ' . substr( $schedulesDomingoOut, 0, 5); } else { echo ''; }?></td>
                </tr>
            </tbody>
        <?php                              
            }
        ?>
        </table>
        <?php
        }
        ?>


    </div>
</body>
</html>