<?php
namespace KriSpiX\VideothequeBundle\Dvdfr;

class Api
{
    public function __construct($searchUrl, $dvdUrl)
    {
        $this->searchUrl = $searchUrl;
        $this->dvdUrl    = $dvdUrl;
    }
    
    public function curlXML($path)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $path,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0'
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }
    
    public function searchEan($ean)
    {
        //Titre, synopsys, date, image, ean, format, genre?
        $url = $this->searchUrl . $ean;
        $resp = $this->curlXML($url);
        $xml = simplexml_load_string($resp);
        $dvdfrId = $xml->dvd[0]->id;
        
        $url = $this->dvdUrl . $dvdfrId;
        $resp = $this->curlXML($url);
        $xml = simplexml_load_string($resp);
        
        $title      = (string)$xml->titres->fr;
        $url        = (string)$xml->url;
        $overview   = (string)$xml->synopsis;
        $movieDate  = (string)$xml->sortie;
        $image      = (string)$xml->cover;
        $format     = (string)$xml->media;
        $genres = array();
        foreach($xml->categories->children() as $categorie) {
            $genres[] = (string)$categorie;
        }
        return array(
            'title'     => $title,
            'url'       => $url,
            'overview'  => $overview,
            'movieDate' => $movieDate,
            'image'     => $image,
            'format'    => $format,
            'genres'    => $genres,
            'keywords'  => array(),
        );
    }    
}