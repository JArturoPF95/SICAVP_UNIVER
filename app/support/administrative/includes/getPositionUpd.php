
<?php
	require ('../../../logic/conn.php');
	
	$area = $_POST['area'];
    $department = $_POST['department'];
    $job = $_POST['job'];
	$position = $_POST['position'];
	
	$html= "<option selected disabled>Seleccionar Posición</option>";
		
	$sqlPosition = "SELECT DISTINCT CODE_POSITION, POSITION_DESCRIPTION FROM code_position 
        WHERE CODE_AREA = '$area' AND CODE_DEPARTMENT = '$department' AND CODE_JOB = '$job'
        ORDER BY POSITION_DESCRIPTION ASC;";
	$resultPosition = $mysqli -> query($sqlPosition);
    //$position = $resultPosition -> num_rows;
	
	while($rowPosition = $resultPosition -> fetch_assoc())
	{
		$selected = ($rowPosition['CODE_POSITION'] == $position) ? "selected" : "";
		$html.= "<option $selected value='".$rowPosition['CODE_POSITION']."'>".$rowPosition['POSITION_DESCRIPTION']."</option>";
	}
	
	echo $html;
?>