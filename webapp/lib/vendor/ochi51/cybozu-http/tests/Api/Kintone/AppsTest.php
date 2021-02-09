<?php

namespace CybozuHttp\Tests\Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class AppsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    /**
     * @var integer
     */
    private $spaceId;

    /**
     * @var array
     */
    private $space;

    /**
     * @var integer
     */
    private $guestSpaceId;

    /**
     * @var array
     */
    private $guestSpace;

    /**
     * @var integer
     */
    private $appId;

    /**
     * @var integer
     */
    private $guestAppId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->space = $this->api->space()->get($this->spaceId);
        $this->guestSpaceId = KintoneTestHelper::createTestSpace(true);
        $this->guestSpace = $this->api->space()->get($this->guestSpaceId, $this->guestSpaceId);

        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $this->space['defaultThread']);
        $this->guestAppId = KintoneTestHelper::createTestApp($this->guestSpaceId, $this->guestSpace['defaultThread'], $this->guestSpaceId);
    }

    public function testGet()
    {
        $app = $this->api->apps()->get([$this->appId], [], null, [$this->spaceId])['apps'][0];
        self::assertEquals($app['appId'], $this->appId);
        self::assertEquals($app['name'], 'cybozu-http test app');
        self::assertEquals($app['spaceId'], $this->spaceId);
        self::assertEquals($app['threadId'], $this->space['defaultThread']);

        $app = $this->api->apps()->get([$this->guestAppId], [], null, [$this->guestSpaceId], 100, 0, $this->guestSpaceId)['apps'][0];
        self::assertEquals($app['appId'], $this->guestAppId);
        self::assertEquals($app['name'], 'cybozu-http test app');
        self::assertEquals($app['spaceId'], $this->guestSpaceId);
        self::assertEquals($app['threadId'], $this->guestSpace['defaultThread']);
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
        $this->api->space()->delete($this->guestSpaceId, $this->guestSpaceId);
    }
}
