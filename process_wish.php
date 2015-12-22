<?php session_start(); ?>
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

    try {
        $dbh->exec("INSERT INTO wishes (title, name) VALUES ('$title', '$name')");
        // TODO: Email about the new wish
    }
    catch (PDOException $e) {
        print $e->getMessage();
    }

    $dbh = null;

    header('Location: index.php?msg=' . urlencode("addedWish"));
?>
