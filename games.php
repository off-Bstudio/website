<?php
require_once __DIR__ . '/includes/functions.php';

$games = get_db()->query(
    "SELECT * FROM games ORDER BY position ASC, id ASC"
)->fetchAll();
$recruiting_open = is_recruiting_open();

$page_title = 'Games — Breaker Studio';
require __DIR__ . '/includes/site_head.php';

$count = count($games);
$plural_en = $count === 1 ? '' : 's';
$plural_fr = $count === 1 ? '' : 's';
?>

<section class="page-hero wrap">
  <div class="page-hero-mark"></div>
  <div class="eyebrow-row">
    <span class="dash"></span>
    <span class="eyebrow"
          data-en="<?= $count ?> title<?= $plural_en ?> shipped"
          data-fr="<?= $count ?> titre<?= $plural_fr ?> publié<?= $plural_fr ?>"><?= $count ?> titles shipped</span>
  </div>
  <h1 data-en="Every game breaks a rule" data-fr="Chaque jeu brise une règle">Every game breaks a rule</h1>
  <p data-en="From tactics to puzzle to platformer, each release starts with a convention we decided to ignore." data-fr="De la tactique au puzzle en passant par la plateforme, chaque sortie part d'une convention que nous avons choisi d'ignorer.">From tactics to puzzle to platformer, each release starts with a convention we decided to ignore.</p>
</section>

<section class="games wrap">
  <div class="games-grid">
    <?php foreach ($games as $game): $status = GAME_STATUS_LABELS[$game['status_key']]; ?>
    <div class="game-card reveal">
      <div class="game-art"><div class="<?= e($game['color']) ?>"></div></div>
      <div class="game-body">
        <span class="game-tag"
              data-en="<?= e($game['genre_en']) ?> · <?= e($game['year']) ?>"
              data-fr="<?= e($game['genre_fr']) ?> · <?= e($game['year']) ?>"><?= e($game['genre_en']) ?> · <?= e($game['year']) ?></span>
        <h3><?= e($game['title']) ?></h3>
        <p data-en="<?= e($game['description_en']) ?>" data-fr="<?= e($game['description_fr']) ?>"><?= e($game['description_en']) ?></p>
        <span class="game-status" data-en="<?= e($status['en']) ?>" data-fr="<?= e($status['fr']) ?>"><?= e($status['en']) ?></span>
      </div>
    </div>
    <?php endforeach; if (empty($games)): ?>
    <p class="empty-note" data-en="No games published yet — check back soon." data-fr="Aucun jeu publié pour l'instant — revenez bientôt.">No games published yet — check back soon.</p>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/hiring_cta.php'; ?>
<?php require __DIR__ . '/includes/site_foot.php'; ?>
