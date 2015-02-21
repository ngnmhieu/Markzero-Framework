{
  "transactions": [
    <?php foreach ($transactions as $t): ?>
      {
        "id": <?=$t->id?>,
        "category": {
          "name": "<?=$t->category->name?>"
        },
        "notice": "<?=$t->notice?>"
        "time": "<?=$t->time->format('d-m-Y')?>",
      },
    <?php endforeach; ?>
  ]
}
