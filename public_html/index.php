<?php session_start(); ?>
<?php
    if (isset($_GET['err'])) {
        $error = $_GET['err'];
    }

    if (isset($_GET['msg'])) {
        $msg = $_GET['msg'];
    }

    if (isset($_GET['email'])) {
        $email = $_GET['email'];
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Plexið ógurlega</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="css/main.css" rel="stylesheet">

    </head>

    <body>
        <div class="site-wrapper">
            <div class="inner">
                <h3 class="masthead-brand">Úr Fossagili beint í Plex!</h3>
                <nav>
                    <ul class="nav masthead-nav">
                        <li class="active"><a href="#">Heim</a></li>
                        <li><a href="wishes.php">Óskalistinn</a></li>
                        <li><a href="deildu.php">Nýtt á Deildu.net</a></li>
                    </ul>
                </nav>
            </div>

            <div class="inner cover">
                <h1 class="cover-heading">Hvað viltu horfa á?</h1>
                <form action="process_wish.php" method="post" class="form_left">
                        <div class="form-group">
                            <label for="title">Titill efnis</label>
                            <input type="text" name="title" placeholder="Titill" class="form-control" id="title">
                        </div>

                        <div class="form-group">
                            <label for="title">Nafn</label>
                            <input type="text" name="name" placeholder="Hver er að biðja um þetta?" class="form-control" id="name">
                        </div>

                        <div class="form-group">
                            <input type="submit" name="submit" value="Senda inn" class="btn btn-success">
                            <?php if ($error == "emptyTitle") : ?>
                                <p class="emptyTitle">Það verður að vera titill!</p>
                            <?php endif; ?>
                            <?php if ($error == "emptyName") : ?>
                                <p class="emptyTitle">Það verður að vera nafn!</p>
                            <?php endif; ?>
                            <?php if ($msg == "addedWish" && $email == "success") : ?>
                                <p class="addedWish">Bætt við á óskalistann, GT hefur verið látinn vita!</p>
                            <?php endif; ?>
                            <?php if ($msg == "addedWish" && $email == "failed") : ?>
                                <p class="addedWish">Bætt við á óskalistann, en af einhverjum ástæðum fékk GT ekki email um það.</p>
                            <?php endif; ?>
                        </div>
                </form>
            </div>

                <div class="mastfoot">
                    <div class="inner">
                        <p>Unnið af Gunnari Torfa &copy; 2015</p>
                    </div>
                </div>

        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="js/bootstrap.js"></script>
    </body>
</html>
