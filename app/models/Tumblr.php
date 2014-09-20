<?php
class Tumblr extends AppModel {
  private static $client; // returned by self::getClient()
  private static $blog_list;

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
