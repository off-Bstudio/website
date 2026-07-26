<?php
require_once __DIR__ . '/includes/functions.php';

$recruiting_open = is_recruiting_open();
$page_title = 'Studio — Breaker Studio';
require __DIR__ . '/includes/site_head.php';
?>

<section class="page-hero wrap">
  <div class="page-hero-mark"></div>
  <div class="eyebrow-row">
    <span class="dash"></span>
    <span class="eyebrow" data-en="The studio" data-fr="Le studio">The studio</span>
  </div>
  <h1 data-en="Small studio. Sharp point of view." data-fr="Petit studio. Point de vue tranchant.">Small studio. Sharp point of view.</h1>
  <p data-en="Breaker Studio was founded in 2019 on a simple bet: that the most interesting games come from questioning the rule everyone else takes for granted." data-fr="Breaker Studio a été fondé en 2019 sur un pari simple : les jeux les plus intéressants naissent en remettant en question la règle que tout le monde tient pour acquise.">Breaker Studio was founded in 2019 on a simple bet: that the most interesting games come from questioning the rule everyone else takes for granted.</p>
</section>

<section class="manifesto no-border-top wrap">
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

<section class="pillars wrap" id="pillars">
  <div class="section-head reveal">
    <h2 data-en="How we work" data-fr="Notre approche">How we work</h2>
    <span class="num" data-en="4 principles" data-fr="4 principes">4 principles</span>
  </div>

  <div class="pillars-grid">
    <div class="pillar reveal">
      <span class="pn">/CRAFT</span>
      <h3 data-en="Systems over set pieces" data-fr="Les systèmes avant les scènes scriptées">Systems over set pieces</h3>
      <p data-en="We'd rather ship one mechanic that keeps surprising you than ten moments that only work once." data-fr="Nous préférons livrer une mécanique qui continue de surprendre plutôt que dix moments qui ne fonctionnent qu'une fois.">We'd rather ship one mechanic that keeps surprising you than ten moments that only work once.</p>
    </div>
    <div class="pillar reveal">
      <span class="pn">/RISK</span>
      <h3 data-en="Bet on the weird idea" data-fr="Miser sur l'idée étrange">Bet on the weird idea</h3>
      <p data-en="If a pitch sounds safe, it doesn't make it to the prototype stage." data-fr="Si une proposition semble trop sûre, elle n'atteint jamais l'étape du prototype.">If a pitch sounds safe, it doesn't make it to the prototype stage.</p>
    </div>
    <div class="pillar reveal">
      <span class="pn">/PLAYERS</span>
      <h3 data-en="Player-first tuning" data-fr="Réglages centrés sur le joueur">Player-first tuning</h3>
      <p data-en="Difficulty and pacing are balanced against real playtests, not internal assumptions." data-fr="La difficulté et le rythme sont ajustés selon de vrais tests, jamais selon des suppositions internes.">Difficulty and pacing are balanced against real playtests, not internal assumptions.</p>
    </div>
    <div class="pillar reveal">
      <span class="pn">/ITERATE</span>
      <h3 data-en="Ship, then sharpen" data-fr="Livrer, puis affiner">Ship, then sharpen</h3>
      <p data-en="Post-launch support isn't an afterthought — it's where most of our best ideas actually land." data-fr="Le support post-lancement n'est pas une réflexion après coup — c'est là que nos meilleures idées prennent réellement forme.">Post-launch support isn't an afterthought — it's where most of our best ideas actually land.</p>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/hiring_cta.php'; ?>
<?php require __DIR__ . '/includes/site_foot.php'; ?>
