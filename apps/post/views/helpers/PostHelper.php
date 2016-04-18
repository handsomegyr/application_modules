<?php
namespace App\Post\Views\Helpers;

use App\Post\Models\Post;

class PostHelper extends \Phalcon\Tag
{

    /**
     * 获取帖子图的地址
     *
     * @return array
     */
    static public function getPostImage($baseUrl, $image, $x = 0, $y = 0)
    {
        $modelPost = new Post();
        return $modelPost->getImagePath($baseUrl, $image, $x, $y);
    }
}