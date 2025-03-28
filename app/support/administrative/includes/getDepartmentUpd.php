
<?php
	require ('../../../logic/conn.php');
	
	$area = $_POST['area'];
	$department = $_POST['department'];
	
	$html= "<option selected disabled>Seleccionar Departmento</option>";
		
	$sqlDepartment = "SELECT DISTINCT CODE_DEPRTMENT, DEPARTMENT FROM code_department WHERE CODE_AREA = '$area' ORDER BY DEPARTMENT";
	$resultDepartment = $mysqli -> query($sqlDepartment);
    //$department = $resultDepartment -> num_rows;
	
	while($rowDepartment = $resultDepartment -> fetch_assoc())
	{
		$selected = ($rowDepartment['CODE_DEPRTMENT'] == $department) ? "selected" : "";
		$html.= "<option $selected value='".$rowDepartment['CODE_DEPRTMENT']."'>".$rowDepartment['DEPARTMENT']."</option>";
	}
	
	echo $html;
?>