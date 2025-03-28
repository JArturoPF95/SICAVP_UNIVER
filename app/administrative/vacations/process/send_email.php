<?php
// Cargar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluye los archivos necesarios (ajusta la ruta si descargaste manualmente)
// require 'vendor/autoload.php'; // Si usas Composer
require '../../../lib/PHPMailer/src/PHPMailer.php';
require '../../../lib/PHPMailer/src/SMTP.php';
require '../../../lib/PHPMailer/src/Exception.php';


require '../../logic/conn.php';

$sqlGetEmail = "SELECT DISTINCT USR.SICAVP_USER, CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX) EMPNAME
    , USR.EMAIL USR_MAIL, EMP.SUPERVISOR_ID, EMP.SUPERVISOR_NAME, USR2.EMAIL SUP_EMAIL
    , EMP.SUPERVISOR_ID_AUX,  CONCAT(EMP2.NAME,' ',EMP2.LAST_NAME,' ',EMP2.LAST_NAME_PREFIX) SUP_AUX_NAME, USR3.EMAIL SUP_AUX_EMAIL
    FROM users USR
    INNER JOIN employed EMP ON EMP.ID_NOM = USR.SICAVP_USER
    LEFT OUTER JOIN users USR2 ON USR2.SICAVP_USER = EMP.SUPERVISOR_ID
    LEFT OUTER JOIN employed EMP2 ON EMP2.ID_NOM = EMP.SUPERVISOR_ID_AUX
    LEFT OUTER JOIN users USR3 ON USR3.SICAVP_USER = EMP.SUPERVISOR_ID_AUX
    WHERE USR.SICAVP_USER = '$user_active';";
$resultGetEmail = $mysqli -> query($sqlGetEmail);
if ($resultGetEmail -> num_rows > 0) {
    while ($rowMail = $resultGetEmail -> fetch_assoc()) {
        $myMail = $rowMail['USR_MAIL'];
        $myName = $rowMail['EMPNAME'];
        $supMail = $rowMail['SUP_EMAIL'];
        $supName = $rowMail['SUPERVISOR_NAME'];
        
        $supAuxMail = $rowMail['SUP_AUX_EMAIL'];
        $supAuxName = $rowMail['SUP_AUX_NAME'];
    }
}

$startDay = date('d/m/Y', strtotime($first_day));
$endDay = date('d/m/Y', strtotime($last_day));

if ($supMail !== '' || $supMail !== NULL) {
    // Crear una instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor (Se requiere generar una App Password con Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sicavp.notificaciones@univer-gdl.edu.mx'; // Correo que envía las notificaciones, es temporal (se agrega el indicado por la institución)
        $mail->Password   = 'drjsoivztaudvjyc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encriptación
        $mail->Port       = 587; // Puerto SMTP

        // Configuración del correo
        $mail->setFrom('sicavp.notificaciones@univer-gdl.edu.mx', 'SICAVP. Solicitud de Vacaciones'); // Remitente
        if ($supAuxMail == '' || $supAuxMail == NULL) {
            $mail->addAddress($supMail, $supName); // Destinatario
        } else {
            $mail->addAddress($supMail, $supName); // Destinatario
            $mail->addAddress($supAuxMail, $supAuxName); //Destinatario #2 (Supervisor Auxiliar)
        }

        // Contenido del correo
        $mail->isHTML(true); // Activar HTML
        $mail->Subject = "SICAVP Solicitud Vacaciones {$myName}"; // Asunto
        $mail->Body    = "<h4>Por medio del presente, le notificamos que se ha registrado una solicitud de vacaciones para el colaborador {$myName}. <br>

                            Período de vacaciones: Del {$startDay} al {$endDay}. <br>
                            Esta solicitud se encuentra pendiente de su revisión y aprobación/rechazo en SICAVP. <br>
                            Visitar <a href='https://sicavp.univer-gdl.info/app/'>SICAVP</a></h4>
                            <br><br><br>
                            <p>Este correo es de carácter informativo. Por lo que no es necesario responder el mismo.</p>";
        $mail->AltBody = '<pEste correo es de carácter informativo. Por lo que no es necesario responder el mismo.</p>'; // Texto alternativo

        // Enviar correo
        $mail->send();
        //echo '¡Correo enviado exitosamente!';
    } catch (Exception $e) {
        echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
    }
}
?>
