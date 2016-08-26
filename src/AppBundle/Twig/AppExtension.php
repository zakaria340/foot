<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
  private $pdfDirectoryClubs = '';
  public function __construct($paramName1)
  {
    $this->pdfDirectoryClubs = $paramName1;
  }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('GenerateThumbnail', array($this, 'ThumbnailFilter')),
        );
    }

    public function ThumbnailFilter($title)
    {
     return $this->thumbnail($title);
    }

    public function getName()
    {
        return 'app_extension';
    }

      public function thumbnail($str) {
        $path = $this->pdfDirectoryClubs;
        $userPath = $path . '/clubs.json';
        $userFile = file_get_contents($userPath);
        $listUsers = json_decode($userFile, TRUE);
        $wikiImage = $listUsers[$str];
        $urlBaseW = str_replace(basename($wikiImage), '', $wikiImage);
        $pos = strpos($wikiImage, 'png');
        if ($pos === false) {
          $qsd = '25px-'.basename($wikiImage).'.png';
        } else {
          $qsd = '25px-'.basename($wikiImage);
        }
        $name = basename($wikiImage);
        $explodeurlBaseW = explode('/', $urlBaseW);
        array_splice( $explodeurlBaseW, 5, 0, 'thumb' );
        $urlBase = implode('/', $explodeurlBaseW);
        $wikiImageA = "$urlBase/$name/$qsd";
        return $wikiImageA;
    }
}