
<?php
	require ('../../../logic/conn.php');
	
	$city = $_POST['city'];
		
	$sqlCampus = "SELECT DISTINCT CODE_SESION_NOM, NOM_SESION FROM code_sesion WHERE CODE_CITY = '$city' ORDER BY NOM_SESION";
	$resultCampus = $mysqli -> query($sqlCampus);
    $campus = $resultCampus -> num_rows;
	
	$html= "<option selected disabled>Seleccionar Campus</option>";
	
	while($rowCampus = $resultCampus -> fetch_assoc())
	{
		$html.= "<option value='".$rowCampus['CODE_SESION_NOM']."'>".$rowCampus['NOM_SESION']."</option>";
	}
	
	echo $html;
?>