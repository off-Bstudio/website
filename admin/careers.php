<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$db = get_db();

function validate_job_form(array $form): array {
    $errors = [];
    $title_en = trim($form['title_en'] ?? '');
    $title_fr = trim($form['title_fr'] ?? '');
    $department_en = trim($form['department_en'] ?? '');
    $department_fr = trim($form['department_fr'] ?? '');
    $location_key = $form['location_key'] ?? 'remote';
    $position = trim($form['position'] ?? '0');

    if ($title_en === '' || $title_fr === '') $errors[] = 'Job title is required in both languages.';
    if ($department_en === '' || $department_fr === '') $errors[] = 'Department is required in both languages.';
    if (!array_key_exists($location_key, JOB_LOCATION_LABELS)) $errors[] = 'Invalid location type.';
    if (!ctype_digit(ltrim($position, '-'))) $position = '0';

    $data = [
        'title_en' => $title_en, 'title_fr' => $title_fr,
        'department_en' => $department_en, 'department_fr' => $department_fr,
        'location_key' => $location_key, 'position' => (int)$position,
    ];
    return [$data, $errors];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_recruiting') {
        $current = is_recruiting_open();
        set_setting('recruiting_open', $current ? 'false' : 'true');
        flash('Recruiting is now ' . ($current ? 'closed.' : 'open.'));
        redirect('careers.php');
    }

    if ($action === 'create_job') {
        [$data, $errors] = validate_job_form($_POST);
        if ($errors) {
            foreach ($errors as $err) flash($err, 'error');
        } else {
            $stmt = $db->prepare(
                "INSERT INTO job_offers (title_en, title_fr, department_en, department_fr, location_key, position)
                 VALUES (:title_en,:title_fr,:department_en,:department_fr,:location_key,:position)"
            );
            $stmt->execute($data);
            flash("Job \"{$data['title_en']}\" added.");
        }
        redirect('careers.php');
    }

    if ($action === 'edit_job') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM job_offers WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) { flash('Job not found.', 'error'); redirect('careers.php'); }

        [$data, $errors] = validate_job_form($_POST);
        if ($errors) {
            foreach ($errors as $err) flash($err, 'error');
            redirect('careers.php');
        }
        $data['id'] = $id;
        $stmt = $db->prepare(
            "UPDATE job_offers SET title_en=:title_en, title_fr=:title_fr,
                    department_en=:department_en, department_fr=:department_fr,
                    location_key=:location_key, position=:position
             WHERE id=:id"
        );
        $stmt->execute($data);
        flash("Job \"{$data['title_en']}\" updated.");
        redirect('careers.php');
    }

    if ($action === 'delete_job') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM job_offers WHERE id = ?");
        $stmt->execute([$id]);
        $job = $stmt->fetch();
        if ($job) {
            $db->prepare("DELETE FROM job_offers WHERE id = ?")->execute([$id]);
            flash("Job \"{$job['title_en']}\" deleted.");
        }
        redirect('careers.php');
    }
}

$jobs = $db->query("SELECT * FROM job_offers ORDER BY position ASC, id ASC")->fetchAll();
$recruiting_open = is_recruiting_open();

