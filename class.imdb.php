<?php
/**
 * IMDB information parser
 *
 * Provides an api for retrieving movie information from IMDB
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
        return $this->_preg_match('/<h1 class="header" itemprop="name">(.+?)<span/s');
    }
    
    /**
     * Get Movie year
     *
     * @return string
     */
    public function getMovieYear()
    {
        return $this->_preg_match('/<span class="nobr">\(<a href="\/year\/[0-9]*\/">(.+?)<\/a>\)<\/span>/s');
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
        $plotData = $this->_preg_match('/Storyline<\/h2>\s*<p>(.+?)<em class="nobr">/s');
		if($plotData == 'Data not available'){
			return $this->_preg_match('/Storyline<\/h2>\s*<p>(.+?)<\/p>/s');
		}
		else{
			return $plotData;
		}
	}
    
    /**
     * Get Movie director
     *
     * @return string
     */
    public function getMovieDirector()
    {
        return $this->_preg_match('/Director:\s*<\/h4>\s*<a\s*onclick="(?:.*)"\s*href="\/name\/[a-z0-9]*\/"\s*itemprop="director"\s*>(.+?)<\/a>/s');
    }
    
    /**
     * Get Movie MPAA Rating
     *
     * @return string
     */
    public function getMovieMPAARating()
    {
        return $this->_preg_match('/<div class="infobar">\s*<img width="18" alt="R" src="[\x0-\x7A]*" class="absmiddle" title="(.*?)" height="15">/s');
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
        preg_match_all('#href="/genre/(.*)"#Ui',$this->imdbSitedata,$hit);
		return implode('&nbsp;|&nbsp;',array_unique($hit[1]));
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