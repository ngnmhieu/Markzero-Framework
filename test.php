<?

$db = new PDO('mysql:host=localhost;dbname=sale_db;charset=utf8', 
              'root', '');
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;

try {
  $stmt = $db->query("SELECT * FROM sales1");
  print_r($stmt->fetchAll(PDO::FETCH_ASSOC)); 
} catch (PDOException $e) {
  echo "Some errors occurred. Please contact the administrator. <br />";
}

echo "<br />";
echo "<br />";
echo "<br />";
echo "#######\n";
echo "Continuing with the web application\n";

$stmt = $db->prepare("SELECT * FROM customer WHERE customer_id =:id");
$id = 0;
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

foreach (array(1,2,3) as $id) {
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo "<pre>";
  print_r($rows);
}

echo "#######\n";
