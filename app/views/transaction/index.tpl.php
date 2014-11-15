<table border=1>
  <th>Time</th>
  <th>Amount</th>
  <th>Notice</th>
  <th>Category</th>
  <th>Action</th>
<?php foreach($transactions as $t): ?>
  <tr>
    <td><?=$t->time->format('d-m-Y h:m')?></td>
    <td><?=$t->amount?></td>
    <td><?=$t->notice?></td>
    <td><?=$t->category->name?></td>
    <td>
      <a href="/transaction/<?=$t->id?>/edit">Edit</a>
      |
      <a href="/transaction/<?=$t->id?>/delete">Delete</a>
    </td>
  </tr>   
<?php endforeach; ?>
</table>

<a href="/transaction/add">[ New Transaction ]</a>
