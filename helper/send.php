<?php 
    require_once '../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Load Config 
    $config = require_once '../config/mail.php';

    function buildSignupEmailBody($name, $email, $logoCid = null) {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $registeredAt = htmlspecialchars(date('F j, Y \a\t g:i A'), ENT_QUOTES, 'UTF-8');
        $logoHtml = $logoCid
            ? '<img src="cid:' . htmlspecialchars($logoCid, ENT_QUOTES, 'UTF-8') . '" alt="HomePlanner Logo" width="96" height="96" style="display:block;margin:0 auto 14px;border-radius:16px;background:rgba(255,255,255,0.92);padding:10px;">'
            : '';

        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner Sign Up</title>
</head>
<body style="margin:0;padding:0;background:#ecfdf3;font-family:Segoe UI,Arial,sans-serif;color:#2f4f38;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#ecfdf3;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 14px 36px rgba(95,148,103,0.18);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#5f9467,#89ad8d);padding:28px 32px 24px;text-align:center;">
                            ' . $logoHtml . '
                            <h1 style="margin:0;color:#ffffff;font-size:1.75rem;font-weight:800;letter-spacing:-0.5px;">HomePlanner</h1>
                            <p style="margin:8px 0 0;color:#f0fdf4;font-size:0.98rem;">Account created successfully</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 32px 10px;">
                            <p style="margin:0 0 16px;font-size:1.05rem;line-height:1.6;color:#2f4f38;">
                                Hello <strong>' . $safeName . '</strong>,
                            </p>
                            <p style="margin:0 0 22px;font-size:0.98rem;line-height:1.65;color:#4b6b52;">
                                Successfully created account! Welcome to HomePlanner. Here are your registration details:
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6fff8;border:1px solid #d9f0df;border-radius:14px;">
                                <tr>
                                    <td style="padding:14px 18px;border-bottom:1px solid #e4f4e8;">
                                        <span style="display:block;font-size:0.78rem;font-weight:700;color:#7aa07f;text-transform:uppercase;letter-spacing:0.4px;">Name</span>
                                        <span style="display:block;margin-top:4px;font-size:0.98rem;color:#2f4f38;">' . $safeName . '</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;border-bottom:1px solid #e4f4e8;">
                                        <span style="display:block;font-size:0.78rem;font-weight:700;color:#7aa07f;text-transform:uppercase;letter-spacing:0.4px;">Email</span>
                                        <span style="display:block;margin-top:4px;font-size:0.98rem;color:#2f4f38;">' . $safeEmail . '</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <span style="display:block;font-size:0.78rem;font-weight:700;color:#7aa07f;text-transform:uppercase;letter-spacing:0.4px;">Registered at</span>
                                        <span style="display:block;margin-top:4px;font-size:0.98rem;color:#2f4f38;">' . $registeredAt . '</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 28px;">
                            <p style="margin:0;font-size:0.92rem;line-height:1.55;color:#6b8a72;">
                                You can now sign in and start planning your home with HomePlanner.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f6fff8;padding:16px 32px 22px;text-align:center;border-top:1px solid #e4f4e8;">
                            <p style="margin:0;font-size:0.88rem;color:#7aa07f;">
                                <em>This message was sent automatically by the HomePlanner system.</em>
                            </p>
                            <p style="margin:8px 0 0;font-size:0.88rem;color:#89ad8d;font-weight:700;">Plan smarter. Live better.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    function sendEmail($toEmail, $toName, $subject, $body, $embeddedImages = []) {
        global $config;
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->SMTPSecure = $config['encryption'];
            $mail->Port       = $config['port'];

            $mail->setFrom($config['from_email'],$config['from_name']);

            $mail->addAddress($toEmail,$toName);

            foreach ($embeddedImages as $image) {
                $mail->addEmbeddedImage(
                    $image['path'],
                    $image['cid'],
                    $image['name'] ?? ''
                );
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception $e){
            return $mail->ErrorInfo;
        }
    }

    function sendSignupEmail($toEmail, $toName) {
        $validatedEmail = filter_var($toEmail, FILTER_VALIDATE_EMAIL);

        if (!$validatedEmail) {
            return false;
        }

        $logoPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo.png';
        $logoCid = null;
        $embeddedImages = [];

        if (is_file($logoPath)) {
            $logoCid = 'homeplanner_logo';
            $embeddedImages[] = [
                'path' => $logoPath,
                'cid' => $logoCid,
                'name' => 'logo.png',
            ];
        }

        $body = buildSignupEmailBody($toName, $validatedEmail, $logoCid);
        $subject = 'HomePlanner - Welcome! Account Created';

        return sendEmail($validatedEmail, $toName, $subject, $body, $embeddedImages);
    }


?>