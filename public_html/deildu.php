<?php require_once dirname(__DIR__).'/vendor/autoload.php'; ?>
<?php
    function getFeed($feed_url) {
        $content = file_get_contents($feed_url);
        $x = new SimpleXmlElement($content);

        $token  = new \Tmdb\ApiToken('1e5387b55fe12efeb19db53ea9ca88a1');
        $client = new \Tmdb\Client($token);
        $repository = new \Tmdb\Repository\MovieRepository($client);

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

            if (count($movie) > 0) {
                if (!in_array($movie['id'], $movieIds)) {
                    array_push($movieIds, $movie['id']);
                    $imageURL =  "http://image.tmdb.org/t/p/w500" . $movie['poster_path'];
                    $released_date = substr($movie['release_date'], 0, 4);
                    $genres = $movie['genre_ids'];

                    $genres_ids_matched = array();
                    for ($i = 0; $i < count($genres); $i++) {
                        for ($j = 0; $j < count($all_genres); $j++) {
                            if ($all_genres[$j]->id == $genres[$i]) {
                                $genres_ids_matched[] = $j;
                            }
                        }
                    }

                    $plot = $movie['overview'];
                    if (strlen($plot) > 300)
                        $plot = substr($plot, 0, 297) . '...';
                  ?>
                          <tr>
                            <th><a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $movie['original_title']; ?>'><img class="poster-image" src="<?php echo $imageURL; ?>" alt="" onerror="this.onerror=null;this.src='images/movie-default.jpg';"/></a></th>
                            <th>
                                <div class="movie-background" style="background: url('<?php echo "http://image.tmdb.org/t/p/w500" . $movie['backdrop_path']; ?>') no-repeat center center; background-size: 100%;">
                                    <div class="movie-background-overlay">
                                            <a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $movie['original_title']; ?>'>
                                                <div class="movie-info">
                                                    <h2><?php echo $movie['original_title'] . ' (' . $released_date . ')'; ?></h2>
                                                    <p class="movie-genre"><?php echo $all_genres[$genres_ids_matched[0]]->name;?></p>
                                                    <p class="movie-plot"><?php echo $plot ?></p>
                                                </div>
                                            </a>
                                        </a>
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
                                'x264-rusted'
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
        <div class="wish-list not-downloaded">
            <table class="table table-hover table-deildu">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php getFeed("http://icetracker.org/get_rss.php?user=Jobs&cat=12,6,7&passkey=f22615c5c857aae94708b379e1594ecd"); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
