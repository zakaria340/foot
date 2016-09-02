<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Bet;
use AppBundle\Entity\User;
use AppBundle\Entity\Score;

class DefaultController extends Controller {


  /**
   * @Route("/", name="homepage")
   */
  public function indexAction(Request $request) {
    $competitions = $this->getListCompetitions();

    return $this->render(
      'default/index.html.twig',
      [
        'competitions' => $competitions,
        'base_dir' => realpath(
          $this->getParameter('kernel.root_dir') . '/..'
        ),
      ]
    );
  }

  /**
   * 
   */
  public function menuAction(Request $request) {
    $competitions = $this->getListCompetitions();
    return $this->render(
      'default/menu.html.twig',
      [
        'competitions' => $competitions,
        'base_dir' => realpath(
          $this->getParameter('kernel.root_dir') . '/..'
        ),
      ]
    );
  }

  /**
   * @Route(
   *     "/competitions/{code}/fixtures",
   *     name="fixtures",
   * )
   */
  public function fixturesAction($code, Request $request) {
    $form = $this->createFormBuilder()
      ->add(
        'bet',
        SubmitType::class,
        ['label' => 'Save']
      )
      ->getForm();
    $form->handleRequest($request);
    $matchday = $this->getParameter('matchday_'.$code);
    $fixtures = array();
    for ($i = 1; $i <= $matchday; $i++) {
      $fixtures[] = $this->getFixtures($code, $i);
    }
    $fixtures = array_reverse($fixtures);
    $user = $this->get('security.token_storage')->getToken()->getUser();

    $em = $this->getDoctrine()->getManager();
    $itemEntity = $em->getRepository('AppBundle:Bet')->findOneBy(
      array('fixture' => $code, 'matchday' => $matchday, 'user' => $user)
    );
    $dataBet = array();
    if ($form->isSubmitted() && $form->isValid()) {
      $data = $_POST;
      unset($data['form']);
      if (isset($data['betuser'])) {
        $bet = $itemEntity;
        $dataBet = [
          'id' => $itemEntity->getId(),
          'scores' => $_POST,
        ];
      }
      else {
        $bet = new Bet();
      }

      $em = $this->getDoctrine()->getManager();
      $bet->setFixture($code);
      $bet->setMatchday($matchday);
      $bet->setUser($user);
      if(isset($data['betuser'])){
        foreach($bet->getScores() as $score){
          $score->setScoreAway(trim($data[$score->getIdfootball()]['scoreAway']));
          $score->setScoreHome(trim($data[$score->getIdfootball()]['scoreHome']));

          if (trim($data[$score->getIdfootball()]['scoreAway']) > trim($data[$score->getIdfootball()]['scoreHome'])) {
            $resultMatch = 'win';
          }
          elseif (trim($data[$score->getIdfootball()]['scoreAway']) == trim($data[$score->getIdfootball()]['scoreHome'])) {
            $resultMatch = 'draw';
          }
          else {
            $resultMatch = 'loose';
          }
          $score->setResultMatch($resultMatch);

          $em->persist($score);
          $em->flush();
        }
      }else{
        foreach ($data as $key => $item) {
          $score = new Score();
          $score->setScoreAway(trim($item['scoreAway']));
          $score->setScoreHome(trim($item['scoreHome']));
          $score->setIdfootball($key);
          $score->setBet($bet);
          if (trim($item['scoreAway']) > trim($item['scoreHome'])) {
            $resultMatch = 'win';
          }
          elseif (trim($item['scoreAway']) == trim($item['scoreHome'])) {
            $resultMatch = 'draw';
          }
          else {
            $resultMatch = 'loose';
          }
          $score->setResultMatch($resultMatch);
          $em->persist($score);
        }
      }

      unset($data['betuser']);
      $json_data = json_encode($data);
      $bet->setData($json_data);
      $em->persist($bet);
      $em->flush();
    }

    if ($itemEntity) {
      $scores = $itemEntity->getScores();
      $data = array();
      foreach ($scores as $score) {
        $data[$score->getIdfootball()] = [
          'scoreHome' => $score->getScoreHome(),
          'scoreAway' => $score->getScoreAway(),
          'resultMatch' => $score->getBetResults(),
          'BetScors' => $score->getBetScors(),
        ];
      }
      $dataBet = [
        'id' => $itemEntity->getId(),
        'scores' => $data,
      ];
    }
    $status = 0;
    if ($itemEntity) {
      $status = $itemEntity->getStatus();
    }
    $competitions = $this->getListCompetitions();


    foreach($fixtures as $fixture){
      foreach($fixture->fixtures as $c){
        //var_dump($c->_links->homeTeam->href);die('r');
      }
    }
    return $this->render(
      'default/fixtures.html.twig',
      [
        'fixture' => $competitions[$code],
        'fixtures' => $fixtures,
        'code' => $code,
        'codematchs' => $code . $matchday,
        'form' => $form->createView(),
        'dataBet' => $dataBet,
        'status' => $status,
        'competitions' => $competitions,
        'LogoClubs' => $this->MappingLogoClubs(),
      ]
    );
  }


