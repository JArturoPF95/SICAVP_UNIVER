
<?php
	require ('../../../logic/conn.php');
	
	$area = $_POST['area'];
		
	$sqlDepartment = "SELECT DISTINCT CODE_DEPRTMENT, DEPARTMENT FROM code_department WHERE CODE_AREA = '$area' ORDER BY DEPARTMENT";
	$resultDepartment = $mysqli -> query($sqlDepartment);
    $department = $resultDepartment -> num_rows;
	
	$html= "<option selected disabled>Seleccionar Departmento</option>";
	
	while($rowDepartment = $resultDepartment -> fetch_assoc())
	{
		$html.= "<option value='".$rowDepartment['CODE_DEPRTMENT']."'>".$rowDepartment['DEPARTMENT']."</option>";
	}
	
	echo $html;
?>