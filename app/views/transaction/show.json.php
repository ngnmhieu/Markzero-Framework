{
  "id": <?=$transaction->id?>,
  "amount": "<?=$transaction->amount?>",
  "category": {
    "id": <?=$transaction->category->id?>,
    "name": "<?=$transaction->category->name?>"
  },
  "notice": "<?=$transaction->notice?>",
  "time": "<?=$transaction->time->format('d/m/Y')?>"
}
