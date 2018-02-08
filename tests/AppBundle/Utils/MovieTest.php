<?php

namespace Tests\AppBundle\Utils;

use AppBundle\Utils\MyMovie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieTest extends WebTestCase
{
    /**
     * @var MyMovie $movie
     */
    private $movie;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->movie = $kernel->getContainer()->get('my_movie.service');
    }

    public function testIfCanBeCreateMovie()
    {
        $this->assertInstanceOf(MyMovie::class, $this->movie);
    }

    public function testIfCertificationIsCorrect()
    {
        $this->assertEquals('12', $this->movie->getCertification(19995));
    }

    public function testIfVideoTitleIsCorrect()
    {
        $this->assertEquals('Avatar - Aufbruch nach Pandora', $this->movie->getMovie(19995)->getTitle());
    }

    public function testIfVideoKeyIsCorrect()
    {
        $this->assertEquals('8TNlvM4cN6U', $this->movie->getVideoKey(19995));
    }
}
