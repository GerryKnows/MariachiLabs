<?php
header("Access-Control-Allow-Headers: Access-Control-Allow-Credentials, Content-Type, Access-Control-Request-Method, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Request-Headers");
header("Access-Control-Allow-Origin: https://mariachilabs.mx");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

/**
 * source: 
 * https://www.awardspace.com/kb/create-contact-form-using-phpmailer/
 * https://mailtrap.io/blog/php-email-contact-form/
 * 
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {

    // $myPersonalEmail = "balamcantzin@gmail.com";
    $myPersonalEmail = "hola@mariachilabs.mx";
    
    // reCAPTCHA configuration
    $recaptcha_secret = "6LfrmnMrAAAAAPKAFpZKoafokGIHGcxqerFQNh0R";
    
    $externalMailHost = "smtp.ionos.mx";
    $externalMailAddress = "noreply@mariachilabs.mx";
    $externalMailSMTPAuth = true;
    $externalMailUsername = "m79057095-153101793";
    $externalMailPassword = "hE5NHWKSwGTzDEZ";
    $externalMailSMTPSecure = "ssl";
    $externalMailPort = 465;

    // Check if PHPMailer files exist
    $phpmailer_files = [
        './PHPMailer-master/src/Exception.php',
        './PHPMailer-master/src/PHPMailer.php',
        './PHPMailer-master/src/SMTP.php'
    ];
    
    foreach ($phpmailer_files as $file) {
        if (!file_exists($file)) {
            echo json_encode(['message' => 'Error: Archivo PHPMailer no encontrado: ' . $file, 'status' => 'error']);
            die();
        }
    }
    
    require './PHPMailer-master/src/Exception.php';
    require './PHPMailer-master/src/PHPMailer.php';
    require './PHPMailer-master/src/SMTP.php';
    
    // Get and decode JSON input
    $json_input = file_get_contents('php://input');
    if ($json_input === false) {
        echo json_encode(['message' => 'Error al leer datos de entrada', 'status' => 'error']);
        die();
    }
    
    $_POST = json_decode($json_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => 'Error al decodificar JSON: ' . json_last_error_msg(), 'status' => 'error']);
        die();
    }

    $response = ['message'=>"Hubo un problema para enviar el correo", 'status'=>"error" ];
    if(isset($_POST['data']) && $_POST['data']['submit']) {
        $data = $_POST['data'];
        
        // Verify reCAPTCHA
        if (!isset($data['captcha_response']) || empty($data['captcha_response'])) {
            $response['message'] = "Por favor, completa la verificación reCAPTCHA";
            echo json_encode($response);
            die();
        }
        
        // Verify the captcha response with Google using cURL
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        $verify_data = array(
            'secret' => $recaptcha_secret,
            'response' => $data['captcha_response'],
            'remoteip' => $_SERVER['REMOTE_ADDR']
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verify_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($verify_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $verify_response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($curl_error) {
            $response['message'] = "Error de conexión con reCAPTCHA: " . $curl_error;
            echo json_encode($response);
            die();
        }
        
        if ($http_code !== 200) {
            $response['message'] = "Error del servidor reCAPTCHA. Código HTTP: " . $http_code;
            echo json_encode($response);
            die();
        }
        
        $verify_result = json_decode($verify_response, true);
        
        if ($verify_result === null) {
            $response['message'] = "Error al procesar respuesta de reCAPTCHA";
            echo json_encode($response);
            die();
        }
        
        if (!isset($verify_result['success']) || !$verify_result['success']) {
            $error_codes = isset($verify_result['error-codes']) ? implode(', ', $verify_result['error-codes']) : 'Unknown error';
            $response['message'] = "Verificación reCAPTCHA fallida. Errores: " . $error_codes;
            echo json_encode($response);
            die();
        }
        
        $subject = "Contacto de mariachilabs.mx por {$data['nombre']}";
        $mail = new PHPMailer(true);

        $mail->SMTPDebug = 0;
        $mail->isSMTP();

        $mail->Host = $externalMailHost;
        $mail->SMTPAuth = $externalMailSMTPAuth;
        $mail->Username = $externalMailUsername;
        $mail->Password = $externalMailPassword;
        $mail->SMTPSecure = $externalMailSMTPSecure;
        $mail->Port = $externalMailPort;
        
        $mail->addReplyTo($data['email'], $data['nombre']);
        $mail->setFrom($externalMailAddress, 'Mailer Mariachi Labs');
        $mail->addAddress($myPersonalEmail);

        $mail->isHTML(true);    
        $mail->Subject = $subject;
        $mail->Body = 
        "<div>
            <h4>Datos de contacto</h4>
            <table>
                <tr>
                    <td><b>Nombre:</b></td>
                    <td>{$data['nombre']}</td>
                </tr>
                <tr>
                    <td><b>Email:</b></td>
                    <td>{$data['email']}</td>
                </tr>
                <tr>
                    <td><b>Phone:</b></td>
                    <td>{$data['phone']}</td>
                </tr>
                <tr>
                    <td><b>Mensaje:</b></td>
                    <td>{$data['mensaje']}</td>
                </tr>
                <tr>
                    <td><b>Check:</b></td>
                    <td>{$data['check']}</td>
                </tr>
            </table>
        </div>";
        
        try {
            $mail->send();
            $response['message']="El mensaje se envio satisfactoriamente!";
            $response['status']="ok";
            echo json_encode($response);
            die();
        } catch (Exception $e) {
            $response['message']="Tu mensaje no pudo enviarse! PHPMailer Error: {$mail->ErrorInfo}";
            echo json_encode($response);
            die();
        }
        
    } else {
        echo json_encode($response);
        die();
    }
    
} catch (Throwable $e) {
    // Catch any fatal errors or exceptions
    $error_response = [
        'message' => "Error interno del servidor: " . $e->getMessage(),
        'status' => "error",
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ];
    echo json_encode($error_response);
    error_log("Mailer Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}
    
?>