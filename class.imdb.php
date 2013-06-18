<?php
/**
 * PHP IMDB information parser/scraper
 *
 * Provides an api for retrieving movie information from IMDB
 *
 * PHP 5 with CURL
 *
 * Copyright 2011-2013, blacklizard(https://www.facebook.com/icodewithlizard)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011-2013, blacklizard(https://www.facebook.com/icodewithlizard)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version       1.0.1 (19th June 2013)
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
     * Constructor.
     *
     * @param $imdb_site takes IMDB website url as input and store the site data in $imdbSitedata
     */
    function __construct($imdb_site = null)
    {
        try {
            if ($imdb_site == null) {
                throw new Exception('Imdb site not specified');
            } else {
                $this->imdbSitedata = $this->_getSiteContent($imdb_site);
            }
        }
        catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
    
    /**
     * Retrive data from IMDB website through
     *
     * @param string $site
     * @return string
     */
    private function _getSiteContent($site = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $site);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $sitedata = curl_exec($ch);
        curl_close($ch);
        return $sitedata;
    }
    
    /**
     * Custom wrapper for preg_match.
     *
     * @param string $pattern
     * @param string $source
     - if source is null, it will take data from $imdbSitedata
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
                    var_dump($hit);
                    return $hit[1];
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
     * Get Movie title
     *
     * @return string
     */
    public function getMovieTitle()
    {
        return $this->_preg_match('/<span class="itemprop" itemprop="name">(.+?)<\/span>/s');
    }
    
    /**
     * Get Movie year
     *
     * @return string
     */
    public function getMovieYear()
    {
        return $this->_preg_match('/href="\/year\/(.+?)\/\?/s');
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
     * Get Movie plot
     *
     * @return string
     */
    public function getMoviePlot()
    {
        return trim ( $this->_preg_match('/<div class="inline canwrap" itemprop="description">\s+<p>(.+?)<em/s') );
	}
    
    /**
     * Get Movie MPAA Rating
     *
     * @return string
     */
    public function getMovieMPAARating()
    {
        return $this->_preg_match('/<span itemprop="contentRating">(.+?)<\/span>/s');
    }

    /**
     * Get Movie director
     *
     * @return string
     */
    public function getMovieDirector()
    {
        return $this->_preg_match('/(?:Director|Directors):<\/h4>(.*)<\/div>/sU');

    }
    
    /**
     * Get Movie big poster url
     *
     * @return string
     */
    public function getMovieBigPoster()
    {
        $imageLink      = $this->_preg_match('/<td rowspan="2" id="img_primary">\s*<a\s*onclick="\(new Image\(\)\)\.src=\'\/rg\/title\-overview\/primary\/images\/b.gif\?link=%2Fmedia%2Frm[0-9]*%2Ftt[0-9]*\'\;"\s*href="(.+?)"\s*><img src="/s');
        $largeImageLink = 'http://www.imdb.com' . $imageLink;
        $largeImagedata = $this->_getSiteContent($largeImageLink);
        return $this->_preg_match('/<img id="primary-img" itemprop="contentURL" title="[\x0-\x7A]*" alt="[\x0-\x7A]*"  src="(.+?)"  data-rmconst="[\x0-\x7A]*"  onmousedown="return false\;" onmousemove="return false\;" oncontextmenu="return false\;" \/>/s', $largeImagedata);
    }

    /**
     * Get Movie Genre
     *
     * @return string
     */
    public function getMovieGenre()
    {
        return $this->_preg_match('/(?:Genres|Genre):<\/h4>(.+?)<\/div>/s');
    }
	
    /**
     * Get Movie Actors
     *
     * @return string
     */
    public function getMovieActor()
    {
        preg_match_all('#<td class="name">\s*<a\s*onclick="(?:.*)"\s*href="/name/nm(\d*)/"\s*>(.*)</a>\s*</td>#Ui',$this->imdbSitedata,$hit);
		return implode('&nbsp;|&nbsp;',array_unique($hit[2]));
    }		
}

?>