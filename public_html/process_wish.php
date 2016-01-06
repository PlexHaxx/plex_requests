<?php require '../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php'; ?>
<?php
    if (isset($_GET['title'])) {
        $title = $_GET['title'];
        $name = "Óþekktur";
    }
    else {
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
    }

    $servername = "localhost";
    $dbname = "plex_wishes";
    $username = "gunnartorfis";
    $password = "Gunnar95";

    $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $dbh->exec("INSERT INTO wishes (title, name) VALUES ('$title', '$name')");

        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "gunnartorfis@gmail.com";
        $mail->Password = "Fohf=bvAAp3dZ28ZjX";
        $mail->SetFrom("plex@gunnartorfis.is");
        $mail->CharSet = "UTF-8";
        $mail->Subject = "Plex ósk";
        $mail->Body = $name . ": " . $title;
        $mail->AddAddress("gunnartorfis@gmail.com");

         if(!$mail->Send()) {
            $mailMsg = "fail";
            // echo "Mailer Error: " . $mail->ErrorInfo;
         } else {
            $mailMsg = "success";
         }
    }
    catch (PDOException $e) {
        // print $e->getMessage();
    }

    $dbh = null;

    header('Location: index.php?msg=' . urlencode("addedWish") . '&email=' . urlencode($mailMsg));
?>
