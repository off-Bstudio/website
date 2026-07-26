<?php
require_once __DIR__ . '/includes/functions.php';

$recruiting_open = is_recruiting_open();
$jobs = $recruiting_open
    ? get_db()->query("SELECT * FROM job_offers ORDER BY position ASC, id ASC")->fetchAll()
    : [];

$page_title = 'Careers — Breaker Studio';
require __DIR__ . '/includes/site_head.php';

$job_count = count($jobs);
$job_plural = $job_count === 1 ? '' : 's';
?>

<section class="page-hero wrap">
  <div class="page-hero-mark"></div>
  <div class="eyebrow-row">
    <span class="dash"></span>
    <span class="eyebrow" data-en="Careers" data-fr="Carrières">Careers</span>
  </div>
  <?php if ($recruiting_open): ?>
    <h1 data-en="Come break things with us" data-fr="Venez tout casser avec nous">Come break things with us</h1>
    <p data-en="We're a small, remote-friendly team. If a system's rough edges bother you more than they bother most people, you'll fit right in." data-fr="Nous sommes une petite équipe, ouverte au télétravail. Si les aspérités d'un système vous dérangent plus qu'elles ne dérangent la plupart des gens, vous serez à votre place ici.">We're a small, remote-friendly team. If a system's rough edges bother you more than they bother most people, you'll fit right in.</p>
  <?php else: ?>
    <h1 data-en="Not hiring right now" data-fr="Pas de recrutement pour l'instant">Not hiring right now</h1>
    <p data-en="We're not actively recruiting at the moment. Follow the studio or reach out below and we'll keep you in mind when a role opens up." data-fr="Nous ne recrutons pas activement en ce moment. Suivez le studio ou écrivez-nous ci-dessous et nous vous recontacterons dès qu'un poste s'ouvrira.">We're not actively recruiting at the moment. Follow the studio or reach out below and we'll keep you in mind when a role opens up.</p>
  <?php endif; ?>
</section>

<section class="wrap values-strip">
  <div class="value-card reveal">
    <h3 data-en="Remote-first" data-fr="Télétravail d'abord">Remote-first</h3>
    <p data-en="Work from anywhere. We overlap a few core hours and leave the rest to focus time." data-fr="Travaillez d'où vous voulez. Nous partageons quelques heures communes et laissons le reste au temps de concentration.">Work from anywhere. We overlap a few core hours and leave the rest to focus time.</p>
  </div>
  <div class="value-card reveal">
    <h3 data-en="Small rooms, big say" data-fr="Petites équipes, grande influence">Small rooms, big say</h3>
    <p data-en="Every hire changes the shape of a project you can actually see end to end." data-fr="Chaque recrue change la forme d'un projet que l'on peut suivre du début à la fin.">Every hire changes the shape of a project you can actually see end to end.</p>
  </div>
  <div class="value-card reveal">
    <h3 data-en="Ship, don't stall" data-fr="Livrer, sans s'enliser">Ship, don't stall</h3>
    <p data-en="We favor playable prototypes over long design documents." data-fr="Nous privilégions les prototypes jouables aux longs documents de conception.">We favor playable prototypes over long design documents.</p>
  </div>
</section>

<section class="wrap roles">
  <div class="section-head reveal">
    <h2 data-en="Open positions" data-fr="Postes ouverts">Open positions</h2>
    <?php if ($recruiting_open): ?>
    <span class="num" data-en="<?= $job_count ?> role<?= $job_plural ?>" data-fr="<?= $job_count ?> poste<?= $job_plural ?>"><?= $job_count ?> roles</span>
    <?php endif; ?>
  </div>

  <?php if ($recruiting_open): ?>
    <div class="role-list">
      <?php foreach ($jobs as $job): $loc = JOB_LOCATION_LABELS[$job['location_key']]; ?>
      <div class="role reveal">
        <h3 data-en="<?= e($job['title_en']) ?>" data-fr="<?= e($job['title_fr']) ?>"><?= e($job['title_en']) ?></h3>
        <span class="meta" data-en="<?= e($job['department_en']) ?>" data-fr="<?= e($job['department_fr']) ?>"><?= e($job['department_en']) ?></span>
        <span class="meta" data-en="<?= e($loc['en']) ?>" data-fr="<?= e($loc['fr']) ?>"><?= e($loc['en']) ?></span>
        <a href="mailto:hello@breakerstudio.com" class="apply" data-en="Apply" data-fr="Postuler">Apply</a>
      </div>
      <?php endforeach; if (empty($jobs)): ?>
      <div class="empty-state" data-en="No open roles right now, check back soon." data-fr="Aucun poste ouvert pour l'instant, revenez bientôt.">No open roles right now, check back soon.</div>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="empty-state" data-en="We're not hiring at the moment. Feel free to reach out anyway — we keep good people in mind for later." data-fr="Nous ne recrutons pas pour le moment. N'hésitez pas à nous écrire quand même — nous gardons les bons profils en tête pour plus tard.">We're not hiring at the moment. Feel free to reach out anyway — we keep good people in mind for later.</div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/site_foot.php'; ?>
