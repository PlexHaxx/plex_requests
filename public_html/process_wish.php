<?php session_start(); ?>
<?php require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php'; ?>
<?php
    if(!isset($_POST['title']) || strlen(trim($_POST['title'])) == 0){
        header('Location: index.php?err=' . urlencode("emptyTitle"));
        die();
    }
    else if(!isset($_POST['name']) || strlen(trim($_POST['name'])) == 0){
        header('Location: index.php?err=' . urlencode("emptyName"));
        die();
    }

    $title = $_POST['title'];
    $name = $_POST['name'];

    $servername = "localhost";
    $dbname = "plex_wishes";
    $username = "gunnartorfis";
    $password = "Gunnar95";

    $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $emailMsg = "";
    try {
        $dbh->exec("INSERT INTO wishes (title, name) VALUES ('$title', '$name')");
        sendMail($title, $name);
    }
    catch (PDOException $e) {
        print $e->getMessage();
    }

    $dbh = null;

    header('Location: index.php?msg=' . urlencode("addedWish") . '&email=' . urlencode($emailMsg));
?>

<?php

    function sendMail($title, $name) {
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = "smtp.gmail.com"
        $mail->Port = 465;
        $mail->IsHTML(true);
        $mail->Username = "gunnartorfis@gmail.com";
        $mail->Password = "m3zjuTusMgs82TdkT}";
        $mail->SetFrom("wish@plex.gunnartorfis.is");
        $mail->Subject = "Ný beiðni";
        $mail->Body = "Titill: " . $title . ".\nUmbeðið af " . $name . ".";

        if (!$mail->Send()) {
            // echo "Mailer Error: " . $mail->ErrorInfo;
            $emailMsg = "emailFailed";
        }
        else {
            $emailMsg = "emailSuccess";
        }
    }

?>
