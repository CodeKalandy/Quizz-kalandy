<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login"); exit; }

$view_user_id = $_GET['id'] ?? $_SESSION['user_id'];
$is_own_profile = ($view_user_id == $_SESSION['user_id']);

// ── Sauvegarde ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_fav']) && $is_own_profile) {
    $newCols = [
        'fav_skin INT DEFAULT 1',
        'fav_skin_color INT DEFAULT 0',
        'fav_hair_style INT DEFAULT 1',
        'fav_hair_color INT DEFAULT 0',
        'fav_beard INT DEFAULT 0',
        'fav_beard_color INT DEFAULT 0',
        'fav_top INT DEFAULT 1',
        'fav_top_color INT DEFAULT 0',
        'fav_aura INT DEFAULT 0',
        'fav_effect INT DEFAULT 0',
    ];
    foreach ($newCols as $colDef) {
        try { $pdo->exec("ALTER TABLE users ADD COLUMN $colDef"); } catch (Exception $e) {}
    }
    $pdo->prepare("UPDATE users SET
        fav_hair        = ?,
        fav_outfit      = ?,
        fav_skin        = ?,
        fav_skin_color  = ?,
        fav_hair_style  = ?,
        fav_hair_color  = ?,
        fav_beard       = ?,
        fav_beard_color = ?,
        fav_top         = ?,
        fav_top_color   = ?,
        fav_aura        = ?,
        fav_effect      = ?
        WHERE id = ?
    ")->execute([
        (int)($_POST['fav_hair']        ?? 1),
        (int)($_POST['fav_top']         ?? 1),
        (int)($_POST['fav_skin']        ?? 1),
        (int)($_POST['fav_skin_color']  ?? 0),
        (int)($_POST['fav_hair_style']  ?? 1),
        (int)($_POST['fav_hair_color']  ?? 0),
        (int)($_POST['fav_beard']       ?? 0),
        (int)($_POST['fav_beard_color'] ?? 0),
        (int)($_POST['fav_top']         ?? 1),
        (int)($_POST['fav_top_color']   ?? 0),
        (int)($_POST['fav_aura']        ?? 0),
        (int)($_POST['fav_effect']      ?? 0),
        $_SESSION['user_id']
    ]);
    header("Location: profil?id=" . $_SESSION['user_id']);
    exit;
}

// ── Chargement profil ───────────────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$view_user_id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$u) { die("<p style='color:white;text-align:center;margin-top:50px;'>Joueur introuvable. <a href='dashboard'>Retour</a></p>"); }

$role      = $u['role'] ?? 'utilisateur';
// Tous les utilisateurs INSCRITS ont accès complet (tenues, auras, effets).
// Les restrictions (moitié des options, pas d'aura) ne s'appliquent qu'aux anonymes,
// qui n'ont pas accès à cette page de profil de toute façon.
$is_member = true;

$correct   = $u['total_correct'] ?? 0;
$wrong     = $u['total_wrong']   ?? 0;
$games     = $u['total_games']   ?? 0;
$p1        = $u['podium_1']      ?? 0;
$p2        = $u['podium_2']      ?? 0;
$p3        = $u['podium_3']      ?? 0;
$precision = ($correct + $wrong > 0) ? round(($correct / ($correct + $wrong)) * 100) : 0;

$ranks = [
    ["Merguez de Bronze",    0,   "text-orange-400"],
    ["Apprenti Bernard",     50,  "text-blue-400"],
    ["Expert des Quiz",      150, "text-purple-400"],
    ["Maître des Questions", 350, "text-red-400"],
    ["Légende de Bernard",   700, "text-yellow-400"],
];
$currentRank = $ranks[0]; $nextRank = $ranks[1] ?? null;
foreach ($ranks as $idx => $r) {
    if ($correct >= $r[1]) { $currentRank = $r; $nextRank = $ranks[$idx+1] ?? null; }
}

$aura6_unlocked = ($games >= 10);
$aura7_unlocked = ($p1   >= 3);

