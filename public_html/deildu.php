<?php session_start(); ?>
<?php
    function getFeed($feed_url) {

        $content = file_get_contents($feed_url);
        $x = new SimpleXmlElement($content);

        foreach($x->channel->item as $entry) {
            echo "<tr>";
            echo "<th><a target='_blank' href='$entry->link' title='$entry->title'>" . $entry->title . "</a></th>";
            echo "</tr>";
        }
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
                        <li><a href="index.php">Heim</a></li>
                        <li><a href="wishes.php">Óskalistinn</a></li>
                        <li class="active"><a href="#">Nýtt á Deildu.net</a></li>
                    </ul>
                </nav>
            </div>

            <div class="inner cover">
                <div class="wish-list not-downloaded">
                    <table class="table table-hover table-deildu">
                        <thead>
                            <tr>
                                <th>Torrent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php getFeed("http://icetracker.org/get_rss.php?user=Jobs&cat=12,6,7,8&passkey=f22615c5c857aae94708b379e1594ecd"); ?>
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
