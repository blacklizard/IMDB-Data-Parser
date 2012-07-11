<?php
/**
 * Example usage for IMDB information parser
 *
 * PHP 5 with CURL
 *
 * Copyright 2011-2012, blacklizard(https://www.facebook.com/icodewithlizard)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011-2012, blacklizard(https://www.facebook.com/icodewithlizard)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
require ("class.imdb.php");

$data = new IMDB('http://www.imdb.com/title/tt0111161/');
echo '<b>Title : </b>';
echo $data->_getMovieTitle();
echo '<br>';
echo '<b>Year : </b>';
echo $data->_getMovieYear();
echo '<br>';
echo '<b>Rating : </b>';
echo $data->_getMovieRating();
echo '<br>';
echo '<b>Plot : </b>';
echo $data->_getMoviePlot();
echo '<br>';
echo '<b>Director : </b>';
echo $data->_getMovieDirector();
echo '<br>';
echo '<b>MPAA Rating : </b>';
echo $data->_getMovieMPAARating();
echo '<br>';
echo '<b>Genre : </b>';
echo $data->_getMovieGenre();
echo '<br>';
echo '<b>Actor : </b>';
echo $data->_getMovieActor();
echo '<br>';
echo '<b>Poster : </b><br>';
echo '<img src="'.$data->_getMovieBigPoster().'">';

?>