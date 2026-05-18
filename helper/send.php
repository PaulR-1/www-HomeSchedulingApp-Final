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

    function buildHomeCodeEmailBody($name, $email, $homeCode, $logoCid = null) {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safeHomeCode = htmlspecialchars($homeCode, ENT_QUOTES, 'UTF-8');
        $sentAt = htmlspecialchars(date('F j, Y \a\t g:i A'), ENT_QUOTES, 'UTF-8');
        $logoHtml = $logoCid
            ? '<img src="cid:' . htmlspecialchars($logoCid, ENT_QUOTES, 'UTF-8') . '" alt="HomePlanner Logo" width="96" height="96" style="display:block;margin:0 auto 14px;border-radius:16px;background:rgba(255,255,255,0.92);padding:10px;">'
            : '';

        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner Home Code</title>
</head>
<body style="margin:0;padding:0;background:#fff2f2;font-family:Segoe UI,Arial,sans-serif;color:#6c2c31;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff2f2;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 14px 36px rgba(179,72,78,0.2);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#b3484e,#dd7076);padding:28px 32px 24px;text-align:center;">
                            ' . $logoHtml . '
                            <h1 style="margin:0;color:#ffffff;font-size:1.75rem;font-weight:800;letter-spacing:-0.5px;">HomePlanner</h1>
                            <p style="margin:8px 0 0;color:#ffe8ea;font-size:0.98rem;">Red Home Access Invitation</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 32px 10px;">
                            <p style="margin:0 0 16px;font-size:1.05rem;line-height:1.6;color:#6c2c31;">
                                Hello <strong>' . $safeName . '</strong>,
                            </p>
                            <p style="margin:0 0 22px;font-size:0.98rem;line-height:1.65;color:#8a4f54;">
                                You have been invited to join <strong>Red Home</strong>. Use the Home ID below when logging in:
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff6f6;border:1px solid #ffd9dc;border-radius:14px;">
                                <tr>
                                    <td style="padding:16px 18px;text-align:center;">
                                        <span style="display:block;font-size:0.78rem;font-weight:700;color:#b3484e;text-transform:uppercase;letter-spacing:0.4px;">Home ID</span>
                                        <span style="display:block;margin-top:8px;font-size:1.35rem;color:#6c2c31;font-weight:800;letter-spacing:1px;">' . $safeHomeCode . '</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 18px 16px;text-align:center;">
                                        <span style="display:block;font-size:0.82rem;color:#9d656a;">Sent to: ' . $safeEmail . '</span>
                                        <span style="display:block;margin-top:4px;font-size:0.82rem;color:#9d656a;">Date: ' . $sentAt . '</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 28px;">
                            <p style="margin:0;font-size:0.92rem;line-height:1.55;color:#8a4f54;">
                                If you already have an account, log in and enter this Home ID to access the Red Home dashboard.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fff6f6;padding:16px 32px 22px;text-align:center;border-top:1px solid #ffd9dc;">
                            <p style="margin:0;font-size:0.88rem;color:#b9797e;">
                                <em>This message was sent automatically by the HomePlanner system.</em>
                            </p>
                            <p style="margin:8px 0 0;font-size:0.88rem;color:#c4878c;font-weight:700;">Plan smarter. Live better.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    function sendHomeCodeEmail($toEmail, $toName, $homeCode) {
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

        $body = buildHomeCodeEmailBody($toName, $validatedEmail, $homeCode, $logoCid);
        $subject = 'HomePlanner - Your Red Home ID';

        return sendEmail($validatedEmail, $toName, $subject, $body, $embeddedImages);
    }


?>