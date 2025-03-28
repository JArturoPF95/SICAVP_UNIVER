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
    $id_nom = $_GET['u'];
}

$sqlGetBio = "SELECT DISTINCT EMP.ID_NOM
, CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) NAME_USER
, MAP.ID_BIOUNIVER
FROM employed EMP
LEFT OUTER JOIN mapping_bioadminemploy MAP ON MAP.ID_NOM2001 = EMP.ID_NOM
WHERE EMP.ID_NOM = '$id_nom'";
$resultGetBio = $mysqli->query($sqlGetBio);
if ($resultGetBio->num_rows > 0) {
    while ($rowGetBio = $resultGetBio->fetch_assoc()) {
        $name_user = $rowGetBio['NAME_USER'];
        $idBio = $rowGetBio['ID_BIOUNIVER'];
        if ($idBio == '') {
            $flagBio = 0;
        } else {
            $flagBio = 1;
        }
    }
}



?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Biotime</title>
    <script src="../../../static/js/sweetalert.min/sweetalert.min.js"></script>
    <link rel="stylesheet" href="../../../static/css/bootstrap.css">
    <link rel="stylesheet" href="../../../static/css/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
<form class="row row-cols-lg-auto g-3 align-items-center" method="POST" action="updBio.php">
<fieldset>
<legend>Actualizar Usuario del Biotime</legend>
<input hidden type="text" name="send" value="1">
<input hidden type="text" name="idNom" value="<?php echo $id_nom ?>">
<input hidden type="text" name="nameUser" value="<?php echo $name_user ?>">
<input hidden type="text" name="flagBio" value="<?php echo $flagBio ?>">
  <div class="col-12">
    <div class="input-group">
      <div class="input-group-text"><?php echo $name_user ?></div>
      <input type="text" class="form-control" name="bioUser" id="inlineFormInputGroupUsername" value="<?php echo $idBio ?>">
    </div>
  </div>

  <div class="col-12 my-2">
  <a href="../admin_users.php?id=<?php echo $user_active ?>" class="btn btn-secondary"><i class="bi bi-arrow-left-circle-fill"></i> &nbsp; Volver</a>
    <button type="submit" class="btn btn-primary">Actualizar</button>
  </div>
  </fieldset>
</form>

</body>

</html>