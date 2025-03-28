<?php
// Cargar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluye los archivos necesarios (ajusta la ruta si descargaste manualmente)
// require 'vendor/autoload.php'; // Si usas Composer
require '../../../lib/PHPMailer/src/PHPMailer.php';
require '../../../lib/PHPMailer/src/SMTP.php';
require '../../../lib/PHPMailer/src/Exception.php';


require '../conn.php';

function decryptId($encryptedId) {
    $key = "mi_clave_secreta";
    return openssl_decrypt(hex2bin($encryptedId), 'aes-256-cbc', $key, 0, substr(md5($key), 0, 16));
}

$usr = decryptId($encryptedId);


$sqlGetEmail = "SELECT DISTINCT USR.SICAVP_USER
    , CASE
        WHEN EMP.NAME IS NOT NULL THEN CONCAT(EMP.NAME,' ',EMP.LAST_NAME,' ',EMP.LAST_NAME_PREFIX)
        WHEN ASH.NAME IS NOT NULL THEN CONCAT(ASH.NAME,' ',ASH.LAST_NAME,' ',ASH.LAST_NAME_PREFIX)
        WHEN USR.ACCESS_LEVEL = 3 THEN CONCAT('ADMINISTRADOR ',USR.SICAVP_USER)
        ELSE CONCAT('SUPERVISOR DOCENTE ',USR.SICAVP_USER)
        END EMPNAME
    , USR.EMAIL USR_MAIL
    FROM users USR
    LEFT OUTER JOIN employed EMP ON EMP.ID_NOM = USR.SICAVP_USER
    LEFT OUTER JOIN academic_schedules ASH ON ASH.PERSON_CODE_ID = USR.SICAVP_USER
    WHERE USR.SICAVP_USER = '$usr';";
$resultGetEmail = $mysqli -> query($sqlGetEmail);
if ($resultGetEmail -> num_rows > 0) {
    while ($rowMail = $resultGetEmail -> fetch_assoc()) {
        $myMail = $rowMail['USR_MAIL'];
        $myName = $rowMail['EMPNAME'];
    }
}

//Desencriptamos el ID

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
    $mail->setFrom('sicavp.notificaciones@univer-gdl.edu.mx', 'SICAVP. Confirmacion de acceso'); // Remitente
    $mail->addAddress($myMail, $myName); // Destinatario

    // Contenido del correo
    $mail->isHTML(true); // Activar HTML
    $mail->Subject = "SICAVP Confirmacion correo Institucional"; // Asunto
    $mail->Body    = "<h4>¡Bienvenido!</h4>
                        <p>Confirma tu registro haciendo clic en el siguiente enlace:</p>
                        <p><a href='$confirmationUrl'>Confirmar Registro</a></p>";
    $mail->AltBody = '<pEste correo es de carácter informativo. Por lo que no es necesario responder el mismo.</p>'; // Texto alternativo

    // Enviar correo
    $mail->send();
    //echo '¡Correo enviado exitosamente!';
} catch (Exception $e) {
    echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
}
?>
