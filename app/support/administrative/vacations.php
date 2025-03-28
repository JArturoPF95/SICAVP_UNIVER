<?php
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

require '../../logic/conn.php';

$idUser = $_GET['id'];
$term = date('Y');

$sqlGetName = "SELECT NAME, LAST_NAME, LAST_NAME_PREFIX FROM employed WHERE ID_NOM = '$idUser'";
$resultName = $mysqli ->query($sqlGetName);
if ($resultName->num_rows > 0) {
    while ($rowName = $resultName->fetch_assoc()) {
        $name = $rowName['NAME'];
        $last_name = $rowName['LAST_NAME'];
        $last_name_prefix = $rowName['LAST_NAME_PREFIX'];
    }
}

$empName = $name.' '.$last_name.' '.$last_name_prefix;

$sql_myRequest = "SELECT 
        VRE.ID_NOM, VRE.REQUEST_DATE, VRE.START_DATE, VRE.REQUEST_TERM,
        VRE.END_DATE, VRE.DAYS_REQUESTED, VRE.AUTHORIZATION_FLAG, VRE.requestId
    FROM vacation_request VRE 
    WHERE DAYS_REQUESTED != 0 AND VRE.ID_NOM = '$idUser' AND VRE.REQUEST_TERM >= '$term'";
$result_myRequest = $mysqli->query($sql_myRequest);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solcitar Vacaciones</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <div class="container"> <!--Div Inicial-->
        
        <!--Mis Solicitudes-->
        <hr class="my-4">
        <h4 class="mb-3">Solicitudes de Vacaciones: <?php echo $empName ?></h4>
        <div class="row my-4">
            <?php

            if ($result_myRequest->num_rows > 0) {
            ?>

                <table class="table table-hover table-bordered table-sm">
                    <thead class="text-white text-center table-primary">
                        <tr>
                            <th scope="col">Periodo</th>
                            <th scope="col">Solicitadas el</th>
                            <th scope="col">Fecha de Inicio</th>
                            <th scope="col">Fecha de Fin</th>
                            <th scope="col">Días</th>
                            <th scope="col">Autorización</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>

                    <?php

                    while ($row_MyReq = $result_myRequest->fetch_assoc()) {
                        $myRequest_Term = $row_MyReq['REQUEST_TERM'];
                        $myRequest_Date = $row_MyReq['REQUEST_DATE'];
                        $myRequest_Start = $row_MyReq['START_DATE'];
                        $myRequest_End = $row_MyReq['END_DATE'];
                        $myRequest_Days = $row_MyReq['DAYS_REQUESTED'];
                        $myRequest_Authorization = $row_MyReq['AUTHORIZATION_FLAG'];
                        $myRequest_Id = $row_MyReq['requestId'];

                        if ($myRequest_Authorization == 0) {
                            $authorization = 'Pendiente';
                            $background = 'table-warning';
                        } elseif ($myRequest_Authorization == 1) {
                            $authorization = 'Autorizado';
                            $background = 'table-success';
                        } else {
                            $authorization = 'Rechazado';
                            $background = 'table-danger';
                        }
                    ?>

                        <tbody class="text-center <?php echo $background ?>">
                            <tr>
                                <td hidden scope="col"><?php echo $myRequest_Id ?></td>
                                <td scope="col"><?php echo $myRequest_Term ?></td>
                                <td scope="col"><?php echo date('d/m/Y', strtotime($myRequest_Date)) ?></td>
                                <td scope="col"><?php echo date('d/m/Y', strtotime($myRequest_Start)) ?></td>
                                <td scope="col"><?php echo date('d/m/Y', strtotime($myRequest_End)) ?></td>
                                <td scope="col"><?php echo $myRequest_Days ?></td>
                                <td scope="col"><?php echo $authorization ?></td>
                                <?php
                                if ($myRequest_Authorization == 1 || $myRequest_Authorization == 2) {
                                ?>
                                    <td scope="col">
                                        <a href="download.php?id=<?php echo $myRequest_Id ?>" type="button" class="btn btn-sm">
                                            <i class="bi bi-file-earmark-arrow-down-fill" style="color: #000"></i>
                                        </a>
                                    </td>
                                <?php
                                } else {
                                ?>
                                    <td scope="col">
                                        <!--div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <button type="submit" name="authorization" value="1" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="<?php echo $teamRequest_id ?>">Sí</button>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type="submit" name="authorization" value="2" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="<?php echo $teamRequest_id ?>">No</button>
                                        </div-->
                                    </td>
                                <?php
                                }
                                ?>
                            </tr>
                        </tbody>

                    <?php
                    }
                    ?>
                </table>
            <?php
                //En caso de que aún no haya realizado solicitudes
            } else {
            ?>
                <div class="alert alert-success d-flex align-items-center fs-1 fw-bolder text-center row my-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>El usuario aún no ha realizado solicitudes de Vacaciones</div>
                </div>
            <?php
            }

            ?>
        </div> <!--Cierra Div mis solicitudes-->
        <div class="row my-4">
            <div class="col-12">
                <a href="../admin_users.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill"></i> &nbsp; Volver</a>
            </div>
        </div>
    </div>

</body>

</html>