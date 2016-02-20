<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Eva\EvaOAuthTest\Utils;

use Eva\EvaOAuth\OAuth2\ResourceServerInterface;
use Eva\EvaOAuth\Utils\ResponseParser;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class ResponseParserTest extends \PHPUnit_Framework_TestCase
{

    public function testJSONP()
    {
        $res = ResponseParser::parseJSONP(
            new Response(200, [], Stream::factory('jQuery21305313212884880004_1427952242748({"status":"success"})'))
        );
        $this->assertArrayHasKey('status', $res);
    }

    public function testParse()
    {
        $res = ResponseParser::parse(
            new Response(200, [], Stream::factory('jQuery21305313212884880004_1427952242748({"status":"success"})')),
            ResourceServerInterface::FORMAT_JSONP
        );
        $this->assertArrayHasKey('status', $res);
    }

    /**
     * @expectedException Eva\EvaOAuth\Exception\InvalidArgumentException
     */
    public function testUnknowFormat()
    {
        ResponseParser::parse(
            new Response(200, [], Stream::factory('jQuery21305313212884880004_1427952242748({"status":"success"})')),
            'foo'
        );
    }
}
