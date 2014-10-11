<table border=1>
  <th>Time</th>
  <th>Amount</th>
  <th>Notice</th>
  <th>Action</th>
<? foreach($transactions as $t): ?>
  <tr>
    <td><?=$t->time->format('d-m-Y h:m')?></td>
    <td><?=$t->amount?></td>
    <td><?=$t->notice?></td>
    <td><a href="/transaction/<?=$t->id?>/delete">Delete</a></td>
  </tr>   
<? endforeach;?>
</table>

<a href="/transaction/add">[ New Transaction ]</a>
