
<?php
	require ('../../../logic/conn.php');
	
	$scheduleGroup = $_POST['scheduleGroup'];
	$daytrip = $_POST['daytrip'];

	$html = "<option selected disabled>Seleccionar Jornada</option>";
		
	$sqlDaytrip = "SELECT DISTINCT CODE_DAYTRIP, DAYTRIP FROM groups_daytrip WHERE CODE_GROUP = '$scheduleGroup' ORDER BY DAYTRIP ASC;";
	$resultDaytrip = $mysqli -> query($sqlDaytrip);
    //$daytrip = $resultDaytrip -> num_rows;
	
	while($rowDaytrip = $resultDaytrip -> fetch_assoc())
	{
		$selected = ($rowDaytrip['CODE_DAYTRIP'] == $daytrip) ? "selected" : "";
		$html.= "<option $selected value='".$rowDaytrip['CODE_DAYTRIP']."'>".$rowDaytrip['DAYTRIP']."</option>";
	}
	
	echo $html;
?>