$page_title = 'Careers';
$active_tab = 'careers';
require __DIR__ . '/../includes/admin_head.php';
?>

  <div class="recruiting-card">
    <div class="status-text">
      Recruiting is currently
      <?php if ($recruiting_open): ?><strong class="on">OPEN</strong><?php else: ?><strong class="off">CLOSED</strong><?php endif; ?>
      <p><?php if ($recruiting_open): ?>The careers page shows your open positions and accepts applications.<?php else: ?>The careers page shows a "not hiring" message and hides the job list.<?php endif; ?></p>
    </div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="toggle_recruiting">
      <button type="submit" class="btn <?= $recruiting_open ? 'btn-danger' : 'btn-primary' ?>">
        <?= $recruiting_open ? 'Turn recruiting off' : 'Turn recruiting on' ?>
      </button>
    </form>
  </div>

  <div class="panel">
    <h2>Add job offer</h2>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create_job">
      <div class="form-grid">
        <div class="field"><label>Job title (English)</label><input type="text" name="title_en" placeholder="e.g. Senior Gameplay Engineer" required></div>
        <div class="field"><label>Job title (French)</label><input type="text" name="title_fr" placeholder="e.g. Ingénieur·e gameplay senior" required></div>
        <div class="field"><label>Department (English)</label><input type="text" name="department_en" placeholder="e.g. Engineering" required></div>
        <div class="field"><label>Department (French)</label><input type="text" name="department_fr" placeholder="e.g. Ingénierie" required></div>
      </div>
      <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-top:16px;">
        <div class="field">
          <label>Location type</label>
          <select name="location_key">
            <option value="remote">Remote</option>
            <option value="hybrid">Hybrid</option>
            <option value="onsite">On-site</option>
          </select>
        </div>
        <div class="field"><label>Position (display order)</label><input type="number" name="position" value="0"></div>
      </div>
      <div style="margin-top:20px;"><button type="submit" class="btn btn-primary">Add job offer</button></div>
    </form>
  </div>

  <div class="section-title">
    <h2>Job offers</h2>
    <span class="meta"><?= count($jobs) ?> total</span>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Title</th><th>Department</th><th>Location</th><th>Order</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($jobs as $job): ?>
        <tr>
          <td class="username-cell"><?= e($job['title_en']) ?></td>
          <td class="email-cell"><?= e($job['department_en']) ?> / <?= e($job['department_fr']) ?></td>
          <td><span class="badge role-player"><?= e(JOB_LOCATION_LABELS[$job['location_key']]['en']) ?></span></td>
          <td class="email-cell"><?= (int)$job['position'] ?></td>
          <td>
            <div class="row-actions">
              <button type="button" class="btn btn-ghost btn-sm" onclick="toggleEdit('job-<?= $job['id'] ?>')">Edit</button>
              <form method="post" style="display:inline;" data-confirm="Delete &quot;<?= e($job['title_en']) ?>&quot;? This can't be undone.">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_job">
                <input type="hidden" name="id" value="<?= $job['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <tr class="edit-row" id="edit-job-<?= $job['id'] ?>">
          <td colspan="5">
            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="edit_job">
              <input type="hidden" name="id" value="<?= $job['id'] ?>">
              <div class="form-grid">
                <div class="field"><label>Job title (English)</label><input type="text" name="title_en" value="<?= e($job['title_en']) ?>" required></div>
                <div class="field"><label>Job title (French)</label><input type="text" name="title_fr" value="<?= e($job['title_fr']) ?>" required></div>
                <div class="field"><label>Department (English)</label><input type="text" name="department_en" value="<?= e($job['department_en']) ?>" required></div>
                <div class="field"><label>Department (French)</label><input type="text" name="department_fr" value="<?= e($job['department_fr']) ?>" required></div>
              </div>
              <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-top:16px;">
                <div class="field">
                  <label>Location type</label>
                  <select name="location_key">
                    <option value="remote" <?= $job['location_key'] === 'remote' ? 'selected' : '' ?>>Remote</option>
                    <option value="hybrid" <?= $job['location_key'] === 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                    <option value="onsite" <?= $job['location_key'] === 'onsite' ? 'selected' : '' ?>>On-site</option>
                  </select>
                </div>
                <div class="field"><label>Position</label><input type="number" name="position" value="<?= (int)$job['position'] ?>"></div>
              </div>
              <div style="margin-top:16px;"><button type="submit" class="btn btn-primary btn-sm">Save changes</button></div>
            </form>
          </td>
        </tr>
        <?php endforeach; if (empty($jobs)): ?>
        <tr><td colspan="5"><div class="empty-state">No job offers yet — add one above.</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<?php require __DIR__ . '/../includes/admin_foot.php'; ?>
