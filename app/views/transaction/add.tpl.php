<form action="/transaction/" method="post">
  <label for="amount">Amount: </label>
  <input type="text" name="amount" value="" />
  <br />
  <label for="notice">Notice: </label>
  <textarea name="notice" rows="3" cols="40"></textarea>
  <!-- <br /> -->
  <!-- <label for="time">Time: </label> -->
  <br />
  <label for="category_id">Category: </label>
  <select id="" name="category_id">
  <?php foreach($categories as $c): ?>
    <option value="<?=$c->id?>"><?=$c->name?></option>
  <?php endforeach; ?>
  </select>  
  <br />
  <input type="submit" name="submit" value="Add Transaction" />
</form>
