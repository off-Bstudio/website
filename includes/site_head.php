<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Breaker Studio') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/site.css">
</head>
<body>

<header>
  <nav class="wrap">
    <a href="index.php" data-nav="index.php" class="logo"><span class="logo-mark"></span>BREAKER STUDIO</a>
    <div class="nav-links" id="navLinks">
      <a href="studio.php" data-nav="studio.php" data-en="Studio" data-fr="Studio">Studio</a>
      <a href="games.php" data-nav="games.php" data-en="Games" data-fr="Jeux">Games</a>
      <a href="careers.php" data-nav="careers.php" data-en="Careers" data-fr="Carrières">Careers</a>
    </div>
    <div class="nav-right">
      <div class="lang-toggle" role="group" aria-label="Language toggle">
        <button id="btn-en" class="active" onclick="setLang('en')">EN</button>
        <button id="btn-fr" onclick="setLang('fr')">FR</button>
      </div>
      <button class="burger" id="burger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>
</header>
