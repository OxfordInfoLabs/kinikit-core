<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 10/10/2018
 * Time: 10:44
 */

namespace Kinikit\Core\HTTP;

include_once "autoloader.php";

class WebServiceProxyTest extends \PHPUnit\Framework\TestCase {


    public function testCanCreateSimpleNoParamsWebServiceProxyAndRetrieveResults() {

        $proxy = new WebServiceProxy("http://jsonplaceholder.typicode.com");

        $posts = $proxy->callMethod("posts", "GET", array(), null);
        $this->assertEquals(100, sizeof($posts));
        $this->assertTrue(is_array($posts[0]));
        $this->assertEquals(1, $posts[0]["userId"]);
        $this->assertEquals(1, $posts[0]["id"]);


        // Now attempt one with a mapped return value
        $posts = $proxy->callMethod("posts", "GET", array(), null, "Kinikit\Core\HTTP\Post[]");

        $this->assertEquals(100, sizeof($posts));
        $this->assertEquals(new Post(1, 1, "sunt aut facere repellat provident occaecati excepturi optio reprehenderit", "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"),
            $posts[0]);


    }


    public function testCanPassParametersThrough() {


        $proxy = new WebServiceProxy("http://jsonplaceholder.typicode.com");
        $comments = $proxy->callMethod("comments", "GET", array("postId" => 1), null, "Kinikit\Core\HTTP\Comment[]");

        $this->assertEquals(5, sizeof($comments));
        $this->assertEquals(new Comment(1, 1, "id labore ex et quam laborum", "Eliseo@gardner.biz", "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"), $comments[0]);


        $newPost = new Post(5, null, "Hello World", "Testing a new Post out.");
        $proxy = new WebServiceProxy("http://jsonplaceholder.typicode.com");
        $post = $proxy->callMethod("posts", "POST", null, $newPost, "Kinikit\Core\HTTP\Post");
        $this->assertEquals(new Post(5, 101, "Hello World", "Testing a new Post out."), $post);
    }


    public function testGlobalParametersAreMergedIn() {

        $proxy = new WebServiceProxy("http://jsonplaceholder.typicode.com", array("postId" => 1));
        $comments = $proxy->callMethod("comments", "GET", array(), null, "Kinikit\Core\HTTP\Comment[]");

        $this->assertEquals(5, sizeof($comments));
        $this->assertEquals(new Comment(1, 1, "id labore ex et quam laborum", "Eliseo@gardner.biz", "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"), $comments[0]);


    }


}
