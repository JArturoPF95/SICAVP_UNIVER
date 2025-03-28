
<?php
	require ('../../../logic/conn.php');
	
	$scheduleGroup = $_POST['scheduleGroup'];
		
	$sqlDaytrip = "SELECT DISTINCT CODE_DAYTRIP, DAYTRIP FROM groups_daytrip WHERE CODE_GROUP = '$scheduleGroup' ORDER BY DAYTRIP ASC;";
	$resultDaytrip = $mysqli -> query($sqlDaytrip);
    $daytrip = $resultDaytrip -> num_rows;
	
	$html= "<option selected disabled>Seleccionar Jornada</option>";
	
	while($rowDaytrip = $resultDaytrip -> fetch_assoc())
	{
		$html.= "<option value='".$rowDaytrip['CODE_DAYTRIP']."'>".$rowDaytrip['DAYTRIP']."</option>";
	}
	
	echo $html;
?>