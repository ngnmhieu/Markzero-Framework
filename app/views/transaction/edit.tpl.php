<div style="padding: 10px: border: 1px solid red">
  <?=flash('error')?>
</div>

<a href="<?=path('transaction_edit', [$transaction->id])?>">Edit me</a>

<form action="/transaction/<?=$transaction->id?>/update" method="post">
  <label for="amount">Amount: </label>
  <input type="text" name="amount" value="<?=$transaction->amount?>" />
  <br />
  <label for="notice">Notice: </label>
  <textarea name="notice" rows="3" cols="40"><?=$transaction->notice?></textarea>
  <br />
  <input type="submit" name="submit" value="Update Transaction" />
</form>
