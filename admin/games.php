<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$db = get_db();

function validate_game_form(array $form): array {
    $errors = [];
    $title = trim($form['title'] ?? '');
    $genre_en = trim($form['genre_en'] ?? '');
    $genre_fr = trim($form['genre_fr'] ?? '');
    $year = trim($form['year'] ?? '');
    $status_key = $form['status_key'] ?? 'available';
    $description_en = trim($form['description_en'] ?? '');
    $description_fr = trim($form['description_fr'] ?? '');
    $color = $form['color'] ?? 'g1';
    $position = trim($form['position'] ?? '0');

    if ($title === '') $errors[] = 'Title is required.';
    if ($genre_en === '' || $genre_fr === '') $errors[] = 'Genre is required in both languages.';
    if (!ctype_digit($year)) $errors[] = 'Year must be a number.';
    if (!array_key_exists($status_key, GAME_STATUS_LABELS)) $errors[] = 'Invalid status.';
    if ($description_en === '' || $description_fr === '') $errors[] = 'Description is required in both languages.';
    if (!in_array($color, GAME_COLORS, true)) $color = 'g1';
    if (!ctype_digit(ltrim($position, '-'))) $position = '0';

    $data = [
        'title' => $title, 'genre_en' => $genre_en, 'genre_fr' => $genre_fr,
        'year' => ctype_digit($year) ? (int)$year : 0,
        'status_key' => $status_key,
        'description_en' => $description_en, 'description_fr' => $description_fr,
        'color' => $color, 'position' => (int)$position,
    ];
    return [$data, $errors];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create_game') {
        [$data, $errors] = validate_game_form($_POST);
        if ($errors) {
            foreach ($errors as $err) flash($err, 'error');
        } else {
            $stmt = $db->prepare(
                "INSERT INTO games (title, genre_en, genre_fr, year, status_key, description_en, description_fr, color, position)
                 VALUES (:title,:genre_en,:genre_fr,:year,:status_key,:description_en,:description_fr,:color,:position)"
            );
            $stmt->execute($data);
            flash("Game \"{$data['title']}\" added.");
        }
        redirect('games.php');
    }

    if ($action === 'edit_game') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) { flash('Game not found.', 'error'); redirect('games.php'); }

        [$data, $errors] = validate_game_form($_POST);
        if ($errors) {
            foreach ($errors as $err) flash($err, 'error');
            redirect('games.php');
        }
        $data['id'] = $id;
        $stmt = $db->prepare(
            "UPDATE games SET title=:title, genre_en=:genre_en, genre_fr=:genre_fr, year=:year,
                    status_key=:status_key, description_en=:description_en, description_fr=:description_fr,
                    color=:color, position=:position
             WHERE id=:id"
        );
        $stmt->execute($data);
        flash("Game \"{$data['title']}\" updated.");
        redirect('games.php');
    }

    if ($action === 'delete_game') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        $game = $stmt->fetch();
        if ($game) {
            $db->prepare("DELETE FROM games WHERE id = ?")->execute([$id]);
            flash("Game \"{$game['title']}\" deleted.");
        }
        redirect('games.php');
    }
}

$games = $db->query("SELECT * FROM games ORDER BY position ASC, id ASC")->fetchAll();

