<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
</head>
<body>

<?php

require ("class.imdb.php");

$data = new IMDB('http://www.imdb.com/title/tt0317248/');




echo '<b>Title : </b>';
echo $data->getMovieTitle();
echo '<br>';

echo '<b>Also Known As : </b>';
echo $data->getAKAMovieTitle();
echo '<br>';

echo '<b>Year : </b>';
echo $data->getMovieYear();
echo '<br>';

echo '<b>Rating : </b>';
echo $data->getMovieRating();
echo '<br>';

echo '<b>Description : </b>';
echo $data->getMovieDescription();
echo '<br>';

echo '<b>Storyline : </b>';
echo $data->getMovieStoryline();
echo '<br>';

echo '<b>Director URL: </b>';
echo implode(' | ' , $data->getMovieDirectorWithURL() );
echo '<br>';

echo '<b>Director String: </b>';
echo implode(' | ' , $data->getMovieDirector() );
echo '<br>';

echo '<b>Genre URL: </b>';
echo $data->getMovieGenreWithURL();
echo '<br>';

echo '<b>Genre String: </b>';
echo implode(' | ' , $data->getMovieGenre() );
echo '<br>';

echo '<b>Actor URL: </b>';
echo implode(' | ' , $data->getMovieActorWithURL() );
echo '<br>';

echo '<b>Actor: </b>';
echo implode(' | ' , $data->getMovieActor() );
echo '<br>';

echo '<b>Writer URL: </b>';
echo implode(' | ' , $data->getMovieWriter() );
echo '<br>';

echo '<b>MPAA Rating : </b>';
echo $data->getMovieMPAARating();
echo '<br>';

echo '<b>Tagline : </b>';
echo $data->getMovieTagline();
echo '<br>';

echo '<b>Website : </b>';
echo implode(' | ' , $data->getMovieSites() );
echo '<br>';

$country = $data->getMovieCountry();

echo '<b>Country URL: </b>';
echo implode(' | ',$country['link']);
echo '<br>';

echo '<b>Country String: </b>';
echo implode(' | ',$country['string']);
echo '<br>';

$language = $data->getMovieLanguage();

echo '<b>Language URL: </b>';
echo implode(' | ',$language['link']);
echo '<br>';

echo '<b>Language String: </b>';
echo implode(' | ',$language['string']);
echo '<br>';

echo '<b>Release Date: </b>';
echo $data->getMovieReleaseDate();
echo '<br>';

echo '<b>Filming Locations: </b>';
echo implode(' | ', $data->getMovieFilmingLocation());
echo '<br>';

echo '<b>Budget: </b>';
echo $data->getMovieBudget();
echo '<br>';

echo '<b>Opening Weekend: </b>';
echo $data->getMovieOpeningWeekend();
echo '<br>';

echo '<b>Gross: </b>';
echo $data->getMovieGross();
echo '<br>';

echo '<b>Movie Runtime: </b>';
echo $data->getMovieRuntime();
echo '<br>';

echo '<b>Sound Mix: </b>';
echo $data->getMovieSoundMix();
echo '<br>';

echo '<b>Color: </b>';
echo $data->getMovieColor();
echo '<br>';

echo '<b>Aspect Ratio: </b>';
echo $data->getMovieAspectRatio();
echo '<br>';

$poster = $data->getMoviePoster();
echo '<b>Small Poster : </b><br>';
echo '<img src="'.$poster['small'].'">';

echo '<br>';

echo '<b>Big Poster : </b><br>';
echo '<img src="'.$poster['big'].'">';

?>

</body>

</html>