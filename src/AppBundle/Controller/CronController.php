<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Bet;
use AppBundle\Entity\User;
use AppBundle\Entity\Score;

class CronController extends Controller {


  /**
   * @Route("/cron", name="cron")
   */
  public function cronAction(Request $request) {
    $listData = array();
    $listCompetition = $this->getListCompetitions();

    foreach ($listCompetition as $competition) {
      $matchday = $this->getParameter('matchday_'.$competition['id']);
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
          'resultMatch' => $score->getBetResults(),
          'BetScors' => $score->getBetScors(),
        ];
      }
      $correctData = $listData[$bet->getFixture()];
      $fixtureCorrectData = $correctData->fixtures;
      $pointsToWin = (int) $bet->getPoints();
      $i = 0;
      foreach ($dataUser as $key => $value) {
        if ($value['scoreHome'] != '' && $value['scoreAway'] != '') {
          if ($value['scoreHome'] == $fixtureCorrectData[$i]->result->goalsHomeTeam && $value['scoreAway'] == $fixtureCorrectData[$i]->result->goalsAwayTeam) {
            if ($value['BetScors'] < 20) {
            $pointsToWin = (int)$pointsToWin + 7;
            }
            else {
            $pointsToWin = (int)($pointsToWin) + 5;
            }
          }
          else {
            $boolCorrect = (boolean) $fixtureCorrectData[$i]->result->goalsHomeTeam - $fixtureCorrectData[$i]->result->goalsAwayTeam;
            $boolUser = (boolean) $value['scoreHome'] - $value['scoreAway'];
            if ($boolCorrect === $boolUser) {
             $pointsToWin = (int)$pointsToWin + 3;

            }
          }
        }
        $i++;
      }

      $bet->setPoints($pointsToWin);
      $bet->setStatus(1);
      $em->persist($bet);
      $em->flush();
    }


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
    $itemEntity = $em->getRepository('AppBundle:Bet')->findBy(
      array('status' => 0)
    );

    foreach ($itemEntity as $bet) {
      $scores = $bet->getScores();
      foreach ($scores as $score) {
        $value = [
          'scoreHome' => $score->getScoreHome(),
          'scoreAway' => $score->getScoreAway(),
        ];

        $nbr = $this->calculscorebet($value, $score->getIdfootball(), $bet);
        $nbrTotal = $this->calculscorebet([], $score->getIdfootball());
        $prc = $nbr * 100 / $nbrTotal;
        $score->setBetScors($prc);

        $nbrResult = $this->calculscorebet(
          ['resultMatch' => $score->getresultMatch()],
          $score->getIdfootball(),
          $bet
        );
        $nbrTotalResult = $this->calculscorebet(
          [],
          $score->getIdfootball()
        );

        $prcReslut = $nbrResult * 100 / $nbrTotalResult;
        $score->setBetResults($prcReslut);

        $em->persist($score);
        $em->flush();
      }
    }
    DIE('A');
  }


  /**
   * @param $value
   * @param $idfootball
   * @param array $bet
   * @return int
   */
  public function calculscorebet($value, $idfootball, $bet = []) {
    $em = $this->getDoctrine()->getManager();
    $params = $value;
    $params['status'] = 0;
    $params['idfootball'] = $idfootball;
    if (!empty($bet)) {
      //$params['bet'] = $bet;
    }
    $itemEntity = $em->getRepository('AppBundle:Score')->findBy($params);
    return count($itemEntity);
  }


  /**
   * @return array
   */
  public function getListCompetitions() {
    $competitions = [
      [
        'id' => 426,
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
