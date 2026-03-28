<?php
require_once 'db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$pin = $_GET['pin'] ?? '';
$chemin_sessions = __DIR__ . '/sessions';

if (!is_dir($chemin_sessions)) { mkdir($chemin_sessions, 0777, true); }
$gameStateFile = $chemin_sessions . '/game_' . $pin . '.json';

if (file_exists($gameStateFile)) {
    $state = json_decode(file_get_contents($gameStateFile), true);
} else {
    $state = [
        'mode' => 'classique',
        'eliminated' => [],
        'players' => [], 
        'scores' => new stdClass(), 
        'answers' => new stdClass(), 
        'status' => 'lobby', 
        'current_q_index' => -1, 
        'last_update' => time()
    ];
}

switch ($action) {
    case 'join':
        $input = json_decode(file_get_contents('php://input'), true);
        $nick = htmlspecialchars($input['nickname'] ?? 'Anonyme');
        
        $scoresArr = (array)$state['scores'];
        if (!isset($scoresArr[$nick])) {
            $state['players'][] = [
                'nickname' => $nick,
                'hair' => (int)($input['hair'] ?? 1),
                'outfit' => (int)($input['outfit'] ?? 1),
                'aura' => (int)($input['aura'] ?? 0)
            ];
            $scoresArr[$nick] = 0;
            $state['scores'] = (object)$scoresArr;
        }
        break;

    case 'start_game':
        $quiz_id = $_GET['quiz_id'];
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
        $stmt->execute([$quiz_id]);
        $qs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $state['status'] = 'reveal'; // Révélation de la question pendant 2 secondes
        $state['questions_list'] = $qs;
        $state['current_q_index'] = 0;
        $state['question'] = $qs[0];
        $state['answers'] = new stdClass();
        $state['eliminated'] = [];
        break;

    case 'activate_playing':
        // Activé automatiquement par le frontend après les 2 secondes
        $state['status'] = 'playing';
        break;

    case 'submit_answer':
        $input = json_decode(file_get_contents('php://input'), true);
        $nick = $input['nickname'] ?? '';
        $qIdx = (int)$state['current_q_index'];
        
        if (in_array($nick, $state['eliminated'] ?? [])) {
            break; // Joueur éliminé
        }
        
        $allAnswers = (array)$state['answers'];
        if (!isset($allAnswers[$qIdx])) { $allAnswers[$qIdx] = []; }
        $currentQAnswers = (array)$allAnswers[$qIdx];

        if ($nick && !isset($currentQAnswers[$nick])) {
            $currentQAnswers[$nick] = $input['answer_index'];
            $allAnswers[$qIdx] = (object)$currentQAnswers;
            $state['answers'] = (object)$allAnswers;

            $isCorrect = filter_var($input['is_correct'], FILTER_VALIDATE_BOOLEAN);
            
            if ($isCorrect) {
                $pdo->prepare("UPDATE users SET total_correct = total_correct + 1 WHERE username = ?")->execute([$nick]);
                $timeTaken = (float)($input['response_time'] ?? 0);
                $pts = max(500, 1000 - (int)($timeTaken * 50));
                
                $scoresArr = (array)$state['scores'];
                $scoresArr[$nick] = ($scoresArr[$nick] ?? 0) + $pts;
                $state['scores'] = (object)$scoresArr;
            } else {
                $pdo->prepare("UPDATE users SET total_wrong = total_wrong + 1 WHERE username = ?")->execute([$nick]);
            }
        }
        break;

    case 'show_leaderboard':
        $state['status'] = 'leaderboard';
        
        if (($state['mode'] ?? 'classique') === 'br') {
            $scoresArr = (array)$state['scores'];
            $worstPlayer = null;
            $lowestScore = 99999999;
            
            foreach ($state['players'] as $p) {
                $nick = $p['nickname'];
                if (!in_array($nick, $state['eliminated'] ?? [])) {
                    $score = $scoresArr[$nick] ?? 0;
                    if ($score < $lowestScore) {
                        $lowestScore = $score;
                        $worstPlayer = $nick;
                    }
                }
            }
            
            $activeCount = count($state['players']) - count($state['eliminated'] ?? []);
            if ($worstPlayer && $activeCount > 1) {
                $state['eliminated'][] = $worstPlayer;
            }
        }
        break;

    case 'next_step':
        $state['current_q_index']++;
        if ($state['current_q_index'] < count($state['questions_list'] ?? [])) {
            $state['status'] = 'reveal'; // Repasse en mode révélation pour la question suivante
            $state['question'] = $state['questions_list'][$state['current_q_index']];
        } else {
            $state['status'] = 'finished';
            
            $finalScores = (array)$state['scores'];
            arsort($finalScores);
            $topNicks = array_keys($finalScores);

            foreach ($state['players'] as $p) {
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