$page_title = 'Games';
$active_tab = 'games';
require __DIR__ . '/../includes/admin_head.php';
?>

  <div class="panel">
    <h2>Add game</h2>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create_game">
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="field"><label>Title</label><input type="text" name="title" placeholder="e.g. VOID RUNNERS" required></div>
        <div class="field"><label>Genre (English)</label><input type="text" name="genre_en" placeholder="e.g. Tactics" required></div>
        <div class="field"><label>Genre (French)</label><input type="text" name="genre_fr" placeholder="e.g. Tactique" required></div>
        <div class="field"><label>Year</label><input type="number" name="year" placeholder="2026" required></div>
        <div class="field">
          <label>Status</label>
          <select name="status_key">
            <option value="available">Available now</option>
            <option value="development">In development</option>
          </select>
        </div>
        <div class="field"><label>Position (display order)</label><input type="number" name="position" value="0"></div>
      </div>
      <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-top:16px;">
        <div class="field"><label>Description (English)</label><textarea name="description_en" rows="3" placeholder="A short pitch for the game" required></textarea></div>
        <div class="field"><label>Description (French)</label><textarea name="description_fr" rows="3" placeholder="Une courte description du jeu" required></textarea></div>
      </div>
      <div class="field" style="margin-top:16px;">
        <label>Card color</label>
        <div class="color-picker">
          <?php foreach (GAME_COLORS as $c): ?>
          <label class="color-swatch color-<?= $c ?> <?= $c === 'g1' ? 'selected' : '' ?>">
            <input type="radio" name="color" value="<?= $c ?>" <?= $c === 'g1' ? 'checked' : '' ?> onchange="selectSwatch(this)">
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="margin-top:20px;"><button type="submit" class="btn btn-primary">Add game</button></div>
    </form>
  </div>

  <div class="section-title">
    <h2>Games</h2>
    <span class="meta"><?= count($games) ?> total</span>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr><th></th><th>Title</th><th>Genre</th><th>Year</th><th>Status</th><th>Order</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($games as $game): ?>
        <tr>
          <td><span class="color-swatch color-<?= e($game['color']) ?>" style="width:22px;height:22px;pointer-events:none;"></span></td>
          <td class="username-cell"><?= e($game['title']) ?></td>
          <td class="email-cell"><?= e($game['genre_en']) ?> / <?= e($game['genre_fr']) ?></td>
          <td class="email-cell"><?= (int)$game['year'] ?></td>
          <td><span class="badge <?= $game['status_key'] === 'available' ? 'status-active' : 'role-moderator' ?>"><?= e(GAME_STATUS_LABELS[$game['status_key']]['en']) ?></span></td>
          <td class="email-cell"><?= (int)$game['position'] ?></td>
          <td>
            <div class="row-actions">
              <button type="button" class="btn btn-ghost btn-sm" onclick="toggleEdit('game-<?= $game['id'] ?>')">Edit</button>
              <form method="post" style="display:inline;" data-confirm="Delete &quot;<?= e($game['title']) ?>&quot;? This can't be undone.">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_game">
                <input type="hidden" name="id" value="<?= $game['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <tr class="edit-row" id="edit-game-<?= $game['id'] ?>">
          <td colspan="7">
            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="edit_game">
              <input type="hidden" name="id" value="<?= $game['id'] ?>">
              <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
                <div class="field"><label>Title</label><input type="text" name="title" value="<?= e($game['title']) ?>" required></div>
                <div class="field"><label>Genre (English)</label><input type="text" name="genre_en" value="<?= e($game['genre_en']) ?>" required></div>
                <div class="field"><label>Genre (French)</label><input type="text" name="genre_fr" value="<?= e($game['genre_fr']) ?>" required></div>
                <div class="field"><label>Year</label><input type="number" name="year" value="<?= (int)$game['year'] ?>" required></div>
                <div class="field">
                  <label>Status</label>
                  <select name="status_key">
                    <option value="available" <?= $game['status_key'] === 'available' ? 'selected' : '' ?>>Available now</option>
                    <option value="development" <?= $game['status_key'] === 'development' ? 'selected' : '' ?>>In development</option>
                  </select>
                </div>
                <div class="field"><label>Position</label><input type="number" name="position" value="<?= (int)$game['position'] ?>"></div>
              </div>
              <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-top:16px;">
                <div class="field"><label>Description (English)</label><textarea name="description_en" rows="3" required><?= e($game['description_en']) ?></textarea></div>
                <div class="field"><label>Description (French)</label><textarea name="description_fr" rows="3" required><?= e($game['description_fr']) ?></textarea></div>
              </div>
              <div class="field" style="margin-top:16px;">
                <label>Card color</label>
                <div class="color-picker">
                  <?php foreach (GAME_COLORS as $c): ?>
                  <label class="color-swatch color-<?= $c ?> <?= $c === $game['color'] ? 'selected' : '' ?>">
                    <input type="radio" name="color" value="<?= $c ?>" <?= $c === $game['color'] ? 'checked' : '' ?> onchange="selectSwatch(this)">
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>
              <div style="margin-top:16px;"><button type="submit" class="btn btn-primary btn-sm">Save changes</button></div>
            </form>
          </td>
        </tr>
        <?php endforeach; if (empty($games)): ?>
        <tr><td colspan="7"><div class="empty-state">No games yet — add your first one above.</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<script>
  function selectSwatch(input){
    var group = input.closest('.color-picker');
    group.querySelectorAll('.color-swatch').forEach(function(el){ el.classList.remove('selected'); });
    input.closest('.color-swatch').classList.add('selected');
  }
</script>
<?php require __DIR__ . '/../includes/admin_foot.php'; ?>
