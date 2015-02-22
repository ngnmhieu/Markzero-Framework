[
<?php $count = count($categories) ?>
<?php foreach($categories as $i => $cat): ?>
{
  "id": <?=$cat->id?>,
  "name": "<?=$cat->name?>",
  "description": "<?=$cat->description?>"
} <?= ++$i == $count ? '' : ',' ?>
<?php endforeach; ?>
]