  /**
   * @return array
   */
  public function getListCompetitions() {
    $competitions = [
      426=> [
        'id' => 426,
        'caption' => "Premier League 2016/17",
        "league" => "PL",
        "year" => "2016",
        "currentMatchday" => 1,
        "numberOfMatchdays" => 38,
        "numberOfTeams" => 20,
        "numberOfGames" => 380,
      ],
      430=>[
        "id" => 430,
        "caption" => "1. Bundesliga 2016/17",
        "league" => "BL1",
        "year" => "2016",
        "currentMatchday" => 1,
        "numberOfMatchdays" => 34,
        "numberOfTeams" => 18,
        "numberOfGames" => 306,
      ],
      436=>[
        "id" => 436,
        "caption" => "Primera Division 2016/17",
        "league" => "PD",
        "year" => "2016",
        "currentMatchday" => 1,
        "numberOfMatchdays" => 38,
        "numberOfTeams" => 20,
        "numberOfGames" => 380,
      ],
      438=>[
        "id" => 438,
        "caption" => "Serie A 2016/17",
        "league" => "SA",
        "year" => "2016",
        "currentMatchday" => 1,
        "numberOfMatchdays" => 38,
        "numberOfTeams" => 20,
        "numberOfGames" => 380,
      ],
    ];

    return $competitions;
  }

  /**
   * @Route("/rating", name="rating")
   */
  public function ratingAction(Request $request) {
    $em = $this->getDoctrine()->getManager();
    $users = $em->getRepository('AppBundle:User')
      ->findBy(array(), array('points' => 'DESC'));

    return $this->render(
      'default/rating.html.twig',
      [
        'users' => $users,
      ]
    );
  }

  /**
   * @param $code
   * @param $matchday
   * @return mixed
   */
  public function getFixtures($code, $matchday) {
    $apiUrl = $this->getParameter('url_football_api');
    $api_token = $this->getParameter('api_token');
    $urlFixtures = $apiUrl . "competitions/$code/fixtures?matchday=$matchday";
    $reqPrefs['http']['method'] = 'GET';
    $reqPrefs['http']['header'] = "X-Auth-Token: $api_token";
    $stream_context = stream_context_create($reqPrefs);
    $response = file_get_contents($urlFixtures, FALSE, $stream_context);
    $fixtures = json_decode($response);
    return $fixtures;
  }


  public function MappingLogoClubs(){
    return [
      'Hull City FC' => 'HUL',
      'Leicester City FC' => 'LEI',
      'Southampton FC' => 'SOU',
      'Watford FC' => 'WAT',
      'Middlesbrough FC' => 'MID',
      'Stoke City FC' => 'STK',
      'Everton FC' => 'EVE',
      'Tottenham Hotspur FC' => 'TOT',
      'Crystal Palace FC' => 'CRY',
      'West Bromwich Albion FC' => 'WBA',
      'Burnley FC' => 'BUR',
      'Swansea City FC' => 'SWA',
      'Manchester City FC' => 'MCI',
      'Sunderland AFC' => 'SUN',
      'AFC Bournemouth' => 'BOU',
      'Manchester United FC' => 'MUN',
      'Arsenal FC' => 'ARS',
      'Liverpool FC' => 'LIV',
      'Chelsea FC' => 'CHE',
      'West Ham United FC' => 'WHU',

      'FC Bayern München' => 'FCB',
      'Werder Bremen' => 'SVW',
      'FC Augsburg' => 'FCA',
      'VfL Wolfsburg' => 'WOB',
      'Borussia Dortmund' => 'BVB',
      '1. FSV Mainz 05' => 'M05',
      'Eintracht Frankfurt' => 'SGE',
      'FC Schalke 04' => 'F04',
      'Hamburger SV' => 'HSV',
      'FC Ingolstadt 04' => 'FCI',
      '1. FC Köln' => 'EFFZEH',
      'SV Darmstadt 98' => 'DAR',
      'Bor. Mönchengladbach' => 'BMG',
      'Bayer Leverkusen' => 'B04',
      'Hertha BSC' => 'BSC',
      'SC Freiburg' => 'SCF',
      'TSG 1899 Hoffenheim' => 'TSG',
      'Red Bull Leipzig' => 'RBL',


      'FC Bayern München' => 'FCB',
      'Werder Bremen' => 'SVW',
      'FC Augsburg' => 'FCA',
      'VfL Wolfsburg' => 'WOB',
      'Borussia Dortmund' => 'BVB',
      '1. FSV Mainz 05' => 'M05',
      'Eintracht Frankfurt' => 'SGE',
      'FC Schalke 04' => 'F04',
      'Hamburger SV' => 'HSV',
      'FC Ingolstadt 04' => 'FCI',
      '1. FC Köln' => 'EFFZEH',
      'SV Darmstadt 98' => 'DAR',
      'Bor. Mönchengladbach' => 'BMG',
      'Bayer Leverkusen' => 'B04',
      'Hertha BSC' => 'BSC',
      'SC Freiburg' => 'SCF',
      'TSG 1899 Hoffenheim' => 'TSG',
      'Red Bull Leipzig' => 'RBL'

    ];
  }

  public function thumbnail($str) {

    $idClub = basename($str);


    $club = 'http://api.football-data.org/v1/teams/'.$idClub;
    $api_token = 'e4e83dcdf73c4b9696b5025b22e1d783';
    $reqPrefs['http']['method'] = 'GET';
    $reqPrefs['http']['header'] = "X-Auth-Token: $api_token";
    $stream_context = stream_context_create($reqPrefs);
    $response = file_get_contents($club, FALSE, $stream_context);
    $clubData = json_decode($response);
    $wikiImage = $clubData->crestUrl;

    $urlBaseW = str_replace(basename($wikiImage), '', $wikiImage);


    $qsd = '25px-'.basename($wikiImage).'.png';
    $name = basename($wikiImage);

    $explodeurlBaseW = explode('/', $urlBaseW);
    array_splice( $explodeurlBaseW, 5, 0, 'thumb' );
    $urlBase = implode('/', $explodeurlBaseW);
    $wikiImageA = "$urlBase/$name/$qsd";

    return $wikiImageA;
  }
}