// Valeurs sauvegardées
$fav_hair       = (int)($u['fav_hair']        ?? 1);
$fav_hairStyle  = (int)($u['fav_hair_style']  ?? 1);
$fav_hairColor  = (int)($u['fav_hair_color']  ?? 0);
$fav_skin       = (int)($u['fav_skin']        ?? 1);
$fav_skinColor  = (int)($u['fav_skin_color']  ?? 0);
$fav_beard      = (int)($u['fav_beard']       ?? 0);
$fav_beardColor = (int)($u['fav_beard_color'] ?? 0);
$fav_top        = (int)($u['fav_top']         ?? $u['fav_outfit'] ?? 1);
$fav_topColor   = (int)($u['fav_top_color']   ?? 0);
$fav_aura       = (int)($u['fav_aura']        ?? 0);
$fav_effect     = (int)($u['fav_effect']      ?? 0);

// Limites selon rôle
$maxSkinColors  = $is_member ? 15 : 8;
$maxHairColors  = $is_member ? 4  : 2;
$maxBeard       = $is_member ? 11 : 5;
$maxTop         = $is_member ? 20 : 10;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Profil – <?= htmlspecialchars($u['username']) ?></title>
    <style>
        body { background-color:#0f172a; background-image:radial-gradient(at 0% 0%,#1e1b4b 0px,transparent 50%),radial-gradient(at 100% 100%,#312e81 0px,transparent 50%); background-attachment:fixed; color:white; font-family:sans-serif; }
        .title-text { font-family:'Caveat',cursive; text-shadow:3px 3px 0px #312e81; letter-spacing:2px; }
        .game-card  { background-color:#1e1b4b; border:4px solid #312e81; border-radius:1.5rem; box-shadow:0 8px 0 0 #0b0f19; }
        .particle   { position:fixed; background:rgba(255,255,255,0.02); border-radius:50%; animation:drift infinite linear; pointer-events:none; z-index:0; }
        @keyframes drift { from{transform:translateY(100vh) rotate(0deg)} to{transform:translateY(-100vh) rotate(360deg)} }

        /* ── Vitrine : structure identique à lobby.php ── */
        #preview-container {
            width:200px; height:200px; position:relative; margin:0 auto;
            background-color:#312e81; border-radius:2rem;
            border:6px solid #facc15;
            box-shadow:0 8px 0 0 #ca8a04, inset 0 8px 20px rgba(0,0,0,0.5);
            overflow:visible; transition:all 0.3s ease;
        }
        .layer { position:absolute; top:0; left:0; width:100%; height:100%; display:block; object-fit:contain; }
        .aura-fade { animation:pulseAura 2.5s ease-in-out infinite alternate; }
        @keyframes pulseAura { 0%{opacity:0.4} 100%{opacity:1} }
        .effect-rainbow { animation:rainbowBox 3s linear infinite !important; }
        @keyframes rainbowBox {
            0%  {border-color:#ef4444;box-shadow:0 8px 0 0 #991b1b,0 0 25px #ef4444}
            25% {border-color:#f59e0b;box-shadow:0 8px 0 0 #92400e,0 0 25px #f59e0b}
            50% {border-color:#10b981;box-shadow:0 8px 0 0 #065f46,0 0 25px #10b981}
            75% {border-color:#3b82f6;box-shadow:0 8px 0 0 #1e40af,0 0 25px #3b82f6}
            100%{border-color:#ef4444;box-shadow:0 8px 0 0 #991b1b,0 0 25px #ef4444}
        }
        .effect-levitate { animation:levitate 2.5s ease-in-out infinite !important; }
        @keyframes levitate { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-22px) scale(1.05)} }

        /* Contrôles */
        .control-row   { display:flex;align-items:center;justify-content:space-between;background:#2e2a72;padding:.55rem .75rem;border-radius:1rem;border:3px solid #3b3687;margin-bottom:.5rem; }
        .control-label { font-size:.7rem;font-weight:900;color:#a5b4fc;text-transform:uppercase; }
        .arrow-btn     { display:flex;align-items:center;justify-content:center;background:#3b82f6;color:white;padding:.35rem .5rem;border-radius:.45rem;font-weight:900;transition:transform .1s,box-shadow .1s;box-shadow:0 4px 0 0 #1d4ed8;cursor:pointer;flex-shrink:0; }
        .arrow-btn:active { transform:translateY(4px);box-shadow:0 0 0 0 #1d4ed8; }
        .arrow-btn.pink { background:#ec4899;box-shadow:0 4px 0 0 #be185d; }
        .arrow-btn.pink:active { box-shadow:0 0 0 0 #be185d; }
        .val-display   { font-size:.85rem;font-weight:900;width:34px;text-align:center;color:white;flex-shrink:0; }
        .locked-tag    { font-size:.6rem;font-weight:900;color:#6366f1;background:#1e1b4b;padding:.15rem .4rem;border-radius:.4rem;border:2px solid #3730a3; }

        /* Boutons */
        .save-btn  { background:#10b981;color:white;border:4px solid #047857;box-shadow:0 6px 0 0 #064e3b;border-radius:1rem;font-weight:900;font-size:1rem;text-transform:uppercase;letter-spacing:1px;padding:.9rem;width:100%;transition:all .1s;cursor:pointer;text-shadow:2px 2px 0 #065f46; }
        .save-btn:hover  { background:#34d399; }
        .save-btn:active { transform:translateY(6px);box-shadow:0 0 0 0 #064e3b; }
        .join-btn  { background:#10b981;color:white;border:4px solid #047857;box-shadow:0 6px 0 0 #064e3b;border-radius:1rem;font-weight:900;font-size:1.1rem;text-transform:uppercase;letter-spacing:2px;padding:.8rem;width:100%;transition:all .1s;cursor:pointer;text-shadow:2px 2px 0 #065f46; }
        .join-btn:hover  { background:#34d399; }
        .join-btn:active { transform:translateY(6px);box-shadow:0 0 0 0 #064e3b; }

        /* PIN */
        .pin-input { background:#0f172a;border:4px solid #312e81;border-radius:1rem;color:white;font-weight:900;font-size:2rem;text-align:center;letter-spacing:8px;padding:.6rem 1rem;width:100%;outline:none;transition:border-color .2s; }
        .pin-input:focus { border-color:#facc15; }

        /* Stats */
        .stat-card { background:#2e2a72;border:3px solid #4338ca;border-radius:1rem;padding:1rem;text-align:center; }
        .rank-bar  { background:#1e1b4b;border-radius:999px;height:10px;overflow:hidden; }
        .rank-fill { height:100%;border-radius:999px;background:linear-gradient(to right,#6366f1,#a78bfa);transition:width 1s; }
        .neon-vip  { text-shadow:0 0 5px #fff,0 0 10px #facc15,0 0 20px #facc15;color:#facc15; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 relative">
<div class="particle" style="width:120px;height:120px;left:5%;animation-duration:25s;"></div>
<div class="particle" style="width:200px;height:200px;left:80%;animation-duration:35s;"></div>
<div class="particle" style="width:70px;height:70px;left:45%;animation-duration:18s;"></div>
<div class="relative z-10 max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-12 drop-shadow-md" onerror="this.style.display='none'">
            <h1 class="title-text text-3xl text-yellow-400 hidden md:block">
                <?= $is_own_profile ? "Ma Vitrine" : "Profil de " . htmlspecialchars($u['username']) ?>
            </h1>
        </div>
        <a href="dashboard" class="bg-indigo-600 text-white px-5 py-2 rounded-xl font-black uppercase tracking-wider shadow-[0_4px_0_0_#1e1b4b] hover:bg-indigo-500 transition-all active:translate-y-1 active:shadow-none text-sm border-2 border-indigo-400">← Menu</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ═══ COLONNE GAUCHE ═══ -->
        <div class="flex flex-col gap-6">

            <!-- Vitrine -->
            <div class="game-card p-6 flex flex-col items-center">
                <div class="relative w-full flex flex-col items-center mb-4">
                    <div class="bg-yellow-400 text-indigo-900 px-5 py-1 rounded-xl font-black text-base shadow-[0_3px_0_0_#ca8a04] -rotate-1 border-2 border-yellow-500 mb-4 z-10" style="font-family:'Caveat',cursive;">
                        <?= $is_own_profile ? "Mon Bernard Favori" : "Bernard de " . htmlspecialchars($u['username']) ?>
                    </div>

                    <!-- Preview box — structure identique à lobby.php -->
                    <div id="preview-container">
                        <img id="layer-aura"       src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer aura-fade" style="z-index:0;">
                        <img id="layer-hair-back"  src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:5;">
                        <img id="layer-skin"       src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:10;">
                        <img id="layer-top"        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:20;">
                        <img id="layer-beard"      src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:40;">
                        <img id="layer-mouth"      src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:50;">
                        <img id="layer-eyes"       src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:51;">
                        <img id="layer-eyebrow"    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:52;">
                        <img id="layer-nose"       src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:53;">
                        <img id="layer-hair-front" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index:54;">
                    </div>
                </div>

                <?php if ($is_own_profile): ?>
                <form method="POST" id="fav-form" class="w-full mt-4">
                    <input type="hidden" name="fav_hair"        id="inp_hair"        value="<?= $fav_hair ?>">
                    <input type="hidden" name="fav_skin"        id="inp_skin"        value="<?= $fav_skin ?>">
                    <input type="hidden" name="fav_skin_color"  id="inp_skin_color"  value="<?= $fav_skinColor ?>">
                    <input type="hidden" name="fav_hair_style"  id="inp_hair_style"  value="<?= $fav_hairStyle ?>">
                    <input type="hidden" name="fav_hair_color"  id="inp_hair_color"  value="<?= $fav_hairColor ?>">
                    <input type="hidden" name="fav_beard"       id="inp_beard"       value="<?= $fav_beard ?>">
                    <input type="hidden" name="fav_beard_color" id="inp_beard_color" value="<?= $fav_beardColor ?>">
                    <input type="hidden" name="fav_top"         id="inp_top"         value="<?= $fav_top ?>">
                    <input type="hidden" name="fav_top_color"   id="inp_top_color"   value="<?= $fav_topColor ?>">
                    <input type="hidden" name="fav_aura"        id="inp_aura"        value="<?= $fav_aura ?>">
                    <input type="hidden" name="fav_effect"      id="inp_effect"      value="<?= $fav_effect ?>">

                    <!-- PEAU -->
                    <div class="control-row">
                        <span class="control-label">Peau</span>
                        <div class="flex gap-1 items-center">
                            <button type="button" onclick="ch('skin','color',-1)" class="arrow-btn pink"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                            <span id="lbl-skin-color" class="val-display">1</span>
                            <button type="button" onclick="ch('skin','color',1)"  class="arrow-btn pink"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                        <?php if (!$is_member): ?><span class="locked-tag">🔒 <?= $maxSkinColors ?>/15</span><?php endif; ?>
                    </div>

                    <!-- CHEVEUX -->
                    <div class="bg-[#2e2a72] p-3 rounded-2xl border-2 border-[#3b3687] mb-1">
                        <span class="control-label block mb-2 text-indigo-300">Cheveux</span>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] text-indigo-400 font-bold uppercase">Style</span>
                            <div class="flex gap-1 items-center">
                                <button type="button" onclick="ch('hair','style',-1)" class="arrow-btn !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                                <span id="lbl-hair-style" class="val-display !w-20 text-[10px]">COURT</span>
                                <button type="button" onclick="ch('hair','style',1)"  class="arrow-btn !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-indigo-400 font-bold uppercase">Coupe</span>
                            <div class="flex gap-1 items-center">
                                <button type="button" onclick="ch('hair','type',-1)" class="arrow-btn !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                                <span id="lbl-hair-type" class="val-display text-xs !w-6">1</span>
                                <button type="button" onclick="ch('hair','type',1)"  class="arrow-btn !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                            </div>
                            <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                                <button type="button" onclick="ch('hair','color',-1)" class="arrow-btn pink !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                                <button type="button" onclick="ch('hair','color',1)"  class="arrow-btn pink !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                            </div>
                        </div>
                    </div>

                    <!-- BARBE -->
                    <div class="control-row">
                        <span class="control-label text-[10px]">Barbe</span>
                        <div class="flex gap-1 items-center">
                            <button type="button" onclick="ch('beard','type',-1)" class="arrow-btn !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                            <span id="lbl-beard-type" class="val-display !w-4 text-xs">Ø</span>
                            <button type="button" onclick="ch('beard','type',1)"  class="arrow-btn !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                            <button type="button" onclick="ch('beard','color',-1)" class="arrow-btn pink !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                            <button type="button" onclick="ch('beard','color',1)"  class="arrow-btn pink !py-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                        <?php if (!$is_member): ?><span class="locked-tag">🔒 <?= $maxBeard ?>/11</span><?php endif; ?>
                    </div>

                    <!-- T-SHIRT -->
                    <div class="control-row">
                        <span class="control-label">T-Shirt</span>
                        <div class="flex gap-1 items-center">
                            <button type="button" onclick="ch('top','type',-1)" class="arrow-btn"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                            <span id="lbl-top-type" class="val-display">1</span>
                            <button type="button" onclick="ch('top','type',1)"  class="arrow-btn"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                            <button type="button" onclick="ch('top','color',-1)" class="arrow-btn pink"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                            <button type="button" onclick="ch('top','color',1)"  class="arrow-btn pink"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                        <?php if (!$is_member): ?><span class="locked-tag">🔒 <?= $maxTop ?>/20</span><?php endif; ?>
                    </div>

                    <!-- AURA & EFFET (membres uniquement) -->
                    <?php if ($is_member): ?>
                    <div class="control-row !bg-yellow-900/30 !border-yellow-700/50">
                        <span class="control-label text-yellow-400">Aura</span>
                        <div class="flex gap-1 items-center">
                            <button type="button" onclick="ch('aura','type',-1)" class="arrow-btn !bg-yellow-600 !shadow-[0_4px_0_0_#a16207]"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                            <span id="lbl-aura-type" class="val-display text-yellow-200">Ø</span>
                            <button type="button" onclick="ch('aura','type',1)"  class="arrow-btn !bg-yellow-600 !shadow-[0_4px_0_0_#a16207]"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                    </div>
                    <div class="bg-purple-900/30 p-2 rounded-xl border-2 border-purple-700/50 mb-4">
                        <div class="flex items-center justify-between">
                            <span class="control-label text-purple-400">Effet</span>
                            <span id="lbl-effect-name" class="text-[10px] font-black text-purple-300 uppercase px-1">AUCUN</span>
                            <div class="flex gap-1 items-center">
                                <button type="button" onclick="ch('effect','type',-1)" class="arrow-btn !bg-purple-600 !shadow-[0_4px_0_0_#7e22ce] !py-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"/></svg></button>
                                <span id="lbl-effect-type" class="val-display text-purple-100 !w-4 text-xs">Ø</span>
                                <button type="button" onclick="ch('effect','type',1)"  class="arrow-btn !bg-purple-600 !shadow-[0_4px_0_0_#7e22ce] !py-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/></svg></button>
                            </div>
                        </div>
                        <p id="lbl-effect-desc" class="text-[9px] text-center text-purple-400 font-semibold italic mt-1 h-3"></p>
                    </div>
                    <?php else: ?>
                    <div class="bg-indigo-900/40 border-2 border-indigo-700/40 rounded-xl p-3 mb-4 text-center">
                        <p class="text-xs font-black text-indigo-500 uppercase tracking-widest">🔒 Auras & Effets membres</p>
                        <p class="text-[10px] text-indigo-600 mt-1">Réservés aux créateurs, admins et fondateurs</p>
                    </div>
                    <?php endif; ?>

                    <button type="submit" name="save_fav" class="save-btn">💾 Sauvegarder mon Bernard</button>
                </form>
                <?php else: ?>
                <p class="text-sm font-black text-indigo-300 mt-4 uppercase tracking-widest text-center">Le Bernard favori de <?= htmlspecialchars($u['username']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Widget Rejoindre -->
            <div class="game-card p-6">
                <div class="text-center mb-4">
                    <span class="title-text text-2xl text-green-400">Rejoindre une partie</span>
                    <p class="text-indigo-300 text-xs font-bold uppercase tracking-widest mt-1">Entre le code PIN</p>
                </div>
                <input type="text" id="pin-input" class="pin-input mb-4" placeholder="• • • • • •" maxlength="6" inputmode="numeric">
                <button onclick="joinGame()" class="join-btn">Rejoindre !</button>
            </div>
        </div>

        <!-- ═══ STATS ═══ -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <div class="game-card p-6 flex items-center gap-6">
                <div class="text-6xl">🏆</div>
                <div class="flex-grow">
                    <h2 class="title-text text-3xl <?= $role === 'fondateur' ? 'neon-vip' : 'text-white' ?> mb-1"><?= htmlspecialchars($u['username']) ?></h2>
                    <p class="text-sm font-black uppercase tracking-widest <?= $currentRank[2] ?> mb-3"><?= $currentRank[0] ?></p>
                    <?php if ($nextRank): ?>
                    <div class="rank-bar mb-1"><div class="rank-fill" style="width:<?= min(100,$nextRank[1]>0?($correct/$nextRank[1])*100:100) ?>%"></div></div>
                    <p class="text-xs text-indigo-400 font-bold italic">Plus que <?= $nextRank[1]-$correct ?> bonnes réponses pour « <?= $nextRank[0] ?> »</p>
                    <?php else: ?><p class="text-xs text-yellow-400 font-bold">🏅 Rang maximum atteint !</p><?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="stat-card"><p class="text-3xl font-black text-indigo-300"><?= $games ?></p><p class="text-[10px] font-black text-indigo-400 uppercase mt-1">Parties</p></div>
                <div class="stat-card"><p class="text-3xl font-black text-emerald-400"><?= $correct ?></p><p class="text-[10px] font-black text-indigo-400 uppercase mt-1">Bonnes rép.</p></div>
                <div class="stat-card"><p class="text-3xl font-black text-red-400"><?= $wrong ?></p><p class="text-[10px] font-black text-indigo-400 uppercase mt-1">Erreurs</p></div>
                <div class="stat-card"><p class="text-3xl font-black text-purple-300"><?= $precision ?>%</p><p class="text-[10px] font-black text-indigo-400 uppercase mt-1">Précision</p></div>
            </div>

            <div class="game-card p-6 flex justify-around items-center">
                <div class="text-center"><p class="text-4xl font-black text-yellow-400">🥇 <?= $p1 ?></p><p class="text-xs font-bold text-indigo-300 uppercase mt-2">Victoires</p></div>
                <div class="text-center"><p class="text-4xl font-black text-gray-300">🥈 <?= $p2 ?></p><p class="text-xs font-bold text-indigo-300 uppercase mt-2">2ème places</p></div>
                <div class="text-center"><p class="text-4xl font-black text-orange-400">🥉 <?= $p3 ?></p><p class="text-xs font-bold text-indigo-300 uppercase mt-2">3ème places</p></div>
            </div>

            <div class="game-card p-6">
                <h3 class="title-text text-xl text-white mb-6 border-b border-indigo-700 pb-3">🎯 Progression des Quêtes</h3>
                <?php if (!$is_member): ?>
                <div class="bg-indigo-900/40 border-2 border-indigo-700/40 rounded-xl p-4 mb-4 text-center">
                    <p class="text-sm font-black text-indigo-500">🔒 Quêtes réservées aux membres</p>
                </div>
                <?php endif; ?>
                <div class="flex items-center gap-5 mb-6 <?= !$is_member?'opacity-40':'' ?>">
                    <div class="text-4xl bg-[#2e2a72] p-3 rounded-2xl border-2 border-[#3b3687] flex-shrink-0"><?= $aura6_unlocked?'🌈':'🔒' ?></div>
                    <div class="flex-grow">
                        <p class="font-black text-lg <?= $aura6_unlocked?'text-indigo-300':'text-indigo-500' ?>">Effet Arc-en-Ciel</p>
                        <p class="text-xs text-indigo-400 mb-2">10 parties requises (<?= min(10,$games) ?>/10)</p>
                        <div class="rank-bar"><div class="rank-fill" style="width:<?= min(100,($games/10)*100) ?>%;background:linear-gradient(to right,#ec4899,#f59e0b,#6366f1);"></div></div>
                    </div>
                </div>
                <div class="flex items-center gap-5 <?= !$is_member?'opacity-40':'' ?>">
                    <div class="text-4xl bg-[#2e2a72] p-3 rounded-2xl border-2 border-[#3b3687] flex-shrink-0"><?= $aura7_unlocked?'☁️':'🔒' ?></div>
                    <div class="flex-grow">
                        <p class="font-black text-lg <?= $aura7_unlocked?'text-indigo-300':'text-indigo-500' ?>">Effet Lévitation</p>
                        <p class="text-xs text-indigo-400 mb-2">3 victoires requises (<?= min(3,$p1) ?>/3)</p>
                        <div class="rank-bar"><div class="rank-fill" style="width:<?= min(100,($p1/3)*100) ?>%"></div></div>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-indigo-400/50 font-bold pb-4">
                Avatars basés sur les assets open-source de <a href="https://pinknose.me" target="_blank" class="hover:text-indigo-300">pinknose.me</a>
            </p>
        </div>
    </div>
</div>

<script>
const BLANK         = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
const basePath      = "personnage/images/sections/";
const skinColors    = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
const commonColors  = [1,8,11,15];
const clothesColors = [1,19,31,40];
const hairStyles    = ['very_short','short','medium','long','shaved'];
const hairStyleNames= ['Très court','Court','Moyen','Long','Rasé'];

const isMember      = <?= $is_member?'true':'false' ?>;
const maxSkinColors = <?= $maxSkinColors ?>;
const maxHairColors = <?= $maxHairColors ?>;
const maxBeard      = <?= $maxBeard ?>;
const maxTop        = <?= $maxTop ?>;
const aura6Unlocked = <?= $aura6_unlocked?'true':'false' ?>;
const aura7Unlocked = <?= $aura7_unlocked?'true':'false' ?>;
const maxHairByStyle= isMember
    ? {very_short:15,short:17,medium:18,long:21,shaved:6}
    : {very_short:7, short:8, medium:9, long:10,shaved:3};

const effectDetails = [
    {name:"Aucun",       desc:""},
    {name:"Arc-en-Ciel", desc: aura6Unlocked ? "✓ Débloqué" : "10 parties (<?= min(10,$games) ?>/10)"},
    {name:"Lévitation",  desc: aura7Unlocked ? "✓ Débloqué" : "3 victoires (<?= min(3,$p1) ?>/3)"},
    {name:"Mystère",     desc:"À débloquer..."},
    {name:"Mystère",     desc:"À débloquer..."},
    {name:"Mystère",     desc:"À débloquer..."},
];

// État initialisé depuis PHP
const st = {
    skin:  {colorIdx:<?= $fav_skinColor ?>, maxColors:maxSkinColors},
    hair:  {type:<?= $fav_hair ?>,  colorIdx:<?= $fav_hairColor ?>,  styleIdx:<?= $fav_hairStyle ?>, maxType:8, maxColors:maxHairColors},
    beard: {type:<?= $fav_beard ?>, colorIdx:<?= $fav_beardColor ?>, maxType:maxBeard},
    top:   {type:<?= $fav_top ?>,   colorIdx:<?= $fav_topColor ?>,   maxType:maxTop},
    aura:  {type:<?= $fav_aura ?>,  maxType:5},
    effect:{type:<?= $fav_effect ?>,maxType:5},
};
st.hair.maxType = maxHairByStyle[hairStyles[st.hair.styleIdx]] ?? 8;

// Raccourci pour mettre à jour une img
function setImg(id, src) { const e=document.getElementById(id); if(e) e.src=src||BLANK; }

function renderPreview() {
    const skinC  = skinColors[st.skin.colorIdx] ?? 1;
    const hairC  = commonColors[st.hair.colorIdx ?? 0] ?? 1;
    const hairSt = hairStyles[st.hair.styleIdx] ?? 'short';
    const hairT  = st.hair.type;
    const beardC = commonColors[st.beard.colorIdx ?? 0] ?? 1;
    const topC   = clothesColors[st.top.colorIdx ?? 0] ?? 1;

    setImg('layer-skin',      `${basePath}Skin/1/${skinC}.png`);
    setImg('layer-mouth',     `${basePath}Mouth/1.png`);
    setImg('layer-eyes',      `${basePath}Eyes/1.png`);
    setImg('layer-eyebrow',   `${basePath}Eyebrow/1/1.png`);
    setImg('layer-nose',      `${basePath}Nose/1.png`);
    setImg('layer-top',       st.top.type>0 ? `${basePath}Top/Men/${st.top.type}/${topC}.png` : BLANK);
    setImg('layer-beard',     st.beard.type>0 ? `${basePath}Beards/${st.beard.type}/${beardC}.png` : BLANK);
    setImg('layer-hair-front',hairT>0 ? `${basePath}Hair/Front/${hairSt}/${hairT}/${hairC}.png` : BLANK);
    setImg('layer-hair-back', hairT>0 ? `${basePath}Hair/Back/${hairSt}/${hairT}/${hairC}.png`  : BLANK);

    // Aura
    const preview = document.getElementById('preview-container');
    preview.classList.remove('effect-rainbow','effect-levitate');
    if (st.aura.type>0 && st.aura.type<=5) {
        setImg('layer-aura', `personnage/aura/aura${st.aura.type}.png`);
        document.getElementById('layer-aura').style.zIndex=(st.aura.type==1||st.aura.type==5)?60:0;
    } else { setImg('layer-aura', BLANK); }
    if (st.effect.type==1) preview.classList.add('effect-rainbow');
    if (st.effect.type==2) preview.classList.add('effect-levitate');

    // Labels
    document.getElementById('lbl-skin-color').innerText = st.skin.colorIdx+1;
    document.getElementById('lbl-hair-style').innerText = hairStyleNames[st.hair.styleIdx]??'Court';
    document.getElementById('lbl-hair-type').innerText  = hairT||'Ø';
    document.getElementById('lbl-beard-type').innerText = st.beard.type||'Ø';
    document.getElementById('lbl-top-type').innerText   = st.top.type;
    if (isMember) {
        document.getElementById('lbl-aura-type').innerText   = st.aura.type||'Ø';
        document.getElementById('lbl-effect-type').innerText = st.effect.type||'Ø';
        const ef = effectDetails[st.effect.type]??effectDetails[0];
        document.getElementById('lbl-effect-name').innerText = ef.name;
        document.getElementById('lbl-effect-desc').innerText = ef.desc;
    }

    // Sync inputs
    document.getElementById('inp_skin').value        = 1;
    document.getElementById('inp_skin_color').value  = st.skin.colorIdx;
    document.getElementById('inp_hair').value        = st.hair.type;
    document.getElementById('inp_hair_style').value  = st.hair.styleIdx;
    document.getElementById('inp_hair_color').value  = st.hair.colorIdx??0;
    document.getElementById('inp_beard').value       = st.beard.type;
    document.getElementById('inp_beard_color').value = st.beard.colorIdx??0;
    document.getElementById('inp_top').value         = st.top.type;
    document.getElementById('inp_top_color').value   = st.top.colorIdx??0;
    document.getElementById('inp_aura').value        = st.aura.type;
    document.getElementById('inp_effect').value      = st.effect.type;
}

function ch(cat, prop, dir) {
    if (cat==='aura') {
        st.aura.type=(st.aura.type+dir+st.aura.maxType+1)%(st.aura.maxType+1);
        renderPreview(); return;
    }
    if (cat==='effect') {
        let n=st.effect.type+dir;
        if (n>st.effect.maxType) n=0;
        if (n<0) n=st.effect.maxType;
        if (n===1&&!aura6Unlocked) n+=dir;
        if (n===2&&!aura7Unlocked) n+=dir;
        if (n<0) n=0; if (n>st.effect.maxType) n=0;
        st.effect.type=n; renderPreview(); return;
    }
    if (prop==='type') {
        const min=(cat==='top')?1:0;
        st[cat].type=(st[cat].type||0)+dir;
        if (st[cat].type>st[cat].maxType) st[cat].type=min;
        if (st[cat].type<min) st[cat].type=st[cat].maxType;
    } else if (prop==='color') {
        const maxC=(cat==='skin')?maxSkinColors:(cat==='hair')?maxHairColors:commonColors.length;
        st[cat].colorIdx=((st[cat].colorIdx??0)+dir+maxC)%maxC;
    } else if (prop==='style'&&cat==='hair') {
        st.hair.styleIdx=(st.hair.styleIdx+dir+hairStyles.length)%hairStyles.length;
        st.hair.maxType=maxHairByStyle[hairStyles[st.hair.styleIdx]]??8;
        if (st.hair.type>st.hair.maxType) st.hair.type=st.hair.maxType;
    }
    renderPreview();
}

// PIN
const pinInput=document.getElementById('pin-input');
pinInput.addEventListener('input',()=>{pinInput.value=pinInput.value.replace(/\D/g,'').slice(0,6);});
pinInput.addEventListener('keydown',e=>{if(e.key==='Enter')joinGame();});
function joinGame(){const pin=pinInput.value.trim();if(pin.length<4)return pinInput.focus();window.location.href=`lobby?pin=${pin}`;}

// Chargement initial
window.addEventListener('load', renderPreview);
</script>
</body>
</html>