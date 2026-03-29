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
        'answers' => new stdClass(), 
        'status' => 'lobby', 
        'current_q_index' => -1, 
        'last_update' => time()
    ];
}

switch ($action) {
    case 'join':
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $nick = htmlspecialchars($input['nickname'] ?? 'Anonyme');
        
        $scoresArr = (array)($state['scores'] ?? []);
        if (!isset($scoresArr[$nick])) {
            $players = (array)($state['players'] ?? []);
            $players[] = [
                'nickname' => $nick,
                'hair' => (int)($input['hair'] ?? 1),
                'outfit' => (int)($input['outfit'] ?? 1),
                'aura' => (int)($input['aura'] ?? 0),
                'is_member' => filter_var($input['is_member'] ?? false, FILTER_VALIDATE_BOOLEAN)
            ];
            $state['players'] = $players;
            
            $scoresArr[$nick] = 0;
            $state['scores'] = (object)$scoresArr;
            
            $state['correct_counts'] = (object)array_merge((array)($state['correct_counts'] ?? []), [$nick => 0]);
            $state['wrong_counts'] = (object)array_merge((array)($state['wrong_counts'] ?? []), [$nick => 0]);
            $state['response_times'] = (object)array_merge((array)($state['response_times'] ?? []), [$nick => 0]);
            $state['streaks'] = (object)array_merge((array)($state['streaks'] ?? []), [$nick => 0]);
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
        break;

    case 'activate_playing':
        $state['status'] = 'playing';
        break;

    case 'submit_answer':
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $nick = $input['nickname'] ?? '';
        $qIdx = (int)($state['current_q_index'] ?? 0);
        
        $eliminated = (array)($state['eliminated'] ?? []);
        if (in_array($nick, $eliminated)) { break; }
        
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

            $isCorrect = filter_var($input['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN);
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
                
                $correctCounts = (array)($state['correct_counts'] ?? []);
                $correctCounts[$nick] = ($correctCounts[$nick] ?? 0) + 1;
                $state['correct_counts'] = (object)$correctCounts;
            } else {
                $pdo->prepare("UPDATE users SET total_wrong = total_wrong + 1 WHERE username = ?")->execute([$nick]);
                $streaks[$nick] = 0;

                $wrongCounts = (array)($state['wrong_counts'] ?? []);
                $wrongCounts[$nick] = ($wrongCounts[$nick] ?? 0) + 1;
                $state['wrong_counts'] = (object)$wrongCounts;
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
                    if ($score < $lowestScore) {
                        $lowestScore = $score;
                        $worstPlayer = $nick;
                    }
                }
            }
            
            $activeCount = count($players) - count($elim);
            if ($worstPlayer && $activeCount > 1) {
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