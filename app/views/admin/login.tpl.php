<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
</head>
<body>
<?=var_dump($_SERVER);?>
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <label for="username">Username: </label>
     <input type="text" name="username" id="" placeholder="Your Username" />
<br />
  <label for="password">Password: </label>
   <input type="password" name="password" id="" placeholder="Password" />
<br />
<button type="submit">Login</button>
   </form>
</body>
</html>
