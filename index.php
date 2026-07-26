<?php
require_once __DIR__ . '/includes/functions.php';

$featured_games = get_db()->query(
    "SELECT * FROM games ORDER BY position ASC, id ASC LIMIT 3"
)->fetchAll();
$recruiting_open = is_recruiting_open();

$page_title = 'Breaker Studio — Pushing the boundaries of video games';
require __DIR__ . '/includes/site_head.php';
?>

<section class="hero">
  <svg class="hero-crack" viewBox="0 0 1180 700" preserveAspectRatio="none">
    <path d="M 940 0 L 890 120 L 970 180 L 900 260 L 1000 340 L 940 420 L 1020 520 L 960 700" />
    <path d="M 940 0 L 890 120 L 830 90 L 780 40" />
    <path d="M 970 180 L 1060 150" />
    <path d="M 900 260 L 810 300" />
  </svg>

  <div class="wrap hero-inner">
    <div class="eyebrow-row">
      <span class="dash"></span>
      <span class="eyebrow" data-en="Independent game studio — est. 2025" data-fr="Studio de jeu indépendant — fondé en 2025">Independent game studio — est. 2025</span>
    </div>

    <h1 id="hero-title">
      <span class="line"><span class="word" style="animation-delay:.05s">Pushing</span></span>
      <span class="line"><span class="word" style="animation-delay:.15s">the</span> <span class="word" style="animation-delay:.22s">boundaries</span></span>
      <span class="line"><span class="word" style="animation-delay:.32s">of</span> <span class="word" style="animation-delay:.4s">video</span> <span class="word" style="animation-delay:.48s">games</span></span>
    </h1>

    <p class="hero-sub" data-en="Breaker Studio builds games that don't play it safe — fracturing genre conventions to find the mechanic no one's shipped yet." data-fr="Breaker Studio conçoit des jeux qui ne jouent pas la carte de la prudence — nous brisons les conventions du genre pour trouver la mécanique que personne n'a encore livrée.">
        Breaker Studio builds games that don't play it safe — fracturing genre conventions to find the mechanic no one's shipped yet.
    </p>

    <div class="hero-cta">
      <a href="games.php" data-nav="games.php" class="btn btn-primary" data-en="See our games" data-fr="Voir nos jeux">See our games</a>
      <a href="careers.php" data-nav="careers.php" class="btn btn-ghost" data-en="Join the studio" data-fr="Rejoindre le studio">Join the studio</a>
    </div>
  </div>

  <div class="scroll-cue">
    <div class="bar"></div>
    <span data-en="Scroll" data-fr="Défiler">Scroll</span>
  </div>
</section>

<section class="manifesto wrap" id="manifesto">
  <div class="manifesto-grid">
    <p class="lead reveal" data-en="We think of every design rule as a fault line — something worth pressing on until it gives." data-fr="Nous voyons chaque règle de conception comme une ligne de faille — quelque chose qu'il vaut la peine de pousser jusqu'à la rupture.">
      We think of every design rule as a fault line — something worth pressing on until it gives.
    </p>

    <div class="manifesto-list">
      <div class="manifesto-item reveal">
        <span class="tag">01 / FRACTURE</span>
        <div>
          <h3 data-en="Break the genre first" data-fr="Briser le genre d'abord">Break the genre first</h3>
          <p data-en="Every project starts by naming the convention we intend to ignore, then building the mechanic that convention was hiding." data-fr="Chaque projet commence par identifier la convention que nous comptons ignorer, puis par construire la mécanique que cette convention dissimulait.">Every project starts by naming the convention we intend to ignore, then building the mechanic that convention was hiding.</p>
        </div>
      </div>
      <div class="manifesto-item reveal">
        <span class="tag">02 / REBUILD</span>
        <div>
          <h3 data-en="Prototype in public" data-fr="Prototyper en public">Prototype in public</h3>
          <p data-en="Rough builds reach players early. Feedback reshapes systems before art or polish ever locks them in place." data-fr="Les versions brutes atteignent les joueurs tôt. Les retours remodèlent les systèmes avant que l'art ou la finition ne les figent.">Rough builds reach players early. Feedback reshapes systems before art or polish ever locks them in place.</p>
        </div>
      </div>
      <div class="manifesto-item reveal">
        <span class="tag">03 / SHIP</span>
        <div>
          <h3 data-en="Small team, sharp edges" data-fr="Petite équipe, arêtes vives">Small team, sharp edges</h3>
          <p data-en="A studio kept deliberately small so every release still carries a single point of view." data-fr="Un studio volontairement restreint pour que chaque sortie porte encore un point de vue singulier.">A studio kept deliberately small so every release still carries a single point of view.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="games wrap" id="games">
  <div class="section-head reveal">
    <h2 data-en="Featured games" data-fr="Jeux en vedette">Featured games</h2>
    <a href="games.php" data-nav="games.php" class="num" data-en="See all games →" data-fr="Voir tous les jeux →">See all games →</a>
  </div>

  <div class="games-grid">
    <?php foreach ($featured_games as $game): ?>
    <div class="game-card reveal">
      <div class="game-art"><div class="<?= e($game['color']) ?>"></div></div>
      <div class="game-body">
        <span class="game-tag"
              data-en="<?= e($game['genre_en']) ?> · <?= e($game['year']) ?>"
              data-fr="<?= e($game['genre_fr']) ?> · <?= e($game['year']) ?>"><?= e($game['genre_en']) ?> · <?= e($game['year']) ?></span>
        <h3><?= e($game['title']) ?></h3>
        <p data-en="<?= e($game['description_en']) ?>" data-fr="<?= e($game['description_fr']) ?>"><?= e($game['description_en']) ?></p>
      </div>
    </div>
    <?php endforeach; if (empty($featured_games)): ?>
    <p class="empty-note" data-en="Games coming soon." data-fr="Jeux à venir bientôt.">Games coming soon.</p>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/hiring_cta.php'; ?>
<?php require __DIR__ . '/includes/site_foot.php'; ?>
