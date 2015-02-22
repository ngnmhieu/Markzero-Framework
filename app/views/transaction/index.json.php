
[
  <?php $count = count($transactions); ?>
  <?php foreach ($transactions as $i => $t): ?>
    {
      "id": <?=$t->id?>,
      "amount": "<?=$t->amount?>",
      "category": {
        "name": "<?=$t->category->name?>"
      },
      "notice": "<?=$t->notice?>",
      "time": "<?=$t->time->format('d-m-Y')?>"
    } <?= ++$i == $count ? '' : ',' ?>
  <?php endforeach; ?>
]
