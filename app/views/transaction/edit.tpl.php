<form action="/transaction/<?=$transaction->id?>/update" method="post">
  <label for="amount">Amount: </label>
  <input type="text" name="amount" value="<?=$transaction->amount?>" />
  <br />
  <label for="notice">Notice: </label>
  <textarea name="notice" rows="3" cols="40"><?=$transaction->notice?></textarea>
  <br />
  <input type="submit" name="submit" value="Update Transaction" />
</form>
