<?php
class TransactionController extends AppController {
  function index() {
    echo "<pre>";
    // $cats = Transaction::find(5)->getCategories();
    // Doctrine\Common\Util\Debug::dump($cats); 

    // foreach ($cats as $cat) {
    //   echo $cat->getName();
    //   Doctrine\Common\Util\Debug::dump($cat->getTransactions()); 
    //   echo "<br />";
    // }

    $cat = Category::find(1);
    $trans = $cat->getTransactions();
    Doctrine\Common\Util\Debug::dump($cat); 
    Doctrine\Common\Util\Debug::dump($trans); 
  }  

  function create() {
    Transaction::create(-3000, "Nach Berlin", [1,2]);
    echo "Created";
  }
}
