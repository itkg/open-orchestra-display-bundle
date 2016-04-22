<?php

namespace OpenOrchestra\DisplayBundle\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;
use Phake;

/**
 * Test CacheableManagerTest
 */
class CacheableManagerTest extends AbstractBaseTestCase
{
    /**
     * @var CacheableManager
     */
    protected $manager;
    protected $tagHandler;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->tagHandler = Phake::mock('FOS\HttpCache\Handler\TagHandler');
        $this->manager = new CacheableManager($this->tagHandler);
    }

    /**
     * @param int $maxAge
     * @param int $expectedMaxAge
     * @param int $count
     *
     * @dataProvider provideMaxAge
     */
    public function testSetPublicResponseCacheParameters($maxAge, $expectedMaxAge, $count)
    {
        $response = Phake::mock('Symfony\Component\HttpFoundation\Response');

        $newResponse = $this->manager->setResponseCacheParameters($response, $maxAge, 'public', true);

        $this->assertSame($response, $newResponse);
        Phake::verify($newResponse)->setPublic();
        Phake::verify($newResponse, Phake::times($count-1))->setMaxAge($expectedMaxAge);
        Phake::verify($newResponse, Phake::times($count))->setSharedMaxAge($expectedMaxAge);

        $newResponse = $this->manager->setResponseCacheParameters($response, $maxAge, 'private', false);
        Phake::verify($newResponse)->setPrivate();

        Phake::verify($newResponse, Phake::times($count))->setMaxAge($expectedMaxAge);
        Phake::verify($newResponse, Phake::times($count-1))->setSharedMaxAge($expectedMaxAge);
    }

    /**
     * @param int $maxAge
     * @param int $expectedMaxAge
     * @param int $count
     *
     * @dataProvider provideMaxAge
     */
    public function testSetPrivateResponseCacheParameters($maxAge, $expectedMaxAge, $count)
    {
        $response = Phake::mock('Symfony\Component\HttpFoundation\Response');

        $newResponse = $this->manager->setResponseCacheParameters($response, $maxAge, 'private', false);

        $this->assertSame($response, $newResponse);
        Phake::verify($newResponse)->setPrivate();
        Phake::verify($newResponse, Phake::times($count))->setMaxAge($expectedMaxAge);
        Phake::verify($newResponse, Phake::times($count-1))->setSharedMaxAge($expectedMaxAge);
    }

    /**
     * @return array
     */
    public function provideMaxAge()
    {
        return array(
            array(300, 300, 1),
            array(-1, 2629743, 1),
            array(0, 0, 1)
        );
    }

    /**
     * Test method addCacheTags
     */
    public function testAddCacheTags()
    {
        $tags = array('tag1', 'tag2');

        $this->manager->addCacheTags($tags);

        Phake::verify($this->tagHandler)->addTags($tags);
    }

    /**
     * @param array $tags
     * 
     * @dataProvider provideTags
     */
    public function testInvalidateTags($tags)
    {
        $this->manager->invalidateTags($tags);

        Phake::verify($this->tagHandler)->invalidateTags($tags);
    }

    /**
     * @return array
     */
    public function provideTags()
    {
        return array(
            array(array()),
            array(array('tag1')),
            array(array('tag1', 'tag2', 'tag3'))
        );
    }
}
