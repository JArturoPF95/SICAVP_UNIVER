
<?php
	require ('../../../logic/conn.php');
	
	$area = $_POST['area'];
    $department = $_POST['department'];
    $job = $_POST['job'];
		
	$sqlPosition = "SELECT DISTINCT CODE_POSITION, POSITION_DESCRIPTION FROM code_position 
        WHERE CODE_AREA = '$area' AND CODE_DEPARTMENT = '$department' AND CODE_JOB = '$job'
        ORDER BY POSITION_DESCRIPTION ASC;";
	$resultPosition = $mysqli -> query($sqlPosition);
    $position = $resultPosition -> num_rows;
	
	$html= "<option selected disabled>Seleccionar Posición</option>";
	
	while($rowPosition = $resultPosition -> fetch_assoc())
	{
		$html.= "<option value='".$rowPosition['CODE_POSITION']."'>".$rowPosition['POSITION_DESCRIPTION']."</option>";
	}
	
	echo $html;
?>