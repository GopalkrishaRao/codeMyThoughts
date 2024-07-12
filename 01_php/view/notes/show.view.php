<!-- head -->
<?php require view('partials/head.php'); ?>
<!-- Nav bar -->
<?php require view('./partials/nav.php') ?>
<!-- banner -->
<?php require view('./partials/banner.php') ?>
<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
   
    <button>
        <a href="/notes" class="text-blue-500 hover:underline">Go Back..</a>
    </button>
        <p> <?= htmlspecialchars($note['body']) ?> </p>
        
    </div>
  </main>

<!-- footer -->
<?php require view('./partials/footer.php'); ?>