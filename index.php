<?php include('includes/header.php');?>
<?php include('includes/navigation.php'); ?>

<div class="container">
    <div class="row">

    <?php include('includes/content.php'); ?>

    <!-- Dependiendo de la página, se mostrará los comentarios o no -->
    <?php 
      /* if(condicion) {
        include('includes/comments.php');
      }*/
    ?>
    <?php include('includes/sidebar.php'); ?>
    </div>
<?php include('includes/footer.php'); ?>