<?php
 
            /** Captura asistencia si aún no llega al tiempo de retardo */
            $flag_left_ant = 0;
            $delays = 0;

            if ($entrada <= $incidence_delay_time){

                //echo 'Entra con Asistencia';
                $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'ASISTENCIA';";
                $resultIncidence = $mysqli -> query($sqlIncidence);
                if ($resultIncidence -> num_rows > 0) {
                    while ($rowIn = $resultIncidence -> fetch_assoc()) {
                        $incidence = $rowIn['CODE_TINC'];
                    }
                }
                /** Valida la asistencia si entra entre el tiempo de retardo y de ausencia */
            } elseif ($entrada > $incidence_delay_time && $entrada <= $incidence_ausence_time) {
                //echo 'Entra con Retardo';
                //Validamos los retardos y faltas por retardos de la quincena en cuestión
                $sql_countDelays_BIO;
                $result_countDelays = $mysqli->query($sql_countDelays_BIO);
                if ($result_countDelays->num_rows > 0) {
                    while ($row_countDelay = $result_countDelays->fetch_assoc()) {
                        $delay_incidence = $row_countDelay['TINC'];

                        $delays++;
                        
                        /** Cuenta los retardos, si ya lleva 2, en la tercera (que es esta) ya genera falta por retardos */
                        if ((($delays + 1) % 3) == 0) {
                            $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'FALTA POR RETARDOS';";
                                $resultIncidence = $mysqli -> query($sqlIncidence);
                                if ($resultIncidence -> num_rows > 0) {
                                while ($rowIn = $resultIncidence -> fetch_assoc()) {
                                    $incidence = $rowIn['CODE_TINC'];
                                }
                            }
                        // Si apun no cuenta con  los retardos procede a cargar un retardo.
                        } else {
                            $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'RETARDO';";
                            $resultIncidence = $mysqli -> query($sqlIncidence);
                            if ($resultIncidence -> num_rows > 0) {
                                while ($rowIn = $resultIncidence -> fetch_assoc()) {
                                    $incidence = $rowIn['CODE_TINC'];

                                }
                            }
                        }

                    }
                    /** No cuenta aún con retardos, es el primero */
                } else {
                    $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'RETARDO';";
                    $resultIncidence = $mysqli -> query($sqlIncidence);
                    if ($resultIncidence -> num_rows > 0) {
                        while ($rowIn = $resultIncidence -> fetch_assoc()) {
                            $incidence = $rowIn['CODE_TINC'];

                        }
                    }
                }

                /** Si el tiempo ya excede el retardo se genera "Falta Injustificada" */
            } elseif ($entrada > $incidence_ausence_time) {
                //echo 'Entra con Falta';
                $sqlIncidence = "SELECT DISTINCT CODE_TINC FROM code_incidence WHERE DESCRIP_TINC = 'FALTA INJUSTIFICADA';";
                $resultIncidence = $mysqli -> query($sqlIncidence);
                if ($resultIncidence -> num_rows > 0) {
                    while ($rowIn = $resultIncidence -> fetch_assoc()) {
                        $incidence = $rowIn['CODE_TINC'];
                    }
                }
            }
       
        //echo $incidence;

        /* Inserta el registro 
        $sql_insert_check = "INSERT INTO admin_attendance (NOM_ID, CODE_DAY, ATTENDANCE_DATE, ATTENDANCE_TIME, TINC, IN_OUT, BIO_SIC_FLAG) VALUES ('$user_active','$codeDay','$fecha','$entrada','$incidence','$check', 'S')";
        if ($mysqli->query($sql_insert_check) === true) {
            if ($check == 1) {
                $message_check = 'Entrada Registrada con éxito';
                $icon_check = 'success';
            } else {
                $message_check = 'Salida Registrada con éxito';
                $icon_check = 'success';
            }
        } else {
            $message_check = 'No se pudo Registrar';
            $icon_check = 'error';
        } 

    */

?>