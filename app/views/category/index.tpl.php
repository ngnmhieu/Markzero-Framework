<table border=1>
  <th>Name</th>
  <th>Description</th>
  <th>Action</th>
<?php foreach($categories as $c): ?>
  <tr>
  <td><?=$c->name?></td>
  <td><?=$c->description?></td>
    <td>
      <a href="/category/<?=$c->id?>/edit">Edit</a>
      |
      <a href="/category/<?=$c->id?>/delete">Delete</a>
    </td>
  </tr>   
<?php endforeach; ?>
</table>

<a href="/category/add">[ New Category ]</a>
