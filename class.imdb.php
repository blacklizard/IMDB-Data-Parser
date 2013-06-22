<?php
/**
 * PHP IMDB / IMDB.com information parser/scraper
 *
 * Provides an api to retrieve movie information from IMDB.com via Web scraping
 *
 *
 * @author        blacklizard (https://www.facebook.com/icodewithlizard)
 * @website       http://www.icodewithlizard.com/
 * @link          https://github.com/blacklizard/IMDB-Data-Parser     
 * @license       Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported
 * @version       2.0.0 (22nd June 2013)
 */

class IMDB
{



    /**
     * Holds data from IMDB website
     *
     * @var string
     */
    private $imdbSitedata;



    /**
     * Is tv series?
     *
     * @var string
     */
    private $isTV;



    /**
     * Constructor.
     *
     * @param string IMDB website url as input and store the site data in $imdbSitedata
     */
    function __construct($imdb_site = null)
    {
        try {

            if (!function_exists('curl_init')) {
                throw new Exception('You need cURL!');
            }

            if ($imdb_site == null) {
                throw new Exception('Imdb site not specified');
            } else {
                $this->imdbSitedata = $this->_getSiteContent($imdb_site);

                $isTV = false;

                $this->_isTVCheck();
            }

        }
        catch (Exception $e) {

            echo $e->getMessage();
            die();
        }
    }
    


    /**
     * Get Movie title
     *
     * @return string
     */
    public function getMovieTitle()
    {
        return $this->_preg_match('/<title>(.+?) \((?:.*)\) - IMDb<\/title>/s');
    }



    /**
     * Get AKA Movie title
     *
     * @return string
     */
    public function getAKAMovieTitle()
    {
        return trim ( $this->_preg_match('/Also Known As:<\/h4>(.+?)<span/s') );     
    }
    


    /**
     * Get Movie year
     *
     * @return string
     */
    public function getMovieYear()
    {
        if($this->isTV){
            $tvYear = $this->_preg_match('/Year:<\/h4>(.+?)<\/div>/s'); 
            $tvYear = $this->_preg_match_all('/<a href="(?:.*)" >(.+?)<\/a>/U',$tvYear);

            $yearCount = count($tvYear[1]);

            if($yearCount == 1){
                return $tvYear[1][0];
            }else{
                return $tvYear[1][$yearCount-1].' - '.$tvYear[1][0];
            }

        }else{
            return $this->_preg_match('/href="\/year\/(.+?)\/\?/s'); 
        }

    }
    


    /**
     * Get Movie rating
     *
     * @return string
     */
    public function getMovieRating()
    {
        return $this->_preg_match('/<span itemprop="ratingValue">(.+?)<\/span>/s');
    }
    


    /**
     * Get Movie Storyline
     *
     * @return string
     */
    public function getMovieStoryline()
    {
        return trim ( $this->_preg_match('/<div class="inline canwrap" itemprop="description">\s+<p>(.+?)<em/s') );
    } 



    /**
     * Get Movie Description
     *
     * @return string
     */
    public function getMovieDescription()
    {
        return trim ( $this->_preg_match('/<p itemprop="description">(.+?)<\/p>/s') );
    }
    


    /**
     * Get Movie MPAA Rating
     *
     * @return string
     */
    public function getMovieMPAARating()
    {
        return $this->_preg_match('/itemprop="contentRating">(.+?)<\/span>/s');
    }
    


    /**
     * Get Movie poster url
     * big image can be accessed at index big
     * small image can be accessed at index small
     *
     * @return array
     */
    public function getMoviePoster()
    {
        $url = $this->_preg_match('/Poster"\s*src="(.+?)"\s*itemprop="image"\s*\/>/s');
        
        $urls['small'] = $url;

        $urls['big'] = substr ( $url , 0 , (strlen ( $url ) ) - ( strlen ( strchr ( $url , '_' ) ) ) ).'_V1._SX352_SY500_.jpg';

        return $urls;
    }



    /**
     * Get Movie director(s) with url
     *
     * @return array
     */
    public function getMovieDirectorWithURL()
    {
        $directors = explode(',',$this->_preg_match('/(?:Director|Directors):<\/h4>(.*)<\/div>/sU'));

        $directorArray = array();

        foreach ($directors as $director) {
            $directorArray[] = trim( $director );
        }

        return $directorArray;
    }



    /**
     * Get Movie director(s)
     *
     * @return array
     */
    public function getMovieDirector()
    {
        $direcorArray = $this->getMovieDirectorWithURL();

        $directorsResult = array();

        foreach ($direcorArray as $director) {
            $directorsResult[] = $this->_preg_match('/<a href="\/name\/(?:.*)\/\?ref_=tt_ov_dr" itemprop=\'url\'><span class="itemprop" itemprop="name">(.+?)<\/span><\/a>/s', $director);
        }
        
        return $directorsResult;
    }



