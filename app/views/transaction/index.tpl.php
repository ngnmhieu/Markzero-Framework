<table id="transaction-list" class="table table-hover">
<?php foreach($transactions as $t): ?>
  <tr class="transaction">
    <td><?=$t->category->name?></td>
    <td align="right">$ <?=$t->amount?></td>
    <td align="right">
      <a href="<?=path('transaction_edit',[$t->id])?>"><span class="glyphicon glyphicon-pencil"></span></a>
      <a href="<?=path('transaction_delete',[$t->id])?>"><span class="glyphicon glyphicon-trash"></span></a>
    </td>
  </tr>
  <tr class="moreinfo active">
    <td colspan=3>
      <div class="content">
            <?=$t->notice?>
            <span class="float-right"><?=$t->time->format('d M Y')?></span>    
      </div>
    </td>
  </tr>
<?php endforeach; ?>
</table>

  <a href="<?=path('transaction_add')?>">
    <button class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> New Transaction</button>
  </a>
