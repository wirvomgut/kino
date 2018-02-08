<?php

namespace AppBundle\Utils;

use Symfony\Component\DependencyInjection\Container;
use Tmdb\Model\Common\Video;
use Tmdb\Model\Movie;
use Tmdb\Model\Movie\QueryParameter\AppendToResponse;
use Tmdb\Model\Movie\Release;
use Tmdb\Repository\MovieRepository;

class MyMovie
{

    /**
     * @var MovieRepository $movieRepository
     */
    private $movieRepository;

    /**
     * Constructor assigns service container to private container.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->movieRepository = $container->get('tmdb.movie_repository');
    }

    /**
     * @param $id
     * @return Movie
     */
    public function getMovie($id)
    {
        /** @var Movie $movie */
        $movie = $this->movieRepository->load(
            $id,
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
                ]),
            ]
        );

        return $movie;
    }

    public function getCertification($id)
    {
        /** @var Release $release */
        $release = $this->getMovie($id)->getReleases()->filterCountry('DE')->getIterator()->current();

        $certification = '';
        if ($release) {
            $certification = $release->getCertification();
            if ($certification === '') {
                $certification = '-';
            }
        }

        return $certification;
    }

    /**
     * @param $id
     * @return array
     */
    public function getVideoKey($id)
    {
        /** @var Video $video */
        $video = $this->getMovie($id)->getVideos()->getIterator()->current();
        $videoKey = $video ? $video->getKey() : '';

        return $videoKey;
    }
}
