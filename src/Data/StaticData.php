<?php
namespace Markzero\Data;

/**
 * Static data are stored in JSON files.
 * Data is represented like directory structure.
 * e.g: "database/info.json" => $data->database->info
 *      "tumblr/api/key.json" => $data->tumblr->api->key
 */
class StaticData {
  private $data; // stdClass: stores all the loaded data

  /**
   * @param string Directory where data are stored
   */
  public function __construct($directory) {
    $this->data = $this->recursiveLoadData($directory);
  }

  /*
   * Recursively scan through the $dir directory
   * and load any .json file encountered
   * @param string $dir | directory where data are stored
   * @return stdClass Object that contain json_decoded result
   */
  private function recursiveLoadData($dir) {
    $result = new \stdClass(); 

    $cdir = scandir($dir); 
    foreach ($cdir as $node) { 
      if (!in_array($node,array(".",".."))) { // normal node name - not `.` or `..`
        if (is_dir($dir.'/'.$node)) { // if current node is dir, load recursively in it
          $result->{$node} = $this->recursiveLoadData($dir.'/'.$node); 
        } else if (preg_match('/^(.*)\.json$/', $node, $matches)) {
          $json = file_get_contents($dir.'/'.$node);
          $result->{$matches[1]} = json_decode($json); 
        } 
      } 
    } 

    return $result; 
  }

  public function __get($name) {
    return $this->data->{$name};
  }

}
