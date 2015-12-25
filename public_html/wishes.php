<?php
    if (isset($_GET['err'])) {
        $error = $_GET['err'];
    }

    if (isset($_GET['msg'])) {
        $msg = $_GET['msg'];
    }

    $servername = "localhost";
    $dbname = "plex_wishes";
    $username = "gunnartorfis";
    $password = "Gunnar95";

    $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $sth_pending = $dbh->prepare("SELECT * FROM wishes WHERE downloaded=0");
        $sth_pending->execute();
        $result_pending = $sth_pending->fetchAll();

        $sth_downloaded = $dbh->prepare("SELECT * FROM wishes WHERE downloaded=1");
        $sth_downloaded->execute();
        $result_downloaded = $sth_downloaded->fetchAll();

    }
    catch (PDOException $e) {
        print $e->getMessage();
    }

    $dbh = null;
?>
<?php include 'includes/header.php'; ?>
    <div class="inner">
        <h3 class="masthead-brand">Úr Fossagili beint í Plex!</h3>
        <nav>
            <ul class="nav masthead-nav">
                <li><a href="index.php">Heim</a></li>
                <li class="active"><a href="wishes.php">Óskalistinn</a></li>
                <li><a href="deildu.php">Nýjar myndir á Deildu.net</a></li>
            </ul>
        </nav>
    </div>
        <div class="inner cover">
            <div class="wish-list not-downloaded">
                <h4>Á biðlista <span class="glyphicon glyphicon-pause" aria-hidden="true"></span></h4>
                <table class="table table-hover table-wishes">
                    <thead>
                        <tr>
                            <th>Titill</th>
                            <th>Óskað eftir af</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result_pending as $wish) : ?>
                            <tr id="<?php echo $wish[0]; ?>">
                                <th><?php echo $wish[1]; ?></th>
                                <th><?php echo $wish[2]; ?></th>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="wish-list downloaded">
                <h4>Búið að sækja <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></h4>
                <table class="table table-hover table-wishes">
                    <thead>
                        <tr>
                            <th>Titill</th>
                            <th>Óskað eftir af</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result_downloaded as $wish) : ?>
                            <tr>
                                <th><?php echo $wish[1]; ?></th>
                                <th><?php echo $wish[2]; ?></th>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
        <script type="text/javascript">
            $('tr').dblclick(function() {
                var id = $(this).attr('id');

                $.ajax({
                    type: "POST",
                    url: "process_wish_edit.php",
                    data: "id=" + id,
                    success: function(data) {
                        location.reload();
                    }
                });
            });
        </script>
    </body>
</html>
