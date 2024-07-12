<!-- head -->
<?php require view('partials/head.php'); ?>
<!-- Nav bar -->
<?php require view('./partials/nav.php') ?>
<!-- banner -->
<?php require view('./partials/banner.php') ?>

  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-6">
        <a href="/notes/create">
             Add New Note
        </a>
      </button>

      <ul>
      <?php foreach ($notes as $note) : ?>
        <li>
        <a href="/note?id=<?= $note['id'] ?>" class="text-blue-500 hover:underline">

             <?= htmlspecialchars($note['body']) ?>
        </a>
        </li>
        <?php endforeach; ?>
      </ul>

    </div>
  </main>

<!-- footer -->
<?php require view('./partials/footer.php'); ?>