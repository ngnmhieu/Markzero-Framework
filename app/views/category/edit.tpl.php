<form action="/category/<?=$category->id?>/update" method="post">
  <label for="name">Name: </label>
  <input type="text" name="name" value="<?=$category->name?>" />
  <br />
  <label for="description">Description: </label>
  <input type="text" name="description" value="<?=$category->description?>" />
  <br />
  <input type="submit" name="submit" value="Update Category" />
</form>
