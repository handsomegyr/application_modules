<?php
namespace Test;

use Webcms\Weixin\Models\Source;
use Webcms\Weixin\Models\Keyword;
use Webcms\Weixin\Models\Reply;
use Webcms\Weixin\Models\Application;
use Webcms\Weixin\Models\User;
use Webcms\Weixin\Models\NotKeyword;
use Webcms\Weixin\Models\Menu;
use Webcms\Weixin\Models\Qrcode;
use Webcms\Weixin\Models\Scene;

/**
 * Class UnitTest
 */
class UnitTest extends \UnitTestCase
{

    public function testTestCase()
    {
        $this->assertEquals('works', 'works', 'This is OK');
        
        $this->assertEquals('works', 'works1', 'This will fail');
    }
    
    public function test2TestCase()
    {
        $this->assertEquals('works', 'works', 'This is OK');
        
        $this->assertEquals('works', 'works1', 'This will fail');
        //$modelKeyword = new Keyword();
        //$lowKey = $modelKeyword->keyword2lower('ABC');
        //$this->assertEquals($lowKey, 'abc', 'This is OK');
    }
}