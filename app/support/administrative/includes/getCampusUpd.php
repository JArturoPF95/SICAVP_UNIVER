<?php
require ('../../../logic/conn.php');

$city = $_POST['city'];
$nomSesion = $_POST['nomSesion']; // Asegurar que el valor actual se envía desde JS

$html = "<option selected disabled>Seleccionar Campus</option>";

// Consultar los campus de la ciudad seleccionada
$sqlCampus = "SELECT DISTINCT CODE_SESION_NOM, NOM_SESION FROM code_sesion WHERE CODE_CITY = '$city' ORDER BY NOM_SESION";
$resultCampus = $mysqli->query($sqlCampus);

while ($rowCampus = $resultCampus->fetch_assoc()) {
    // Si el campus coincide con el seleccionado actualmente, marcarlo como "selected"
    $selected = ($rowCampus['CODE_SESION_NOM'] == $nomSesion) ? "selected" : "";
    $html .= "<option $selected value='".$rowCampus['CODE_SESION_NOM']."'>".$rowCampus['NOM_SESION']."</option>";
}

echo $html;
?>