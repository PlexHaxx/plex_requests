<?php require_once dirname(__DIR__).'/vendor/autoload.php'; ?>
<?php require_once 'process_feed.php'; ?>
<?php include 'includes/header.php'; ?>
    <div class="inner">
        <h3 class="masthead-brand">Úr Fossagili beint í Plex!</h3>
        <nav>
            <ul class="nav masthead-nav">
                <li><a href="index.php">Heim</a></li>
                <li><a href="wishes.php">Óskalistinn</a></li>
                <li class="active"><a href="deildu.php">Nýtt á Deildu.net</a></li>
            </ul>
        </nav>
    </div>
    <div class="inner cover">
        <ul class="nav nav-tabs nav-justified">
            <li class="active"><a data-toggle="tab" href="#movies"><span><img src="images/movie.png" alt="Movies" /></span>Kvikmyndir</a></li>
            <li><a data-toggle="tab" href="#episodes"><span><img src="images/tvshow.png" alt="" /></span>Þættir</a></li>
            <li><a data-toggle="tab" href="#animated"><span><img src="images/animated.png" alt="" /></span>Teiknimyndir</a></li>
        </ul>

        <div class="tab-content">
            <!-- MOVIES -->
            <div id="movies" class="tab-pane fade in active">
                <table class="table table-deildu">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php getFeed("http://icetracker.org/get_rss.php?user=Jobs&cat=12,6&passkey=f22615c5c857aae94708b379e1594ecd", 0); ?>
                    </tbody>
                </table>
            </div>

            <div id="episodes" class="tab-pane fade">
                <table class="table table-deildu">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php getFeed("http://icetracker.org/get_rss.php?user=Jobs&cat=8&passkey=f22615c5c857aae94708b379e1594ecd", 1); ?>
                    </tbody>
                </table>
            </div>

            <div id="animated" class="tab-pane fade">
                <table class="table table-deildu">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php getFeed("http://icetracker.org/get_rss.php?user=Jobs&cat=7&passkey=f22615c5c857aae94708b379e1594ecd", 2); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