    /**
     * Get Movie Genre(s) with url
     *
     * @return string
     */
    public function getMovieGenreWithURL()
    {
        return $this->_preg_match('/(?:Genres|Genre):<\/h4>(.+?)<\/div>/s');
    }



    /**
     * Get Movie Genre(s)
     *
     * @return array
     */
    public function getMovieGenre()
    {
        $genres = explode( '|' , $this->_preg_match('/(?:Genres|Genre):<\/h4>(.+?)<\/div>/s') );

        $genreArray = array();

        foreach ($genres as $genre) {
           $genreArray[] = trim( $this->_preg_match('/<a href="\/genre\/(?:.*)\?ref_=tt_stry_gnr" >(.+?)<\/a>/s',$genre) );
        }

        return $genreArray;       
    }	



    /**
     * Get Movie Actor(s) with URL
     * Limited to 3 star actor
     *
     * @return array
     */
    public function getMovieActorWithURL()
    {
        $actors = explode( ',' , trim( $this->_preg_match('/(?:Stars|Star):<\/h4>(.+?)<span class="ghost">/s') ) );

        $actorsArray = array();

        foreach ($actors as $actor) {
            $actorsArray[] = trim( $actor );
        }

        return $actorsArray;
    }



    /**
     * Get Movie Actor(s)
     * Limited to 3 star actor
     *
     * @return array
     */
    public function getMovieActor()
    {
        $actorsArray =  $this->getMovieActorWithURL();

        $stringActorArray = array();

        foreach ($actorsArray as $actor) {
            $stringActorArray[] = $this->_preg_match('/itemprop="name">(.+?)<\/span>/s',$actor);
        }
        
        return $stringActorArray;
    }



    /**
     * Get Movie Write(s) URL
     *
     * @return string
     */
    public function getMovieWriterURL()
    {
        $writers = explode( ',' , $this->_preg_match('/(?:Writers|Writer):<\/h4>(.+?)<\/div>/s') ) ;
        return $writers;
    }



    /**
     * Get Movie Write(s)
     *
     * TODO: Write url helper to return the name as string with their role. Right now this 
     *       function return as href link   
     *       Ex: Chazz Palminteri (play), Chazz Palminteri (screenplay) - A Bronx Tale
     *
     * @return string
     */
    public function getMovieWriter()
    {
        $writers = $this->getMovieWriterURL();
        return $writers;
    }



    /**
     * Get Movie Tagline
     *
     * @return string
     */
    public function getMovieTagline()
    {
        return $this->_preg_match('/(?:Taglines|Tagline):<\/h4>(.+?)(?:<span|<\/div>)/s');
    }



    /**
     * Get Movie Website(s) as href
     *
     * @return array
     */
    public function getMovieSites()
    {
        $data = $this->_preg_match('/Official Sites:<\/h4>(.+?)<span class="see-more inline">/s');
        
        $urlArray = array();

        if($data){
            $urlLink = $this->_preg_match_all('/<a rel="nofollow" href="(.*)" itemprop=\'url\'>(.*)<\/a>/sU',$data);

            foreach ($urlLink[1] as $key => $url) {
                $urlArray[] = '<a href="'.$url.'">'.$urlLink[2][$key].'</a>';
            }  
        }

        return $urlArray;
    }



    /**
     * Get Movie Country(s)
     *
     * @return array
     */
    public function getMovieCountry()
    {
        $data = $this->_preg_match('/Country:<\/h4>(.+?)<\/div>/s');
        $CountryArray = array();
        
        if($data){
            $CountryLink = $this->_preg_match_all('/<a href="(?:.*)" itemprop=\'url\'>(.*)<\/a>/sU',$data);

            foreach ($CountryLink[0] as $key => $country) {
               $CountryArray['link'][] = $country;
               $CountryArray['string'][] = $CountryLink[1][$key];
            } 
        }


       return $CountryArray;
    }



    /**
     * Get Movie Language(s)
     *
     * @return array
     */
    public function getMovieLanguage()
    {
        $data = $this->_preg_match('/(?:Language|Languages):<\/h4>(.+?)<\/div>/s');
        $LanguageArray = array();

        if($data){

            $LanguageLink = $this->_preg_match_all('/<a href="(.*)" itemprop=\'url\'>(.*)<\/a>/sU',$data);

            foreach ($LanguageLink[0] as $key => $language) {
               $LanguageArray['link'][] = '<a href="'.$LanguageLink[1][$key].'" >'.$LanguageLink[2][$key].'</a>';
               $LanguageArray['string'][] = $LanguageLink[2][$key];
            } 
        }

        return $LanguageArray;
    }



