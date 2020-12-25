<?php include('includes/header.php');?>
<?php include('includes/navigation.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if(isset($_POST['username']) && isset($_POST['password'])){
      // Iniciar sesión
    loginUser($_POST['username'], $_POST['password']);
  } else {
    header("Location: /final");
  }
}
?>
<div class="container">
    <div class="row">
    <?php 
      if(isset($_SESSION['username'])) {
        echo "El usuario". $_SESSION['username']. " se encuentra logueado";
    ?>
    <p><a href="logout.php">Cerrar Sesión</a></p>
    <?php
      } else {
    ?>
    <form method="post">
          <legend>Login</legend>
          <fieldset>
            <div class="mb-3">
              <label for="username" class="form-label">Ingrese su usuario</label>
              <input type="text" class="form-control" id="username" name="username">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Ingrese su contraseña</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
          </fieldset>
        </form>
        
    </div>
    <?php } ?>

<?php include('includes/footer.php'); ?>