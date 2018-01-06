<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tmdb\Model\Common\Video;
use Tmdb\Model\Movie;
use Tmdb\Model\Movie\Release;
use Tmdb\Model\Movie\QueryParameter\AppendToResponse;
use Tmdb\Repository\MovieRepository;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @return array
     */
    private function getEvents($eventIds)
    {
        $events = [];
        $i = 0;

        foreach ($eventIds as $eventId) {
            /** @var MovieRepository $movieRepository */
            $movieRepository = $this->get('tmdb.movie_repository');

            /** @var Movie $movie */
            $movie = $movieRepository->load(
                $eventId['id'],
                [
                    'language' => 'de',
                    new AppendToResponse([
                        AppendToResponse::ALTERNATIVE_TITLES,
                        AppendToResponse::CHANGES,
                        AppendToResponse::CREDITS,
                        AppendToResponse::IMAGES,
                        AppendToResponse::KEYWORDS,
                        AppendToResponse::LISTS,
                        AppendToResponse::RELEASES,
                        AppendToResponse::REVIEWS,
                        AppendToResponse::SIMILAR,
                        AppendToResponse::TRANSLATIONS,
                        AppendToResponse::VIDEOS,
                    ])
                ]
            );

            /** @var Release $release */
            $release = $movie->getReleases()->filterCountry('DE')->getIterator()->current();

            $certification = '';
            if ($release) {
                $certification = $release->getCertification();
                if ($certification === '') {
                    $certification = '-';
                }
            }

            /** @var Video $video */
            $video = $movie->getVideos()->getIterator()->current();

            $videoKey = '';
            if ($video) {
                $videoKey = $video->getKey();
            }

            $events[$i]['movie'] = $movie;
            $events[$i]['date'] = $eventId['date'];
            $events[$i]['special'] = $eventId['special'];
            $events[$i]['category'] = $eventId['category'];
            $events[$i]['certification'] = $certification;
            $events[$i]['videoKey'] = $videoKey;

            $i++;

            if ($i > 200) return $events;
        }
        return $events;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $eventIds = Yaml::parseFile(realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR . 'app/config/events.yml');
//        $events = $this->getEvents($eventIds);

        krsort($eventIds);
        $pastEvents = $this->getEvents($eventIds);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
//            'events' => $events,
            'pastEvents' => $pastEvents,
        ]);
    }
}