    /**
     * Get Movie release date
     *
     * @return string
     */
    public function getMovieReleaseDate()
    {
        $ReleaseDate = $this->_preg_match('/Release Date:<\/h4>(.+?)<span class="see-more inline">/s');
        return trim( $ReleaseDate );
    }



    /**
     * Get Movie Filming Location(s)
     *
     * @return array
     */
    public function getMovieFilmingLocation()
    {
        $FilmingLocation = $this->_preg_match('/Filming Locations:<\/h4>\s*<a href="(?:.*)" itemprop=\'url\'>(.+?)<\/a>\s*<span class="see-more inline">/s');
 
        $FilmingLocation = explode(',',$FilmingLocation);

        foreach ($FilmingLocation as $key => $location) {
            $FilmingLocation[$key] = trim($location);
        }

        return $FilmingLocation;
    }



    /**
     * Get Movie Budget
     *
     * @return string
     */
    public function getMovieBudget()
    {
        $budget = $this->_preg_match('/Budget:<\/h4>(.+?)<span class="attribute">/s');
        return trim($budget);
    }



    /**
     * Get Movie Opening Weekend
     *
     * @return string
     */
    public function getMovieOpeningWeekend()
    {
        $openingWeekend = $this->_preg_match('/Opening Weekend:<\/h4>(.+?)\(/s');
        return trim($openingWeekend);
    }



    /**
     * Get Movie Gross
     *
     * @return string
     */
    public function getMovieGross()
    {
        $gross = $this->_preg_match('/Gross:<\/h4>(.+?)<span class="attribute">/s');
        return trim($gross);
    }



    /**
     * Get Movie Runtime
     *
     * @return string
     */
    public function getMovieRuntime()
    {
        $runtime = $this->_preg_match('/Runtime:<\/h4>(.+?)<\/div>/s');

        $runtime = $this->_preg_match_all('/<time itemprop="duration" datetime="(?:.*)">(.+?)<\/time>/U',$runtime);

        return implode(' | ',$runtime[1]);
    }



    /**
     * Get Movie Sound Mix
     *
     * @return string
     */
    public function getMovieSoundMix()
    {
        $SoundMix = $this->_preg_match('/Sound Mix:<\/h4>(.+?)<\/div>/s');

        $SoundMix = $this->_preg_match_all('/<a href="(?:.*)" itemprop=\'url\'>(.+?)<\/a>/U',$SoundMix);

         return implode(' | ',$SoundMix[1]);
    }



    /**
     * Get Movie Color
     *
     * @return string
     */
    public function getMovieColor()
    {
        $Color = $this->_preg_match('/Color:<\/h4>(.+?)<\/div>/s');

        $Color = $this->_preg_match_all('/<a href="(?:.*)" itemprop=\'url\'>(.+?)<\/a>/U',$Color);

        return implode(' | ',$Color[1]);       
    }



    /**
     * Get Movie Aspect Ratio
     *
     * @return string
     */
    public function getMovieAspectRatio()
    {
        $AspectRatio = $this->_preg_match('/Aspect Ratio:<\/h4>(.+?)<\/div>/s');

        return trim($AspectRatio);
    }



    /**
     * Retrive data from IMDB website through cURL
     *
     * @param string $site
     *
     * @return string
     */
    private function _getSiteContent($site = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $site);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.baidu.com');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)');
        $sitedata = curl_exec($ch);
        curl_close($ch);
        return $sitedata;
    }
    


    /**
     * Custom wrapper for preg_match.
     *
     * @param string $pattern
     * @param string $source - if source is null, it will take data from $imdbSitedata
     *
     * @return string
     */
    private function _preg_match($pattern = null, $source = null)
    {
        try {
            if ($pattern == null) {
                throw new Exception('Pattern not specified');
            } else {
                if ($source == null) {
                    $source = $this->imdbSitedata;
                }
                if (preg_match($pattern, $source, $hit)) {
                    return $hit[1];
                } else {
                    return false;
                }
            }
        }
        catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }



    /**
     * Custom wrapper for preg_match_all.
     *
     * @param string $pattern
     * @param string $source - if source is null, it will take data from $imdbSitedata
     *
     * @return string
     */
    private function _preg_match_all($pattern = null, $source = null)
    {
        try {
            if ($pattern == null) {
                throw new Exception('Pattern not specified');
            } else {
                if ($source == null) {
                    $source = $this->imdbSitedata;
                }
                if (preg_match_all($pattern, $source, $hit)) {
                    return $hit;
                } else {
                    return 'Data not available';
                }
            }
        }
        catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }



    /**
     * Check if it's tv series
     *
     * @return nothing
     */
    private function _isTVCheck()
    {
        $tv = $this->_preg_match('/<meta property=\'og:type\' content="(.+?)" \/>/s');
        if($tv == 'video.tv_show'){
            $this->isTV = true;
        }
    }		
}

?>