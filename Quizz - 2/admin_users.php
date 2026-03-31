<?php
require_once 'db.php';
if (!hasRole('admin')) { header("Location: dashboard.php"); exit; }

if (isset($_POST['update_role'])) {
    $target_id = $_POST['user_id'];
    $new_role   = $_POST['new_role'];
    if ($_SESSION['role'] === 'fondateur' || ($new_role !== 'admin' && $new_role !== 'fondateur')) {
        $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$new_role, $target_id]);
        $success = "Rôle mis à jour !";
    } else {
        $error = "Permissions insuffisantes pour ce rang.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/logo.png">
    <title>Panel Admin – Bernard Quizz</title>
    <style>
        body { background-color:#0f172a; background-image:radial-gradient(at 0% 0%,#1e1b4b 0px,transparent 50%),radial-gradient(at 100% 100%,#312e81 0px,transparent 50%); background-attachment:fixed; color:white; font-family:sans-serif; }
        .title-text { font-family:'Caveat',cursive; text-shadow:3px 3px 0px #312e81; letter-spacing:2px; }
        .game-card  { background-color:#1e1b4b; border:4px solid #312e81; border-radius:1.5rem; box-shadow:0 8px 0 0 #0b0f19; }
        .particle   { position:fixed; background:rgba(255,255,255,0.02); border-radius:50%; animation:drift infinite linear; pointer-events:none; z-index:0; }
        @keyframes drift { from{transform:translateY(100vh) rotate(0deg)} to{transform:translateY(-100vh) rotate(360deg)} }

        .role-badge { padding:.2rem .6rem; border-radius:.5rem; font-size:.7rem; font-weight:900; text-transform:uppercase; letter-spacing:1px; }
        .role-fondateur { background:#581c87; color:#e9d5ff; border:2px solid #7c3aed; }
        .role-admin     { background:#7f1d1d; color:#fecaca; border:2px solid #ef4444; }
        .role-createur  { background:#1e3a5f; color:#bfdbfe; border:2px solid #3b82f6; }
        .role-utilisateur{ background:#1e3a5f22; color:#818cf8; border:2px solid #3730a3; }

        .table-row { border-bottom:2px solid #312e81; transition:background .15s; }
        .table-row:hover { background:#2e2a72; }
        .table-row:last-child { border-bottom:none; }

        select { background:#0f172a; color:white; border:2px solid #4338ca; border-radius:.5rem; padding:.3rem .5rem; font-size:.85rem; outline:none; }
        select:focus { border-color:#facc15; }
        .ok-btn { background:#4f46e5; color:white; border:3px solid #3730a3; box-shadow:0 3px 0 0 #1e1b6e; border-radius:.5rem; font-weight:900; font-size:.8rem; padding:.3rem .9rem; cursor:pointer; transition:all .1s; }
        .ok-btn:hover  { background:#6366f1; }
        .ok-btn:active { transform:translateY(3px); box-shadow:0 0 0 0 #1e1b6e; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 relative">
<div class="particle" style="width:150px;height:150px;left:5%;animation-duration:25s;"></div>
<div class="particle" style="width:220px;height:220px;left:80%;animation-duration:35s;"></div>
<div class="relative z-10 max-w-4xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-12 drop-shadow-md" onerror="this.style.display='none'">
            <h1 class="title-text text-3xl text-yellow-400">Panel Admin</h1>
        </div>
        <a href="dashboard" class="bg-indigo-600 text-white px-5 py-2 rounded-xl font-black uppercase tracking-wider shadow-[0_4px_0_0_#1e1b4b] hover:bg-indigo-500 transition-all active:translate-y-1 text-sm border-2 border-indigo-400">← Menu</a>
    </div>

    <?php if (isset($success)): ?>
    <div class="bg-emerald-900/50 border-2 border-emerald-600 text-emerald-300 font-black px-5 py-3 rounded-xl mb-6 uppercase tracking-widest text-sm">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
    <div class="bg-red-900/50 border-2 border-red-600 text-red-300 font-black px-5 py-3 rounded-xl mb-6 text-sm">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="game-card overflow-hidden">
        <div class="px-6 py-4 border-b-2 border-[#312e81] flex items-center gap-3">
            <span class="text-2xl">👥</span>
            <h2 class="title-text text-xl text-white">Gestion des utilisateurs</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f172a]/60">
                    <th class="p-4 text-left text-xs font-black uppercase tracking-widest text-indigo-400">Pseudo</th>
                    <th class="p-4 text-left text-xs font-black uppercase tracking-widest text-indigo-400">Rôle actuel</th>
                    <th class="p-4 text-left text-xs font-black uppercase tracking-widest text-indigo-400">Modifier</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY FIELD(role,'fondateur','admin','createur','utilisateur') ASC, username ASC");
                while ($u = $stmt->fetch()):
                    $roleClass = 'role-' . $u['role'];
                ?>
                <tr class="table-row">
                    <td class="p-4 font-black text-white text-lg"><?= htmlspecialchars($u['username']) ?></td>
                    <td class="p-4">
                        <span class="role-badge <?= $roleClass ?>"><?= $u['role'] ?></span>
                    </td>
                    <td class="p-4">
                        <?php if ($u['role'] !== 'fondateur'): ?>
                        <form method="POST" class="flex gap-2 items-center">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <select name="new_role">
                                <option value="utilisateur" <?= $u['role']==='utilisateur'?'selected':'' ?>>Utilisateur</option>
                                <option value="createur"    <?= $u['role']==='createur'   ?'selected':'' ?>>Créateur</option>
                                <?php if ($_SESSION['role'] === 'fondateur'): ?>
                                <option value="admin"       <?= $u['role']==='admin'      ?'selected':'' ?>>Admin</option>
                                <?php endif; ?>
                            </select>
                            <button type="submit" name="update_role" class="ok-btn">OK</button>
                        </form>
                        <?php else: ?>
                        <span class="text-purple-400 text-xs font-black uppercase tracking-widest">⭐ Fondateur</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>