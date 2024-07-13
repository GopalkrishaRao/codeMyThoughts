<!-- head -->
<?php 
require(view('partials/head.php'))
?>

<!-- Nav bar -->
<?php require base_path('view/partials/nav.php'); ?>
<!-- banner -->
<?php require base_path('view/partials/banner.php'); ?>


  
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <p>Hello, <?= $_SESSION['user']['email'] ?? 'Guest' ?>. Welcome to the home page.</p>
    </div>
  </main>

<!-- footer -->
<?php require base_path('./view/partials/footer.php'); ?>