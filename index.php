<?php

require 'app/logic/conn.php';

$message_error = '';
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch ($error) {
        case 1:
            $message_error = '
                <div class="alert alert-danger text-center" role="alert">
                Datos incorrectos, verifique sus credenciales
                </div>
                <p class="text-light fs-6 text-center">Si no cuenta con sus datos de acceso pongase en contacto con el área correspondiente</p>';
            break;
        case 2:
            $message_error = '<div class="alert alert-danger text-center" role="alert">
            Datos incorrectos, verifique sus credenciales
            </div>
            <p class="text-light fs-6 text-center">Si no cuenta con sus datos de acceso pongase en contacto con el área correspondiente</p>';
            break;
        case 3:
            $message_error = '<div class="alert alert-danger text-center" role="alert">
                                    Usuario bloqueado <br> Favor de ponerse en contacto con el área correspondiente
                                </div>';
        case 4:
            $message_error = '<div class="alert alert-danger text-center" role="alert">
                                    Correo no confirmado
                                </div>';
        case 5:
            $message_error = '<div class="alert alert-danger text-center" role="alert">
                               No fue posible actualizar información de acceso. Intente de nuevo o contacte al área correspondiente.
                             </div>';
            break;
            //Sólo en caso de que se solicite bloquear ingreso desde ubicaciones no dadas de alta en catálogo
            /*case 4:
            $message_error = '<p class="text-danger fw-bold fs-6">No es posible acceder desde esta ubicación</p>';
            break;*/
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="static/img/fav_sicavp.png" type="image/x-icon">
    <title>SICAVP</title>
    <script src="static/js/popper.min.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="static/css/bootstrap.css">

    <style>
        /**
        footer {
            position: relative;
            margin: 0;
            width: 100%;
        }
        */

        html,
        body {
            height: 100%;
            margin: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            min-height: 75%;
        }

        footer {
            margin-top: 200px;
            /* Margen inferior */
            width: 100%;
        }
    </style>
</head>

<body style="background-color: #022859;">
    <div class="container">

        <div class="row" style="margin-top: 15%;">
            <div class="col-lg-1 col-sm-12"></div>
            <div class="col-lg-5 col-sm-12">
                <div class="row text-center">
                    <div class="col">
                        <img src="static/img/cropped-univer_white.webp" class="img-fluid" width="200">
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col text-center">
                        <img src="static/img/SICAVP_Banner_Login.png" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="col-1 d-flex d-none d-lg-flex d-xl-flex">
                <div class="vr" style="width: 3px; color:#FFF;"></div>
            </div>
            <div class="col-lg-3 col-sm-12">
                <form action="app/logic/session/session.php" method="POST">
                    <div class="mb-3 float-start" style="width: 100%">
                        <label for="inputPassword5" class="form-label text-light">Usuario</label>
                        <input type="text" class="form-control" id="inputPassword5" name="usuario" placeholder="0000001" required min="7">
                    </div>
                    <div class="mb-3 float-start" style="width: 100%">
                        <label for="inputPassword2" class="form-label my-2 text-light">Contraseña</label>
                        <input type="password" class="form-control my-2" id="inputPassword2" name="password" placeholder="*********" required min="6">
                    </div>
                    <button type="submit" class="btn btn-dark my-3  fs-6" style="width: 100%">Acceder</button>

                </form>
                <?php
                if ($message_error != '') {
                ?>
                    <hr style="color:#FFF;">
                    <?php echo $message_error ?>
                <?php
                }
                ?>

            </div>
            <div class="col-lg-3 col-sm-12"></div>
        </div>
    </div>
    <footer class="py-3 my-4">
        <p class="text-center text-light" style="font-size: 11px;">
            <img src="static/img/new_nacerlogo.png" alt="" srcset="" style="width: auto; height: 30px;">
            <br>
            &copy; 2025 Dirección de Tecnologías de la Información
        </p>
    </footer>

    <script>
        function detectarSO() {
            let userAgent = navigator.userAgent || navigator.vendor || window.opera;
            let sistemaOperativo = "Desconocido";

            if (/windows phone/i.test(userAgent)) {
                sistemaOperativo = "Windows Phone";
            } else if (/android/i.test(userAgent)) {
                sistemaOperativo = "Android";
            } else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                sistemaOperativo = "iOS";
            } else if (/Win/i.test(userAgent)) {
                sistemaOperativo = "Windows";
            } else if (/Mac/i.test(userAgent)) {
                sistemaOperativo = "MacOS";
            } else if (/Linux/i.test(userAgent)) {
                sistemaOperativo = "Linux";
            } else if (/CrOS/i.test(userAgent)) { // 🔥 ChromeOS usa "CrOS"
                sistemaOperativo = "ChromeOS";
            } else if (/BlackBerry/i.test(userAgent)) {
                sistemaOperativo = "BlackBerry";
            } else if (/BB10/i.test(userAgent)) {
                sistemaOperativo = "BB10";
            } else if (/IEMobile/i.test(userAgent)) {
                sistemaOperativo = "IEMobile";
            } else if (/Opera Mini/i.test(userAgent)) {
                sistemaOperativo = "Opera Mini";
            } else if (/Opera/i.test(userAgent)) {
                sistemaOperativo = "Opera";
            } else if (/Safari/i.test(userAgent)) {
                sistemaOperativo = "Safari";
            }

            return sistemaOperativo;
        }

        // Mostrar en consola
        console.log("Sistema Operativo Detectado:", detectarSO());
        document.addEventListener("DOMContentLoaded", function() {
            let sistemaOperativo = detectarSO();

            console.log("Sistema Operativo:", sistemaOperativo);

            fetch("app/logic/session/session.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "so=" + encodeURIComponent(sistemaOperativo)
                })
                .then(response => response.text())
                .then(data => console.log("Respuesta del servidor:", data))
                .catch(error => console.error("Error en fetch:", error));
        });
    </script>
</body>

</html>