<?php include('includes/httpful.phar'); ?>
<?php
    function getFeed($feed_url) {
        $content = file_get_contents($feed_url);
        $x = new SimpleXmlElement($content);

        $uri = "http://api.themoviedb.org/3/genre/movie/list?api_key=1e5387b55fe12efeb19db53ea9ca88a1";
        $response = \Httpful\Request::get($uri)->send();
        $all_genres = $response->body->genres;

        foreach($x->channel->item as $entry) {
            // ID: id
            // Poster: poster_path
            // Title: original_title
            // Plot: overview
            // Released: release_date
            // Backdrop path: backdrop_path (mynd)
            // Genres: genre_ids

            $movie_title = $entry->title;
            $title = modifyTitle($movie_title);

            $uri = "http://api.themoviedb.org/3/search/movie?api_key=1e5387b55fe12efeb19db53ea9ca88a1&query=" . $title;
            $uri = str_replace(" ","%20",$uri);
            $response = \Httpful\Request::get($uri)->send();

            if (count($response->body->results[0]) > 0) {
                $imageURL =  "http://image.tmdb.org/t/p/w500" . $response->body->results[0]->poster_path;
                $released_date = date("Y", $response->body->results[0]->release_date);
                $genres = $response->body->results[0]->genre_ids;

                $genres_ids_matched = array();
                for ($i = 0; $i < count($genres); $i++) {
                    for ($j = 0; $j < count($all_genres); $j++) {
                        if ($all_genres[$j]->id == $genres[$i]) {
                            $genres_ids_matched[] = $j;
                        }
                    }
                }

              ?>
                  <tr>
                    <th><img src="<?php echo $imageURL; ?>" alt="" style="width:140px;height:200px;"/></th>
                    <th style="background: url('<?php echo "http://image.tmdb.org/t/p/w500" . $response->body->results[0]->backdrop_path; ?>') no-repeat; background-size=100%;"><div class="movie-info"><a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $response->body->results[0]->original_title; ?>'><?php echo $response->body->results[0]->original_title . ' (' . $released_date . ')'; ?></a><br><?php echo $all_genres[$genres_ids_matched[0]]->name;?><br><br><p><?php echo $response->body->results[0]->overview; ?></p></div></th>
                </tr>

              <?php
            }
            else { ?>
                <tr>
                      <th><img src="default_image.png" alt="" style="width:140px;height:200px;"/></th>
                      <th><a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $entry->title; ?>'><?php echo $entry->title; ?></a></th>
                 </tr>
            <?php
            }
        }
    }
?>

<?php

    function modifyTitle($title) {
        $words = explode(" ", $title);
        $indexes = array();
        for ($i = 0; $i < count($words); $i++) {
            if (strpos(strtolower($words[$i]), 'brrip') === false && strpos(strtolower($words[$i]), 'bdrip') === false && strpos(strtolower($words[$i]), 'xvid') === false && strpos(strtolower($words[$i]), 'ac3') === false && strpos(strtolower($words[$i]), 'hive') === false && strpos(strtolower($words[$i]), 'dvdscr') === false && strpos(strtolower($words[$i]), 'hq') === false && strpos(strtolower($words[$i]), '1080p') === false && strpos(strtolower($words[$i]), '720p') === false && strpos(strtolower($words[$i]), 'x264') === false && strpos(strtolower($words[$i]), 'hdtv') === false && strpos(strtolower($words[$i]), 'dvdrip') === false && strpos(strtolower($words[$i]), 'dd5') === false && strpos(strtolower($words[$i]), 'uncut') === false && strpos(strtolower($words[$i]), 's0') === false && !ctype_digit($words[$i]) && strpos(strtolower($words[$i]), '[') === false && strpos(strtolower($words[$i]), ']') === false && strpos(strtolower($words[$i]), 'web') === false && strpos(strtolower($words[$i]), 'dd5') === false && strpos(strtolower($words[$i]), 'h264') === false && strpos(strtolower($words[$i]), '(') === false && strpos(strtolower($words[$i]), ')') === false && strpos(strtolower($words[$i]), 'pldub') === false) {
                array_push($indexes, $i);
            }
        }

        $modified_title = "";
        for ($i = 0; $i < count($indexes); $i++) {
            $modified_title .= $words[$indexes[$i]] . " ";
        }

        return $modified_title;
    }

?>

<?php include 'includes/header.php'; ?>
    <div class="inner">
        <h3 class="masthead-brand">Úr Fossagili beint í Plex!</h3>
        <nav>
            <ul class="nav masthead-nav">
                <li><a href="index.php">Heim</a></li>
                <li><a href="wishes.php">Óskalistinn</a></li>
                <li class="active"><a href="deildu.php">Nýjar myndir á Deildu.net</a></li>
            </ul>
        </nav>
    </div>
    <div class="inner cover">
        <div class="wish-list not-downloaded">
            <table class="table table-hover table-deildu">
                <thead>
                    <tr>
                        <th>Mynd</th>
                        <th>Titill</th>
                    </tr>
                </thead>
                <tbody>
                    <?php getFeed("http://icetracker.org/get_rss.php?user=Jobs&cat=12,6,7&passkey=f22615c5c857aae94708b379e1594ecd"); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
