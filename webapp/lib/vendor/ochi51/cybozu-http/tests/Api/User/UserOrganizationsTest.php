<?php

namespace CybozuHttp\Tests\Api\User;

require_once __DIR__ . '/../../_support/UserTestHelper.php';
use UserTestHelper;

use EasyCSV\Reader;
use CybozuHttp\Api\UserApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserOrganizationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserApi
     */
    private $api;

    protected function setup()
    {
        $this->api = UserTestHelper::getUserApi();

        $filename = __DIR__ . '/../../_data/users.csv';
        $id = $this->api->users()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $content = $this->api->organizations()->getByCsv();
        $path = __DIR__ . '/../../_output/export-organizations.csv';
        file_put_contents($path, $content);

        $filename = __DIR__ . '/../../_data/orgs.csv';
        $id = $this->api->organizations()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $filename = __DIR__ . '/../../_data/titles.csv';
        $id = $this->api->titles()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }
    }

    public function testGet()
    {
        $config = UserTestHelper::getConfig();
        $this->api->userOrganizations()->get($config['login']);
        self::assertTrue(true);
    }

    public function testCsv()
    {
        $filename = __DIR__ . '/../../_data/user-orgs.csv';
        $id = $this->api->userOrganizations()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $content = $this->api->userOrganizations()->getByCsv();
        $path = __DIR__ . '/../../_output/export-user-orgs.csv';
        file_put_contents($path, $content);
        $getCsv = new Reader($path, 'r+', false);
        while ($row = $getCsv->getRow()) {
            if ('example-title1' === reset($row)) {
                self::assertEquals($row, [
                    'test1@example.com','example-org1','example-title1'
                ]);
            }
            if ('example-title2' === reset($row)) {
                self::assertEquals($row, [
                    'test2@example.com','example-org2','example-title2'
                ]);
            }
        }
    }

    protected function tearDown()
    {
        $filename = __DIR__ . '/../../_data/delete-users.csv';
        $id = $this->api->users()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $filename = __DIR__ . '/../../_output/export-organizations.csv';
        $id = $this->api->organizations()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }

        $filename = __DIR__ . '/../../_data/delete-titles.csv';
        $id = $this->api->titles()->postByCsv($filename);
        while (1) {
            $result = $this->api->csv()->result($id);
            if (!$result['done']) {
                continue;
            }
            if ($result['success']) {
                self::assertTrue(true);
            } else {
                self::fail($result['errorCode']);
            }
            break;
        }
    }
}
