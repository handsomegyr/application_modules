<?php
namespace Test;

use App\Weixin\Models\Source;
use App\Weixin\Models\Keyword;
use App\Weixin\Models\Reply;
use App\Weixin\Models\Application;
use App\Weixin\Models\User;
use App\Weixin\Models\NotKeyword;
use App\Weixin\Models\Menu;
use App\Weixin\Models\Qrcode;
use App\Weixin\Models\Scene;

/**
 * Class UnitTest
 */
class UnitTest extends \UnitTestCase
{

    public function testTestCase()
    {
        $this->assertEquals('works', 'works', 'This is OK');
        
        //$this->assertEquals('works', 'works1', 'This will fail');
    }
    
    public function test2TestCase()
    {
        $this->assertEquals('works', 'works', 'This is OK');
        
        //$this->assertEquals('works', 'works1', 'This will fail');
        //$modelKeyword = new Keyword();
        //$lowKey = $modelKeyword->keyword2lower('ABC');
        //$this->assertEquals($lowKey, 'abc', 'This is OK');
    }
}