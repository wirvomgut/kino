<?php

namespace AppBundle\Controller;

use AppBundle\Utils\MyMovie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * @param array $eventId
     * @param $date
     * @return array
     */
    private function getEvent($eventId, $date)
    {
        /** @var MyMovie $movie */
        $movie = $this->get('my_movie.service');
        $event['movie'] = $movie->getMovie($eventId['id']);
        $event['date'] = $date;
        $event['special'] = $eventId['special'];
        $event['category'] = $eventId['category'];
        $event['certification'] = $movie->getCertification($eventId['id']);
        $event['videoKey'] = $movie->getVideoKey($eventId['id']);

        return $event;
    }

    /**
     * @param array $eventIds
     * @param bool $new
     * @return array
     */
    private function getEvents($eventIds, $new = true)
    {
        krsort($eventIds);

        $events = [];
        $now = time();

        foreach ($eventIds as $date => $value) {
            if ($new === true && strtotime($date) > $now) {
                $events[] = $this->getEvent($value, $date);
            } elseif ($new === false && strtotime($date) < $now) {
                $events[] = $this->getEvent($value, $date);
            }
        }

        return $events;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $eventIds = Yaml::parseFile(realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR . 'app/config/events.yml');
        $events = $this->getEvents($eventIds);

        krsort($events);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'pageTitle' => 'Programm',
            'events' => $events,
        ]);
    }

    /**
     * @Route("/archive", name="archive")
     */
    public function archiveAction(Request $request)
    {
        $eventIds = Yaml::parseFile(realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR . 'app/config/events.yml');
        $events = $this->getEvents($eventIds, false);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'pageTitle' => 'Archiv',
            'events' => $events,
        ]);
    }
}
