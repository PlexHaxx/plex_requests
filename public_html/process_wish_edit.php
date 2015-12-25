<?php
    if(!isset($_POST['id']) || strlen(trim($_POST['id'])) == 0){
        header('Location: index.php?err=' . urlencode("emptyTitle"));
        die();
    }

    $id = $_POST['id'];

    $servername = "localhost";
    $dbname = "plex_wishes";
    $username = "gunnartorfis";
    $password = "Gunnar95";

    $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $sth = $dbh->prepare("SELECT * FROM wishes WHERE id=$id");
        $sth->execute();
        $row = $sth->fetch();

        if ($row['downloaded'] == 0) {
            $dbh->exec("UPDATE wishes SET downloaded=1 WHERE id=$id");
        }
        else if ($row['downloaded'] == 1) {
            $dbh->exec("UPDATE wishes SET downloaded=0");
        }
    }
    catch (PDOException $e) {
        print $e->getMessage();
    }

    $dbh = null;
?>
