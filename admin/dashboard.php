<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$db = get_db();
const EMAIL_RE = '/^[^@\s]+@[^@\s]+\.[^@\s]+$/';

// ---------------------------------------------------------------- actions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create_account') {
        $username = trim($_POST['username'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'player';
        $errors = [];

        if (strlen($username) < 3 || strlen($username) > 32) $errors[] = 'Username must be between 3 and 32 characters.';
        if (!preg_match(EMAIL_RE, $email)) $errors[] = 'Enter a valid email address.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if (!in_array($role, ['player', 'moderator', 'admin'], true)) $errors[] = 'Invalid role.';

        if (!$errors) {
            $stmt = $db->prepare("SELECT id FROM accounts WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) $errors[] = 'An account with that username or email already exists.';
        }

        if ($errors) {
            foreach ($errors as $err) flash($err, 'error');
        } else {
            $stmt = $db->prepare(
                "INSERT INTO accounts (username, email, password_hash, role) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            flash("Account \"$username\" created.");
        }
        redirect('dashboard.php');
    }

    if ($action === 'edit_account') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM accounts WHERE id = ?");
        $stmt->execute([$id]);
        $account = $stmt->fetch();

        if (!$account) { flash('Account not found.', 'error'); redirect('dashboard.php'); }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $role = $_POST['role'] ?? $account['role'];
        $new_password = trim($_POST['password'] ?? '');

        if (!preg_match(EMAIL_RE, $email)) { flash('Enter a valid email address.', 'error'); redirect('dashboard.php'); }
        if (!in_array($role, ['player', 'moderator', 'admin'], true)) { flash('Invalid role.', 'error'); redirect('dashboard.php'); }
        if ($new_password !== '' && strlen($new_password) < 8) { flash('New password must be at least 8 characters.', 'error'); redirect('dashboard.php'); }

        $stmt = $db->prepare("SELECT id FROM accounts WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) { flash('That email is already used by another account.', 'error'); redirect('dashboard.php'); }

        if ($new_password !== '') {
            $stmt = $db->prepare("UPDATE accounts SET email = ?, role = ?, password_hash = ? WHERE id = ?");
            $stmt->execute([$email, $role, password_hash($new_password, PASSWORD_DEFAULT), $id]);
        } else {
            $stmt = $db->prepare("UPDATE accounts SET email = ?, role = ? WHERE id = ?");
            $stmt->execute([$email, $role, $id]);
        }
        flash("Account \"{$account['username']}\" updated.");
        redirect('dashboard.php');
    }

    if ($action === 'toggle_status') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM accounts WHERE id = ?");
        $stmt->execute([$id]);
        $account = $stmt->fetch();
        if ($account) {
            $new_status = $account['status'] === 'active' ? 'suspended' : 'active';
            $db->prepare("UPDATE accounts SET status = ? WHERE id = ?")->execute([$new_status, $id]);
            flash("\"{$account['username']}\" is now $new_status.");
        } else {
            flash('Account not found.', 'error');
        }
        redirect('dashboard.php');
    }

    if ($action === 'delete_account') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM accounts WHERE id = ?");
        $stmt->execute([$id]);
        $account = $stmt->fetch();
        if ($account) {
            $db->prepare("DELETE FROM accounts WHERE id = ?")->execute([$id]);
            flash("Account \"{$account['username']}\" deleted.");
        }
        redirect('dashboard.php');
    }

    if ($action === 'change_admin_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([current_admin_id()]);
        $admin = $stmt->fetch();

        if (!password_verify($current, $admin['password_hash'])) {
            flash('Current password is incorrect.', 'error');
        } elseif (strlen($new) < 8) {
            flash('New password must be at least 8 characters.', 'error');
        } elseif ($new !== $confirm) {
            flash("New passwords don't match.", 'error');
        } else {
            $db->prepare("UPDATE admins SET password_hash = ? WHERE id = ?")
               ->execute([password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
            flash('Admin password updated.');
        }
        redirect('dashboard.php');
    }
}

// ---------------------------------------------------------------- data for GET

$query = trim($_GET['q'] ?? '');
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM accounts WHERE 1=1";
$params = [];
if ($query !== '') {
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
}
if ($role_filter !== '') { $sql .= " AND role = ?"; $params[] = $role_filter; }
if ($status_filter !== '') { $sql .= " AND status = ?"; $params[] = $status_filter; }
$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$accounts = $stmt->fetchAll();

$stats = $db->query(
    "SELECT
       COUNT(*) AS total,
       SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
       SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) AS suspended,
       SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS admins,
       SUM(CASE WHEN role = 'moderator' THEN 1 ELSE 0 END) AS moderators
     FROM accounts"
)->fetch();

