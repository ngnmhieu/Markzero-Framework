<a href="<?=path('transaction_index')?>">Return to Listing</a>
<br />
<br />
<?php if (flash_exist('error')): ?>
<div class="error">
  <?=flash('error')?>
</div>
<?php endif; ?>

<?php $t = $transaction ;?>
<form action="/transaction/<?=$t->id?>" method="post">
  <label for="amount">Amount: </label>
  <div class="input-group">
    <span class="input-group-addon">$</span>
    <input type="text" name="amount" class="form-control" value="<?=$t->amount?>">
  </div>
  <br />
  <label for="notice">Notice: </label>
  <textarea class="form-control" placeholder="Notice ..." name="notice" rows="3" cols="40"><?=$t->notice?></textarea>
  <br />
  <label for="category[id]">Category: </label>
  <select class="form-control" name="category[id]">
  <?php foreach($categories as $c): ?>
    <option value="<?=$c->id?>" <?=selected($c->id, $t->category->id)?>><?=$c->name?></option>
  <?php endforeach; ?>
  </select>  
  <br />
  <label for="time">Time </label>
  <input type="text" class="form-control" name="time" id="datepicker" value="<?=$t->time->format('d/m/Y')?>" />
  <br />
  <input type="submit" class="btn btn-primary" name="submit" value="Add Transaction" />
</form>
