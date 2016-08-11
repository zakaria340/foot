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
        ['label' => 'Bet']
      )
      ->getForm();
    $form->handleRequest($request);
    $apiUrl = $this->getParameter('url_football_api');
    $matchday = $this->getParameter('matchday');
    $api_token = $this->getParameter('api_token');
    $fixtures = array();
    for ($i = 1; $i <= $matchday; $i++) {
      $urlFixtures = $apiUrl . "competitions/$code/fixtures?matchday=$i";
      $reqPrefs['http']['method'] = 'GET';
      $reqPrefs['http']['header'] = "X-Auth-Token: $api_token";
      $stream_context = stream_context_create($reqPrefs);
      $response = file_get_contents($urlFixtures, FALSE, $stream_context);
      $fixtures[] = json_decode($response);
    }
    $fixtures = array_reverse($fixtures);
    $user = $this->get('security.token_storage')->getToken()->getUser();

    $em = $this->getDoctrine()->getManager();
    $itemEntity = $em->getRepository('AppBundle:Bet')->findOneBy(
      array('fixture' => $code, 'matchday' => $matchday, 'user' => $user)
    );
    $dataBet = array();
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
      unset($data['betuser']);

      $json_data = json_encode($data);
      $bet->setData($json_data);

      $em = $this->getDoctrine()->getManager();

      $bet->setFixture($code);
      $bet->setMatchday($matchday);
      $bet->setUser($user);

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
        //resultMatch
        $score->setResultMatch($resultMatch);
        $em->persist($score);
      }

      $em->persist($bet);
      $em->flush();

    }
    $status = 0;
    if ($itemEntity) {
      $status = $itemEntity->getStatus();
    }
    return $this->render(
      'default/fixtures.html.twig',
      [
        'fixtures' => $fixtures,
        'codematchs' => $code . $matchday,
        'form' => $form->createView(),
        'dataBet' => $dataBet,
        'status' => $status,
      ]
    );
  }


  /**
   * @return array
   */
  public function getListCompetitions() {
    $competitions = [
      [
        'id' => 395,
        'caption' => "Premier League 2016/17",
        "league" => "PL",
        "year" => "2016",
        "currentMatchday" => 1,
        "numberOfMatchdays" => 38,
        "numberOfTeams" => 20,
        "numberOfGames" => 380,
      ],
      [
        "id" => 430,
        "caption" => "1. Bundesliga 2016/17",
        "league" => "BL1",
        "year" => "2016",
        "currentMatchday" => 1,
        "numberOfMatchdays" => 34,
        "numberOfTeams" => 18,
        "numberOfGames" => 306,
      ],
      [
        "id" => 436,
        "caption" => "Primera Division 2016/17",
        "league" => "PD",
        "year" => "2016",
        "currentMatchday" => 1,
        "numberOfMatchdays" => 38,
        "numberOfTeams" => 20,
        "numberOfGames" => 380,
      ],
      [
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
   * @Route("/cron", name="cron")
   */
  public function cronAction(Request $request) {
    $listData = array();
    $matchday = $this->getParameter('matchday');
    $listCompetition = $this->getListCompetitions();

    foreach ($listCompetition as $competition) {
      $fixture = $this->getFixtures($competition['id'], $matchday);
      $listData[$competition['id']] = $fixture;
    }
    $em = $this->getDoctrine()->getManager();
    $itemEntity = $em->getRepository('AppBundle:Bet')->findBy(
      array('matchday' => $matchday, 'status' => 0)
    );
    foreach ($itemEntity as $bet) {

      $scores = $bet->getScores();
      $dataUser = [];
      foreach ($scores as $score) {
        $dataUser[$score->getIdfootball()] = [
          'scoreHome' => $score->getScoreHome(),
          'scoreAway' => $score->getScoreAway(),
        ];
      }
      $dataUser = json_decode($bet->getData(), TRUE);
      $correctData = $listData[$bet->getFixture()];
      $fixtureCorrectData = $correctData->fixtures;
      $pointsToWin = $bet->getPoints();
      $i = 0;
      foreach ($dataUser as $key => $value) {
        if ($value['scoreHome'] == $fixtureCorrectData[$i]->result->goalsHomeTeam && $value['scoreAway'] && $fixtureCorrectData[$i]->result->goalsAwayTeam) {
          $pointsToWin = $pointsToWin + 5;
        }
        $boolCorrect = (boolean) $fixtureCorrectData[$i]->result->goalsHomeTeam - $fixtureCorrectData[$i]->result->goalsAwayTeam;
        $boolUser = (boolean) $value['scoreHome'] - $value['scoreAway'];

        if ($boolCorrect === $boolUser) {
          $pointsToWin = $pointsToWin + 3;
        }
        $i++;
      }
      $bet->setPoints($pointsToWin);
      $bet->setStatus(1);
      $em->persist($bet);
      $em->flush();
    }
  }

  /**
   * @Route("/cron-user", name="cronuser")
   */
  public function cronuserAction(Request $request) {
    $em = $this->getDoctrine()->getManager();
    $userEntity = $em->getRepository('AppBundle:User')->findAll();
    foreach ($userEntity as $user) {
      $points = 0;
      foreach ($user->getBets() as $bet) {
        $points += (int) ($bet->getPoints());
      }
      $user->setPoints($points);
      $em->persist($user);
      $em->flush();
    }

  }


  /**
   * @Route("/calcul-bet", name="calculbet")
   */
  public function calculguessAction(Request $request) {
    $em = $this->getDoctrine()->getManager();
    $matchday = $this->getParameter('matchday');
    $itemEntity = $em->getRepository('AppBundle:Bet')->findBy(
      array('matchday' => $matchday, 'status' => 0)
    );

    foreach ($itemEntity as $bet) {
      $scores = $bet->getScores();
      foreach ($scores as $score) {
        $value = [
          'scoreHome' => $score->getScoreHome(),
          'scoreAway' => $score->getScoreAway(),
        ];

        $nbr = $this->calculscorebet($value, $score->getIdfootball(), $bet);
        $nbrTotal = $this->calculscorebet($value, $score->getIdfootball());
        $prc = $nbr * 100 / $nbrTotal;
        $score->setBetScors($prc);

        $nbrResult = $this->calculscorebet(
          ['resultMatch' => $score->getresultMatch()],
          $score->getIdfootball(),
          $bet
        );
        $nbrTotalResult = $this->calculscorebet(
          ['resultMatch' => $score->getresultMatch()],
          $score->getIdfootball()
        );
        $prcReslut = $nbrResult * 100 / $nbrTotalResult;
        $score->setBetResults($prcReslut);

        $em->persist($score);
        $em->flush();
      }
    }
  }


  public function calculscorebet($value, $idfootball, $bet = []) {
    $em = $this->getDoctrine()->getManager();
    $params = $value;
    $params['status'] = 0;
    $params['idfootball'] = $idfootball;
    if (!empty($bet)) {
      $params['bet'] = $bet;
    }
    $itemEntity = $em->getRepository('AppBundle:Score')->findBy($params);
    return count($itemEntity);
  }

  /**
   * @Route("/calcul-guess", name="calculguess")
   */
  public function calculresultAction(Request $request) {
    $em = $this->getDoctrine()->getManager();
    $matchday = $this->getParameter('matchday');

    $itemEntity = $em->getRepository('AppBundle:Bet')->findBy(
      array('matchday' => $matchday, 'status' => 0)
    );

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


}
