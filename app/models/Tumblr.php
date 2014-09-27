<?php
class Tumblr extends AppModel {
  private static $client; // returned by self::getClient()
  private static $blog_list; // returned by self::getBlogs();

  public static function getShuffledPhotos($limit = 10) {
    $photos = self::getPhotos($limit);
    shuffle($photos);
    return $photos;
  }

  public static function getPhotos($limit = 10) {
    $client = self::getClient();
    $blogs = self::getBlogs();

    $photos = array();
    foreach ($blogs as $blog) {
      $blog_name = $blog.".tumblr.com";
      $posts = $client->getBlogPosts($blog_name, ['type' => 'photo', 'limit' => $limit])->posts; 
      foreach ($posts as $post) {
        $photos[] = $post->photos[0]->original_size; 
      }
    }

    return $photos;
  }

  public static function searchTaggedPhoto($tag = "") {
    $client = self::getClient();

    date_default_timezone_set('Europe/Berlin');
    // $time = mktime(0,0,0, 8, 30, 2014);
    $time = time();
    $posts = $client->getTaggedPosts($tag, ['before' => $time]);
    $photo_posts = array_filter($posts, function($post) {
      return $post->type == "photo";
    });


    // put all the photos into an array and return it
    return array_reduce($photo_posts, function ($result, $post) {
      foreach ($post->photos as $photo) 
        $result[] = $photo->original_size;
      return $result;
    }, array());

  }

  /*
   * @return $client | tumblr client main object
   */
  private static function getClient() {
    if (isset(self::$client))
      return self::$client;
    
    $config = App::$data->tumblr;
    self::$client = new Tumblr\API\Client($config->consumer_key, $config->secret_key); 
    return self::$client;
  }

  /*
   * @return $blogs | tumblr blogs, where contents come from
   */
  private static function getBlogs() {
    if (isset(self::$blog_list))
      return self::$blog_list;

    self::$blog_list = App::$data->tumblr->blogs;
    return self::$blog_list;
  }
}
