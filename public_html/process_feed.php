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

        $current_ids = array();

        foreach($x->channel->item as $entry) {

            /* @@ Modification for TMDB API @@ */
            $movie_title = $entry->title;
            $title = modifyTitle($movie_title);

            $result = $client->getSearchApi()->searchMovies($title);
            $movie = array_values($result)[1][0];

            /* @@ Check if result is found from TMDB API @@ */
            if (count($movie) > 0) {
                if (!in_array(intval($movie['id']), $current_ids)) {
                    array_push($current_ids, intval($movie['id']));
                }
                else {
                    continue;
                }

                /* @@ Title @@ */
                $original_title = shortenText($movie['original_title'], 40);

                /* @@ Plot (overview) @@ */
                $plot = shortenText($movie['overview'], 285);

                /* @@ Release year @@ */
                $released_year = substr($movie['release_date'], 0, 4);

                /* @@ Release year @@ */
                $genres = $movie['genre_ids'];
                $genre_string = getGenreString($genres, $all_genres_ids, $all_genres);

                $poster_url =  "http://image.tmdb.org/t/p/w500/" . $movie['poster_path'];
                $backdrop_url = "http://image.tmdb.org/t/p/w500" . $movie['backdrop_path'];
                $content_url = "https://www.themoviedb.org/movie/" . $movie['id'];

                renderTable($original_title, $plot, $released_year, $genre_string, $poster_url, $backdrop_url, $content_url, $entry->link);
            }
            else {
                if ($type != 2) {
                    // Teiknimyndir eru oft á íslensku og þ.a.l. ekki á TMDB
                    $plot = "Ekki fundust upplýsingar um þessa mynd.<br>Titill hennar gæti verið stafsettur vitlaust.<br>Einnig gæti verið að þetta sé sjónvarsþáttur.";
                    renderTable($entry->title, $plot, null, null, null, null, null, $entry->link);
                }
            }
        }
    }
    else {
        // TV SHOWS
        foreach($x->channel->item as $entry) {

            $tv_show_title = $entry->title;
            $tv_show_title = modifyTvShowTitle($tv_show_title);

            $result = $client->getSearchApi()->searchTv($tv_show_title);

            $tv_show = array_values($result)[1][0];

            if (count($tv_show) > 0) {
                $tv_show_title = $tv_show['name'];
                $tv_show_title .= ' ' . getEpisodeNumber($entry->title);
                $plot = shortenText($tv_show['overview'], 300);
                $released_year = substr($tv_show['first_air_date'], 0, 4);
                $genres = $tv_show['genre_ids'];
                $genre_string = getGenreString($genres, $all_genres_ids, $all_genres);
                $poster_url =  "http://image.tmdb.org/t/p/w500/" . $tv_show['poster_path'];
                $backdrop_url = "http://image.tmdb.org/t/p/w500" . $tv_show['backdrop_path'];
                $content_url = "https://www.themoviedb.org/tv/" . $tv_show['id'];

                renderTable($tv_show_title, $plot, $released_year, $genre_string, $poster_url, $backdrop_url, $content_url, $entry->link);
            }
        }
    }
}

    function modifyTitle($title)
    {
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

    function modifyTvShowTitle($tv_show_title)
    {
        $words = explode(" ", $tv_show_title);
        $indexes = array();

        $words = array_filter($words, create_function('$var','return !(preg_match("/(?:HDTV|bluray|\w{2,3}rip)|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|(?:AC\d)/i", $var));'));
        $title =  join(' ', $words);

        $pattern = "/S[0-9].*$/";
        $title = preg_replace($pattern, '', $title);

        $pattern = "/-.*$/";
        $title = preg_replace($pattern, '', $title);

        return $title;
    }

    function getEpisodeNumber($title)
    {
        $words = explode(" ", $title);
        $indexes = array();

        $words = array_filter($words, create_function('$var','return !(preg_match("/(?:HDTV|bluray|\w{2,3}rip)|(?:x264)|(?:\d{4})|(?:\d{3,4}p)|(?:AC\d)/i", $var));'));
        $episode_string =  join(' ', $words);

        $episode_string = preg_replace('/\.S[0-9].*$/', '', $episode_string);

        return $episode_string;
    }

    function getGenreString($genres, $all_genres_ids, $all_genres)
    {
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

        return $genre_string;
     }

     function shortenText($text, $max_length)
     {
         if (strlen($text) > intval($max_length)) {
             $text = substr($text, 0, intval($max_length) - 3) . '...';
         }
         return $text;
     }

    function renderTable($title, $plot, $year, $genre, $poster_url, $backdrop_url, $content_url, $torrent_url)
    {
        ?>
            <tr>
                <th>
                    <a target='_blank' href='<?php echo $torrent_url; ?>' title='<?php echo $title; ?>'>
                        <img class="poster-image" src="<?php echo $poster_url; ?>" alt="" onerror="this.onerror=null;this.src='images/movie-default.jpg';"/>
                    </a>
                </th>
                <th>
                    <div class="movie-background" style="background: url('<?php echo $backdrop_url; ?>') no-repeat center center; background-size: 100%;">
                        <div class="movie-background-overlay">
                            <a target='_blank' href='<?php echo $torrent_url; ?>' title='<?php echo $title; ?>'>
                                <div class="movie-info">
                                    <h2>
                                        <?php if (!is_null($year)) : ?>
                                            <?php echo $title . ' (' . $year . ')'; ?>
                                        <?php else : ?>
                                            <?php echo $title; ?>
                                        <?php endif; ?>
                                    </h2>
                                    <p class="movie-genre"><?php echo $genre; ?></p>
                                    <p class="movie-plot"><?php echo $plot ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                </th>
                <th>
                    <div class="table-deildu-actions">
                        <?php
                        $action_type = "";
                        if (!is_null($content_url)) {
                            $action_type = "table-deildu-action";
                        }
                        else {
                            $action_type = "table-deildu-action-double";
                        }
                        ?>
                        <div class="<?php echo $action_type; ?> action-first">
                            <a href="process_wish.php?title=<?php echo $title ?>"><img src="images/add_icon.png" alt="Add movie" /></a>
                        </div>
                        <?php if (!is_null($content_url)) : ?>
                            <div class="<?php echo $action_type; ?> action-second">
                                <a href="<?php echo $content_url; ?>" target="_blank"><img src="images/themoviedb_icon.png" alt="TMDB" /></a>
                            </div>
                        <?php endif; ?>
                        <div class="<?php echo $action_type; ?> action-third">
                            <a href="<?php echo $torrent_url; ?>" target="_blank"><img src="images/deildu_logo.jpg" alt="Deildu" /></a>
                        </div>
                    </div>
                </th>
            </tr>
        <?php
    }
?>
