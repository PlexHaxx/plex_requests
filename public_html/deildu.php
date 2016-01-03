<?php require_once dirname(__DIR__).'/vendor/autoload.php'; ?>
<?php
    function getFeed($feed_url) {
        $content = file_get_contents($feed_url);
        $x = new SimpleXmlElement($content);

        $token  = new \Tmdb\ApiToken('1e5387b55fe12efeb19db53ea9ca88a1');
        $client = new \Tmdb\Client($token);
        $repository = new \Tmdb\Repository\MovieRepository($client);

        $all_genres = $client->getGenresApi()->getGenres();
    //    var_dump($all_genres['genres'][0]['name']);
        $all_genres_ids = array();
        foreach ($all_genres['genres'] as $genre) {
            array_push($all_genres_ids, $genre['id']);
        }

        $movieIds = array();

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
            $result = $client->getSearchApi()->searchMovies($title);
            $movie = array_values($result)[1][0];
            $movie_url = "https://www.themoviedb.org/movie/" . $movie['id'];

            if (count($movie) > 0) {
                if (!in_array($movie['id'], $movieIds)) {
                    array_push($movieIds, $movie['id']);
                    $imageURL =  "http://image.tmdb.org/t/p/w500" . $movie['poster_path'];
                    $released_date = substr($movie['release_date'], 0, 4);
                    $genres = $movie['genre_ids'];

                    $genres_ids_matched = array();
                    for ($i = 0; $i < count($genres); $i++) {
                            for ($j = 0; $j < count($all_genres_ids); $j++) {
                                if ($genres[$i] == $all_genres_ids[$j]) {
                                    array_push($genres_ids_matched, $all_genres_ids[$j]);
                                }
                            }
                    }

                    $genre_string = '';
                    foreach ($all_genres['genres'] as $genre) {
                        if (in_array($genre['id'], $genres_ids_matched)) {
                            $genre_string .= ' ' . $genre['name'] . ',';
                        }
                    }

                    if (substr($genre_string, -1) == ',') {
                        $genre_string = rtrim($genre_string, ',');
                    }

                    $plot = $movie['overview'];
                    if (strlen($plot) > 285)
                        $plot = substr($plot, 0, 282) . '...';

                    $original_title = $movie['original_title'];
                    if (strlen($original_title) > 40)
                        $original_title = substr($original_title, 0, 37) . '...';
                  ?>
                          <tr>
                            <th><a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $original_title; ?>'><img class="poster-image" src="<?php echo $imageURL; ?>" alt="" onerror="this.onerror=null;this.src='images/movie-default.jpg';"/></a></th>
                            <th>
                                <div class="movie-background" style="background: url('<?php echo "http://image.tmdb.org/t/p/w500" . $movie['backdrop_path']; ?>') no-repeat center center; background-size: 100%;">
                                    <div class="movie-background-overlay">
                                            <a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $original_title; ?>'>
                                                <div class="movie-info">
                                                    <h2><?php echo $original_title . ' (' . $released_date . ')'; ?></h2>
                                                    <p class="movie-genre"><?php echo $genre_string; ?></p>
                                                    <p class="movie-plot"><?php echo $plot ?></p>
                                                </div>
                                            </a>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="table-deildu-actions">
                                    <div class="table-deildu-action action-first">
                                        <a href="process_wish.php?title=<?php echo $movie['original_title']; ?>"><img src="images/add_icon.png" alt="Add movie" /></a>
                                    </div>
                                    <div class="table-deildu-action action-second">
                                        <a href="<?php echo $movie_url; ?>" target="_blank"><img src="images/themoviedb_icon.png" alt="TMDB" /></a>
                                    </div>
                                    <div class="table-deildu-action action-third">
                                        <a href="<?php echo $entry->link; ?>" target="_blank"><img src="images/deildu_logo.jpg" alt="Deildu" /></a>
                                    </div>
                                </div>
                            </th>
                        </tr> <?php
                }
            }
            else {
                ?>
                <tr>
                  <th><a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $entry->title ?>'><img class="poster-image" src="images/movie-default.jpg" alt="Default poster"/></th>
                  <th>
                      <div class="movie-background">
                          <div class="movie-background-overlay">
                                  <a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $entry->title ?>'>
                                      <div class="movie-info">
                                          <h2><?php echo $entry->title; ?></h2>
                                          <p class="movie-plot"><?php echo "Ekki fundust upplýsingar um þessa mynd.<br>Titill hennar gæti verið stafsettur vitlaust.<br>Einnig gæti verið að þetta sé sjónvarsþáttur." ?></p>
                                      </div>
                                  </a>
                              </a>
                          </div>
                      </div>
                  </th>
                  <th>
                      <div class="table-deildu-actions">
                          <div class="table-deildu-action-double action-first">
                              <a href="process_wish.php?title=<?php echo $entry->title; ?>"><img src="images/add_icon.png" alt="Add movie" /></a>
                          </div>
                          <div class="table-deildu-action-double action-third">
                              <a href="<?php echo $entry->link; ?>" target="_blank"><img src="images/deildu_logo.jpg" alt="Deildu" /></a>
                          </div>
                      </div>
                  </th>
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

        $skipped_words = array('brrip', 'bdrip', 'xvid', 'ac3', 'hive', 'dvdscr', 'hq', '1080p', '720p', '480p', 'x264', 'hdtv', 's0', 'dvdrip',
                                'dd5', 'uncut', '[', ']', 'web', 'dd5', 'h264', '(', ')', 'pldub', 'dd2', '3d', 'limited', 'isl', 'ensk', 'isl-texti',
                                'islenskur', 'texti', 'txt', 'íslenskur', 'ísl', 'hd', 'fx', 'internal', 'festival', 'enskur-texti', 'know', 'cz-i',
                                'webrip', 'hulu', 'aac', 'x264-phobos', 'aac2', '264-ntb', 'ac3-evo', 'aac-nft', 'x264-amiable', 'greek', 'x264-ichor',
                                'x264-rusted', 'hive-cm8', 'x264-balkan', 'avi', 'x264-scared'
                            );

        for ($i = 0; $i < count($words); $i++) {
            if (!in_array(strtolower($words[$i]), $skipped_words)) {
                if (!is_numeric($words[$i])) {
                    if (strlen($words[$i]) != 1) {
                        array_push($indexes, $i);
                    }
                    else {
                        if (strtolower($words[$i]) == 'a') {
                            array_push($indexes, $i);
                        }
                    }
                }
                else {
                    if (strlen((string)$words[$i]) < 4 && $i == 0) {
                        array_push($indexes, $i);
                    }
                }
            }
        }

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
            <table class="table table-deildu">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php getFeed("http://icetracker.org/get_rss.php?user=Jobs&cat=12,6,7&passkey=f22615c5c857aae94708b379e1594ecd"); ?>
                </tbody>
            </table>
    </div>
<?php include 'includes/footer.php'; ?>
