<?php
// Turn off all error reporting
error_reporting(1);
?>

<?php
function getFeed($feed_url, $type) {
    // Type: Movie(0), TV(1), Animated(2)

    $content = file_get_contents($feed_url);
    $x = new SimpleXmlElement($content);

    $token  = new \Tmdb\ApiToken('1e5387b55fe12efeb19db53ea9ca88a1');
    $client = new \Tmdb\Client($token);

    if ($type != 1) {
        $all_genres = $client->getGenresApi()->getGenres();
        $all_genres_ids = array();

        foreach ($all_genres['genres'] as $genre) {
            array_push($all_genres_ids, $genre['id']);
        }

        $movieIds = array();
        $animatedIds = array();

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

            if (count($movie) > 0 && $type != 1) {
                if ($type == 0) {
                    if (!in_array(intval($movie['id']), $movieIds)) {
                        array_push($movieIds, intval($movie['id']));
                    }
                    else {
                        continue;
                    }
                }
                else if ($type == 2) {
                    if (!in_array(intval($movie['id']), $animatedIds)) {
                        array_push($animatedIds, intval($movie['id']));
                    }
                    else {
                        continue;
                    }
                }

                $imageURL =  "http://image.tmdb.org/t/p/w500/" . $movie['poster_path'];
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
                if (strlen($plot) > 285) {
                    $plot = substr($plot, 0, 282) . '...';
                }


                $original_title = $movie['original_title'];
                if (strlen($original_title) > 40) {
                    $original_title = substr($original_title, 0, 37) . '...';
                }
        ?>
                <tr>
                    <th>
                        <a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $original_title; ?>'>
                            <img class="poster-image" src="<?php echo $imageURL; ?>" alt="" onerror="this.onerror=null;this.src='images/movie-default.jpg';"/>
                        </a>
                    </th>
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
                </tr>
            <?php
            }
            else {
                if ($type == 1 || type == 2) :
            ?>
                    <tr>
                        <th>
                            <a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $entry->title ?>'>
                                <img class="poster-image" src="images/movie-default.jpg" alt="Default poster"/>
                            </a>
                        </th>
                        <th>
                            <div class="movie-background">
                                <div class="movie-background-overlay">
                                    <a target='_blank' href='<?php echo $entry->link; ?>' title='<?php echo $entry->title ?>'>
                                        <div class="movie-info">
                                            <h2><?php echo $entry->title; ?></h2>
                                            <p class="movie-plot"><?php echo "Ekki fundust upplýsingar um þessa mynd.<br>Titill hennar gæti verið stafsettur vitlaust.<br>Einnig gæti verið að þetta sé sjónvarsþáttur." ?></p>
                                        </div>
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
                endif;
            }
        }
    }
    else {
        // TV SHOWS
        foreach($x->channel->item as $entry) {
            // ID: id
            // Poster: poster_path
            // Title: original_title
            // Plot: overview
            // Released: release_date
            // Backdrop path: backdrop_path (mynd)
            // Genres: genre_ids

            $tv_show_title = $entry->title;
            $title = modifyTvShowTitle($tv_show_title);
            echo $title . '<br>';
            /*
            $result = $client->getSearchApi()->searchTv($title);
            $tv_show = array_values($result)[1][0];
            $movie_url = "https://www.themoviedb.org/movie/" . $tv_show['id'];

            echo $movie_title;*/
        }
    }
} ?>

<?php
function modifyTitle($title) {
    $words = explode(" ", $title);
    $indexes = array();
    $modified_title = '';

    $hasYear = false;
    for ($i = 0; $i < count($words); $i++) {
        if (is_numeric($words[$i]) && strlen((string)$words[$i]) == 4 && intval($words[$i]) > 1000 && intval($words[$i]) < 2200) {
            $hasYear = true;
        }

        if (!$hasYear && !is_numeric($words[$i])) {
            if (!preg_match('/\(([0-9]+?)\)/', $words[$i])) {
                array_push($indexes, $i);
            }
        }
    }

    for ($i = 0; $i < count($indexes); $i++) {
        $modified_title .= $words[$indexes[$i]] . " ";
    }

    if (count($indexes) > 0) {
        return $modified_title;
    }
    else {
        return $title;
    }
}

function modifyTvShowTitle($tv_show_title) {
    $words = explode(" ", $tv_show_title);
    $indexes = array();

    $words = array_filter($words, create_function('$var','return !(preg_match("/(?:HDTV|bluray|\w{2,3}rip)|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|(?:AC\d)/i", $var));'));
    $title =  join(' ', $words);

    return $title;
}
?>
