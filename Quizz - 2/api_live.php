<?php
ini_set('display_errors', 0); 
require_once 'db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$pin = $_GET['pin'] ?? '';
$chemin_sessions = __DIR__ . '/sessions';

if (!is_dir($chemin_sessions)) { mkdir($chemin_sessions, 0777, true); }
$gameStateFile = $chemin_sessions . '/game_' . $pin . '.json';

if (file_exists($gameStateFile)) {
    $state = json_decode(file_get_contents($gameStateFile), true) ?: [];
} else {
    $state = [
        'mode' => 'classique',
        'eliminated' => [],
        'players' => [], 
        'scores' => new stdClass(), 
        'correct_counts' => new stdClass(), 
        'wrong_counts' => new stdClass(),
        'response_times' => new stdClass(),
        'streaks' => new stdClass(), 
        'hearts' => new stdClass(),
        'answers' => new stdClass(),
        'chat' => [],
        'status' => 'lobby', 
        'current_q_index' => -1, 
        'last_update' => time()
    ];
}

switch ($action) {
    case 'join':
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $nick = htmlspecialchars($input['nickname'] ?? 'Anonyme');
        
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['current_pin'] = $pin;
        $_SESSION['current_nick'] = $nick;

        // Vérifier si ce pseudo correspond à un compte inscrit en DB
        // is_member = true pour TOUS les utilisateurs ayant un compte (même rôle "utilisateur")
        // is_member = false uniquement pour les joueurs anonymes (sans compte)
        $dbUser = $pdo->prepare("SELECT role FROM users WHERE username = ?");
        $dbUser->execute([$nick]);
        $dbRow = $dbUser->fetch(PDO::FETCH_ASSOC);
        $playerIsMember = ($dbRow !== false);
        $playerRole     = $dbRow ? $dbRow['role'] : 'anonyme';

        $scoresArr = (array)($state['scores'] ?? []);
        if (!isset($scoresArr[$nick])) {
            $players = (array)($state['players'] ?? []);
            
            // Configuration complète de l'avatar (nouveau système en couches)
            $players[] = [
                'nickname'      => $nick,
                'is_member'     => $playerIsMember,
                'role'          => $playerRole,
                // Peau
                'skin'          => (int)($input['skin'] ?? 1),
                'skinColor'     => (int)($input['skinColor'] ?? 0),
                // Visage (fixes)
                'eyes'          => (int)($input['eyes'] ?? 1),
                'mouth'         => (int)($input['mouth'] ?? 1),
                'nose'          => (int)($input['nose'] ?? 1),
                'eyebrow'       => (int)($input['eyebrow'] ?? 1),
                'eyebrowColor'  => (int)($input['eyebrowColor'] ?? 0),
                // Cheveux
                'hair'          => (int)($input['hair'] ?? 1),
                'hairColor'     => (int)($input['hairColor'] ?? 0),
                'hairStyle'     => (int)($input['hairStyle'] ?? 1),
                // Barbe & Moustache
                'beard'         => (int)($input['beard'] ?? 0),
                'beardColor'    => (int)($input['beardColor'] ?? 0),
                'mustache'      => (int)($input['mustache'] ?? 0),
                'mustacheColor' => (int)($input['mustacheColor'] ?? 0),
                // Vêtements de base
                'top'           => (int)($input['top'] ?? 1),
                'topColor'      => (int)($input['topColor'] ?? 0),
                'jacket'        => (int)($input['jacket'] ?? 0),
                'jacketColor'   => (int)($input['jacketColor'] ?? 0),
                // Costume spécial
                'antiquity'     => (int)($input['antiquity'] ?? 0),
                'christmas'     => (int)($input['christmas'] ?? 0),
                'halloween'     => (int)($input['halloween'] ?? 0),
                'job'           => (int)($input['job'] ?? 0),
                'medieval'      => (int)($input['medieval'] ?? 0),
                'neutral'       => (int)($input['neutral'] ?? 0),
                'pirate'        => (int)($input['pirate'] ?? 0),
                // Effets
                'aura'          => (int)($input['aura'] ?? 0),
                'effect'        => (int)($input['effect'] ?? 0),
            ];
            $state['players'] = $players;
            
            $scoresArr[$nick] = 0;
            $state['scores'] = (object)$scoresArr;
            $state['correct_counts'] = (object)array_merge((array)($state['correct_counts'] ?? []), [$nick => 0]);
            $state['wrong_counts'] = (object)array_merge((array)($state['wrong_counts'] ?? []), [$nick => 0]);
            $state['response_times'] = (object)array_merge((array)($state['response_times'] ?? []), [$nick => 0]);
            $state['streaks'] = (object)array_merge((array)($state['streaks'] ?? []), [$nick => 0]);
            $state['hearts'] = (object)array_merge((array)($state['hearts'] ?? []), [$nick => 3]);
        }
        break;

    case 'start_game':
        $quiz_id = $_GET['quiz_id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
        $stmt->execute([$quiz_id]);
        $qs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $state['status'] = 'reveal';
        $state['questions_list'] = $qs;
        $state['current_q_index'] = 0;
        $state['question'] = $qs[0] ?? null;
        $state['answers'] = new stdClass();
        $state['eliminated'] = [];
        $state['correct_counts'] = new stdClass(); 
        $state['wrong_counts'] = new stdClass();
        $state['response_times'] = new stdClass();
        $state['streaks'] = new stdClass(); 
        $state['chat'] = []; 
        
        $hearts = [];
        foreach($state['players'] as $p) { $hearts[$p['nickname']] = 3; }
        $state['hearts'] = (object)$hearts;
        break;

    case 'activate_playing':
        $state['status'] = 'playing';
        break;

    case 'send_chat':
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $nick = htmlspecialchars($input['nickname'] ?? '');
        $msg = htmlspecialchars(trim($input['message'] ?? ''));
        
        if ($nick && $msg) {
            $chat = (array)($state['chat'] ?? []);
            $chat[] = ['nick' => $nick, 'msg' => $msg, 'time' => time()];
            if (count($chat) > 20) { array_shift($chat); }
            $state['chat'] = $chat;
        }
        break;

    case 'submit_answer':
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $nick = $input['nickname'] ?? '';
        $qIdx = (int)($state['current_q_index'] ?? 0);
        
        $elim = (array)($state['eliminated'] ?? []);
        if (in_array($nick, $elim)) { break; }
        
        $allAnswers = (array)($state['answers'] ?? []);
        if (!isset($allAnswers[$qIdx])) { $allAnswers[$qIdx] = []; }
        $currentQAnswers = (array)$allAnswers[$qIdx];

        if ($nick && !isset($currentQAnswers[$nick])) {
            $currentQAnswers[$nick] = $input['answer_index'] ?? 1;
            $allAnswers[$qIdx] = (object)$currentQAnswers;
            $state['answers'] = (object)$allAnswers;

            $timeTaken = (float)($input['response_time'] ?? 0);
            $rtArr = (array)($state['response_times'] ?? []);
            $rtArr[$nick] = ($rtArr[$nick] ?? 0) + $timeTaken;
            $state['response_times'] = (object)$rtArr;

            $currentQuestion = (array)($state['questions_list'] ?? [])[$qIdx] ?? [];
$isCorrect = isset($currentQuestion['correct_answer']) && (int)($input['answer_index'] ?? 0) === (int)$currentQuestion['correct_answer'];
            $qList = (array)($state['questions_list'] ?? []);
            $isLastQuestion = ($qIdx === count($qList) - 1);
            
            $streaks = (array)($state['streaks'] ?? []);
            $currentStreak = $streaks[$nick] ?? 0;

            if ($isCorrect) {
                $pdo->prepare("UPDATE users SET total_correct = total_correct + 1 WHERE username = ?")->execute([$nick]);
                $currentStreak++;
                $streaks[$nick] = $currentStreak;

                $pts = max(500, 1000 - (int)($timeTaken * 50));
                if ($isLastQuestion) { $pts *= 2; }
                if ($currentStreak >= 3) { $pts += 200; }

                $scoresArr = (array)($state['scores'] ?? []);
                $scoresArr[$nick] = ($scoresArr[$nick] ?? 0) + $pts;
                $state['scores'] = (object)$scoresArr;
                
                $cc = (array)($state['correct_counts'] ?? []);
                $cc[$nick] = ($cc[$nick] ?? 0) + 1;
                $state['correct_counts'] = (object)$cc;
            } else {
                $pdo->prepare("UPDATE users SET total_wrong = total_wrong + 1 WHERE username = ?")->execute([$nick]);
                $streaks[$nick] = 0;
                $wc = (array)($state['wrong_counts'] ?? []);
                $wc[$nick] = ($wc[$nick] ?? 0) + 1;
                $state['wrong_counts'] = (object)$wc;
            }
            $state['streaks'] = (object)$streaks;
        }
        break;

    case 'show_answer':
        $state['status'] = 'show_answer';
        $qIdx = (int)($state['current_q_index'] ?? 0);
        $allAnswers = (array)($state['answers'] ?? []);
        $currentQAnswers = (array)($allAnswers[$qIdx] ?? []);
        
        $counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        foreach($currentQAnswers as $n => $ansIndex) {
            if(isset($counts[$ansIndex])) { $counts[$ansIndex]++; }
        }
        $state['answer_counts'] = $counts;

        if (($state['mode'] ?? 'classique') === 'survie') {
            $hearts = (array)($state['hearts'] ?? []);
            $elim = (array)($state['eliminated'] ?? []);
            
            foreach ($state['players'] as $p) {
                $n = $p['nickname'];
                if (in_array($n, $elim)) continue;
                
                $ans = $currentQAnswers[$n] ?? null;
                $isCorrect = ($ans == $state['question']['correct_answer']);
                
                if (!$isCorrect) { 
                    $hearts[$n] = max(0, ($hearts[$n] ?? 3) - 1);
                    if ($hearts[$n] == 0) { $elim[] = $n; }
                }
            }
            $state['hearts'] = (object)$hearts;
            $state['eliminated'] = $elim;
        }
        break;

    case 'show_leaderboard':
        $state['status'] = 'leaderboard';
        
        if (($state['mode'] ?? 'classique') === 'br') {
            $scoresArr = (array)($state['scores'] ?? []);
            $worstPlayer = null;
            $lowestScore = 99999999;
            $players = (array)($state['players'] ?? []);
            $elim = (array)($state['eliminated'] ?? []);
            
            foreach ($players as $p) {
                $nick = $p['nickname'];
                if (!in_array($nick, $elim)) {
                    $score = $scoresArr[$nick] ?? 0;
                    if ($score < $lowestScore) { $lowestScore = $score; $worstPlayer = $nick; }
                }
            }
            
            if ($worstPlayer && (count($players) - count($elim)) > 1) {
                $elim[] = $worstPlayer;
                $state['eliminated'] = $elim;
            }
        }
        break;

    case 'next_step':
        $state['current_q_index'] = (int)($state['current_q_index'] ?? 0) + 1;
        $qList = (array)($state['questions_list'] ?? []);
        
        if ($state['current_q_index'] < count($qList)) {
            $state['status'] = 'reveal';
            $state['question'] = $qList[$state['current_q_index']];
        } else {
            $state['status'] = 'finished';
            $finalScores = (array)($state['scores'] ?? []);
            arsort($finalScores);
            $topNicks = array_keys($finalScores);

            $players = (array)($state['players'] ?? []);
            foreach ($players as $p) {
                $pdo->prepare("UPDATE users SET total_games = total_games + 1 WHERE username = ?")->execute([$p['nickname']]);
            }

            if (isset($topNicks[0])) $pdo->prepare("UPDATE users SET podium_1 = podium_1 + 1 WHERE username = ?")->execute([$topNicks[0]]);
            if (isset($topNicks[1])) $pdo->prepare("UPDATE users SET podium_2 = podium_2 + 1 WHERE username = ?")->execute([$topNicks[1]]);
            if (isset($topNicks[2])) $pdo->prepare("UPDATE users SET podium_3 = podium_3 + 1 WHERE username = ?")->execute([$topNicks[2]]);
        }
        break;

    case 'get_state':
        echo json_encode($state);
        exit;
}

$state['last_update'] = time();
file_put_contents($gameStateFile, json_encode($state));
echo json_encode(['status' => 'success']);