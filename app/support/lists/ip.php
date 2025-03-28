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

$send = 3;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $send = $_POST['send'];
}

//Obtenemos IP
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

//Query Campus
$sqlGetSesion = "SELECT DISTINCT PK, NOM_SESION, CITY FROM code_sesion ORDER BY CITY, NOM_SESION ASC";
$resultGetSesion = $mysqli->query($sqlGetSesion);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puestos</title>
    <script src="../../../static/js/popper.min.js"></script>
    <script src="../../../static/js/bootstrap.min.js"></script>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/styles/tables.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <!--Header con menú de opciones-->
    <header>
        <div class="px-3 py-2 border-bottom">
            <div class="px-3 mb-3">
                <div class="container d-flex flex-wrap justify-content-end">
                    <div class="text-end">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                            <!--Agregamos una IP-->
                            <button name="send" type="submit" value="1" class="btn btn-primary btn-sm">
                                Nueva IP &nbsp; <i class="bi bi-database-fill-up"></i>
                            </button>
                            <!--Obtenemos la IP-->
                            <!--button name="send" type="submit" value="2" class="btn btn-primary btn-sm">
                                Obtener IP &nbsp; <i class="bi bi-router-fill"></i>
                            </button-->
                            <!--Listado de IPs-->
                            <button name="send" type="submit" value="3" class="btn btn-primary btn-sm">
                                IPs &nbsp; <i class="bi bi-wifi"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <h4 class="my-3">Direcciones IP</h4>
    <div class="container align-items-center"> <!--Div Principal-->
        <?php
        if ($send == 3) {
            $sqlGetIP = "SELECT IP.IP, IP.ipID, IP.IP_NAME, SES.CITY, SES.NOM_SESION, SES.PK FROM code_ip IP
            INNER JOIN code_sesion SES ON IP.CODE_SESION = SES.CODE_SESION_NOM AND IP.CODE_CITY = SES.CODE_CITY
            LEFT OUTER JOIN code_sesion_academic CSA ON IP.CODE_SESION = CSA.CODE_NOM";
            $resultGetIP = $mysqli->query($sqlGetIP);
            if ($resultGetIP->num_rows > 0) {
        ?>

                <table id="myTable" class="table table-hover table-bordered" style="font-size: 13px;">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" class="text-white table-primary">Ubicación</th>
                            <th scope="col" class="text-white table-primary">Dirección IP</th>
                            <th scope="col" class="text-white table-primary">Nombre</th>
                            <th scope="col" class="text-white table-primary">Actualizar</th>
                            <th scope="col" class="text-white table-primary">Eliminar</th>
                        </tr>
                    </thead>
                    <?php
                    while ($rowIP = $resultGetIP->fetch_assoc()) {
                            $ipNomSesion =    $rowIP['NOM_SESION'];
                            $ipNomCity =    $rowIP['CITY'];
                            $ip =    $rowIP['IP'];
                            $ipName =    $rowIP['IP_NAME'];
                            $sesPK = $rowIP['PK'];
                    ?>
                        <tbody class="text-center">
                            <tr>
                                <td><?php echo $ipNomSesion . ' - ' . $ipNomCity ?></td>
                                <td><?php echo $ip ?></td>
                                <td><?php echo $ipName ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-database-fill-gear"></i>
                                        </button>
                                        <div class="dropdown-menu p-4 text-body-secondary" style="width: 350px;">
                                            <p>
                                                Actualizar IP.
                                            </p>
                                            <form class="row g-3" method="post" action="process/updateIP.php">
                                                <input hidden type="text" name="idIP" value="<?php echo $rowIP['ipID'] ?>">
                                                <div class="mb-3">
                                                    <label for="exampleInputEmail1" class="form-label">Nombre</label>
                                                    <input disabled type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?php echo $rowIP['IP_NAME'] ?>" style="font-size: 13px;">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="exampleInputPassword1" class="form-label">Dirección IP</label>
                                                    <input type="text" name="ip" class="form-control" id="exampleInputEmail1" value="<?php echo $rowIP['IP'] ?>" style="font-size: 13px;">
                                                </div>
                                                <div class="mb-3 form-check">
                                                    <label class="form-check-label" for="exampleCheck1">Ubicación</label>
                                                    <select class="form-select" name="sesion" aria-label="Default select example">
                                                        <option selected style="font-size: 13px;" value="<?php echo $sesPK?> ><?php echo $rowIP['NOM_SESION'] . ' - ' . $rowIP['CITY'] ?></option>
                                                        <?php
                                                        $sqlGetSesionUp = "SELECT DISTINCT PK, NOM_SESION, CITY FROM code_sesion WHERE PK != '$sesPK' AND CODE_SESION_NOM != '00000' ORDER BY CITY, NOM_SESION ASC";
                                                        $resultGetSesionUp = $mysqli->query($sqlGetSesionUp);
                                                        if ($resultGetSesionUp->num_rows > 0) {
                                                            while ($rowSesionUp = $resultGetSesionUp->fetch_assoc()) {
                                                        ?>
                                                                <option value="<?php echo $rowSesionUp['PK'] ?>" style="font-size: 13px;"><?php echo $rowSesionUp['NOM_SESION'] . ' - ' . $rowSesionUp['CITY'] ?></option>
                                                        <?php
                                                            }
                                                        } else {
                                                            echo '<option disabled>Sin Información</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a type="button" class="btn btn-primary btn-sm" href="process/deleteIP.php?id=<?php echo $rowIP['ipID'] ?>">
                                        <!--Borrar &nbsp; -->
                                        <i class="bi bi-wifi-off"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    <?php
                    }
                    ?>
                </table>
            <?php
            } else {
            ?>
                <div class="alert alert-warning d-flex align-items-center fs-1 fw-bolder text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div class="text-end fs-4">Aún no tiene IPs Registradas en sistema</div>
                </div>
            <?php
            } //Cierra IF Consulta Tabla

        } elseif ($send == 2) {
            ?>
            <div class="card text-bg-secondary mb-3 align-items-center" style="max-width: 18rem;">
                <div class="card-header"> Dirección IP </div>
                <div class="card-body">
                    <p class="card-text"><?php echo $ip ?> </p>
                </div>
            </div>
        <?php
        } elseif ($send == 1) {
        ?>
            <form class="row g-3" method="post" action="process/insertIP.php">
                <fieldset>
                    <div class="row g-3">
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Nombre IP</label>
                            <input type="text" name="ipName" id="disabledTextInput" class="form-control" placeholder="Total Play Corporativo 1" required>
                        </div>
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Dirección IP</label>
                            <input type="text" name="ip" id="disabledTextInput" class="form-control" placeholder="000.000.000.000" required>
                        </div>
                        <div class="col">
                            <label for="disabledSelect" class="form-label">Ubicación</label>
                            <select id="disabledSelect" name="sesion" class="form-select" required>
                                <?php
                                $sqlGetSesionIn = "SELECT DISTINCT PK, NOM_SESION, CITY FROM code_sesion WHERE PK != '$sesPK' AND CODE_SESION_NOM != '00000' ORDER BY CITY, NOM_SESION ASC";
                                $resultGetSesionIn = $mysqli->query($sqlGetSesionIn);
                                if ($resultGetSesionIn->num_rows > 0) {
                                    while ($rowSesionIn = $resultGetSesionIn->fetch_assoc()) {
                                        $ipID = $rowSesionIn['idIP'];
                                ?>
                                        <option value="<?php echo $rowSesionIn['PK'] ?>"><?php echo $rowSesionIn['NOM_SESION'] . ' - ' . $rowSesionIn['CITY'] ?></option>
                                <?php
                                    }
                                } else {
                                    echo '<option disabled>Sin Información</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col py-2">
                        <button type="submit" class="btn btn-primary px-3">Crear</button>
                    </div>
    </div>
    </fieldset>
    </form>
<?php
        }

?>
</div> <!--Cierra Div Principal-->
</body>

</html>