$page_title = 'Accounts';
$active_tab = 'accounts';
require __DIR__ . '/../includes/admin_head.php';
?>

  <div class="stats-grid">
    <div class="stat"><span class="label">Total accounts</span><span class="value"><?= (int)($stats['total'] ?? 0) ?></span></div>
    <div class="stat"><span class="label">Active</span><span class="value green"><?= (int)($stats['active'] ?? 0) ?></span></div>
    <div class="stat"><span class="label">Suspended</span><span class="value accent"><?= (int)($stats['suspended'] ?? 0) ?></span></div>
    <div class="stat"><span class="label">Admins / Mods</span><span class="value"><?= (int)($stats['admins'] ?? 0) + (int)($stats['moderators'] ?? 0) ?></span></div>
  </div>

  <div class="panel">
    <h2>Create account</h2>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create_account">
      <div class="form-grid">
        <div class="field">
          <label for="new-username">Username</label>
          <input type="text" id="new-username" name="username" placeholder="e.g. shadowrunner42" required>
        </div>
        <div class="field">
          <label for="new-email">Email</label>
          <input type="email" id="new-email" name="email" placeholder="name@example.com" required>
        </div>
        <div class="field">
          <label for="new-password">Password</label>
          <input type="password" id="new-password" name="password" placeholder="Min. 8 characters" required minlength="8">
        </div>
        <div class="field">
          <label for="new-role">Role</label>
          <select id="new-role" name="role">
            <option value="player">Player</option>
            <option value="moderator">Moderator</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <div style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">Create account</button>
      </div>
    </form>
  </div>

  <div class="section-title">
    <h2>Accounts</h2>
    <span class="meta"><?= count($accounts) ?> shown</span>
  </div>

  <form method="get" class="filters">
    <input type="text" name="q" placeholder="Search username or email" value="<?= e($query) ?>">
    <select name="role" onchange="this.form.submit()">
      <option value="">All roles</option>
      <option value="player" <?= $role_filter === 'player' ? 'selected' : '' ?>>Player</option>
      <option value="moderator" <?= $role_filter === 'moderator' ? 'selected' : '' ?>>Moderator</option>
      <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>
    <select name="status" onchange="this.form.submit()">
      <option value="">All statuses</option>
      <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
      <option value="suspended" <?= $status_filter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
    </select>
    <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
    <?php if ($query || $role_filter || $status_filter): ?>
      <a href="dashboard.php" class="btn btn-ghost btn-sm">Clear</a>
    <?php endif; ?>
  </form>

  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($accounts as $account): ?>
        <tr>
          <td class="username-cell"><?= e($account['username']) ?></td>
          <td class="email-cell"><?= e($account['email']) ?></td>
          <td><span class="badge role-<?= e($account['role']) ?>"><?= e($account['role']) ?></span></td>
          <td><span class="badge status-<?= e($account['status']) ?>"><?= e($account['status']) ?></span></td>
          <td class="email-cell"><?= e($account['created_at']) ?></td>
          <td>
            <div class="row-actions">
              <button type="button" class="btn btn-ghost btn-sm" onclick="toggleEdit('acc-<?= $account['id'] ?>')">Edit</button>
              <form method="post" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="id" value="<?= $account['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm"><?= $account['status'] === 'active' ? 'Suspend' : 'Reactivate' ?></button>
              </form>
              <form method="post" style="display:inline;" data-confirm="Delete account &quot;<?= e($account['username']) ?>&quot;? This can't be undone.">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_account">
                <input type="hidden" name="id" value="<?= $account['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <tr class="edit-row" id="edit-acc-<?= $account['id'] ?>">
          <td colspan="6">
            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="edit_account">
              <input type="hidden" name="id" value="<?= $account['id'] ?>">
              <div class="edit-form-grid">
                <div class="field">
                  <label>Email</label>
                  <input type="email" name="email" value="<?= e($account['email']) ?>" required>
                </div>
                <div class="field">
                  <label>Role</label>
                  <select name="role">
                    <option value="player" <?= $account['role'] === 'player' ? 'selected' : '' ?>>Player</option>
                    <option value="moderator" <?= $account['role'] === 'moderator' ? 'selected' : '' ?>>Moderator</option>
                    <option value="admin" <?= $account['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                  </select>
                </div>
                <div class="field">
                  <label>New password (optional)</label>
                  <input type="password" name="password" placeholder="Leave blank to keep current" minlength="8">
                </div>
                <div class="field">
                  <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                </div>
              </div>
            </form>
          </td>
        </tr>
        <?php endforeach; if (empty($accounts)): ?>
        <tr><td colspan="6"><div class="empty-state">No accounts match your filters yet.</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="account-settings">
    <div class="section-title"><h2>Your admin password</h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="change_admin_password">
      <div class="form-row">
        <div class="field"><label>Current password</label><input type="password" name="current_password" required></div>
        <div class="field"><label>New password</label><input type="password" name="new_password" required minlength="8"></div>
        <div class="field"><label>Confirm new password</label><input type="password" name="confirm_password" required minlength="8"></div>
        <div class="field"><button type="submit" class="btn btn-ghost">Update</button></div>
      </div>
    </form>
  </div>

<?php require __DIR__ . '/../includes/admin_foot.php'; ?>
