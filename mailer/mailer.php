<?php
header("Access-Control-Allow-Headers: Access-Control-Allow-Credentials, Content-Type, Access-Control-Request-Method, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Request-Headers");
header("Access-Control-Allow-Origin: https://mariachilabs.mx");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json');
/**
 * source: 
 * https://www.awardspace.com/kb/create-contact-form-using-phpmailer/
 * https://mailtrap.io/blog/php-email-contact-form/
 * 
 */
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // $myPersonalEmail = "balamcantzin@gmail.com";
    $myPersonalEmail = "hola@mariachilabs.mx";
    
    $externalMailHost = "smtp.ionos.mx";
    $externalMailAddress = "noreply@mariachilabs.mx";
    $externalMailSMTPAuth = true;
    $externalMailUsername = "m79057095-153101793";
    $externalMailPassword = "hE5NHWKSwGTzDEZ";
    $externalMailSMTPSecure = "tls";
    $externalMailPort = 25;

    require './PHPMailer-master/src/Exception.php';
    require './PHPMailer-master/src/PHPMailer.php';
    require './PHPMailer-master/src/SMTP.php';
    $_POST = json_decode(file_get_contents('php://input'), true);

    $response = ['message'=>"Hubo un problema para enviar el correo", 'status'=>"error" ];
    if(isset($_POST['data']) && $_POST['data']['submit']) {
        $data = $_POST['data'];
        $subject = "Contacto de bennu energy {$data['nombre']}";
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
        $mail->setFrom($externalMailAddress, 'Mailer Bennu Energy');
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
                    <td><b>Mensaje:</b></td>
                    <td>{$data['mensaje']}</td>
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
    
?>