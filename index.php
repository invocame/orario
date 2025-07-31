<?php
date_default_timezone_set('Europe/Rome');

function parseInput($input) {
    $tokens = preg_split('/\s+/', trim($input));
    $events = [];
    
    for ($i = 0; $i < count($tokens); $i += 2) {
        if ($i + 1 >= count($tokens)) break;
        $event = strtoupper($tokens[$i]);
        $time = $tokens[$i + 1];
        
        if (($event === 'E' || $event === 'U') && preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            $events[] = [
                'type' => $event,
                'time' => $time
            ];
        } else {
            return []; // Invalid input
        }
    }
    
    return $events;
}

function calculateTimes($events) {
    $result = [];
    
    if (count($events) === 3 && 
        $events[0]['type'] === 'E' && 
        $events[1]['type'] === 'U' && 
        $events[2]['type'] === 'E') {
        
        // Calcolo orari di uscita
        $e1 = new DateTime($events[0]['time']);
        $u1 = new DateTime($events[1]['time']);
        $e2 = new DateTime($events[2]['time']);
        
        $morningWorked = $u1->diff($e1);
        $morningMinutes = $morningWorked->h * 60 + $morningWorked->i;
        
        // Calcolo orari di uscita per le diverse fasce
        $targets = [
            '6 ore' => 360,
            '7:12 ore' => 432,
            '9 ore' => 540
        ];
        
        foreach ($targets as $label => $targetMinutes) {
            $afternoonNeeded = $targetMinutes - $morningMinutes;
            if ($afternoonNeeded > 0) {
                $exitTime = clone $e2;
                $exitTime->add(new DateInterval('PT' . $afternoonNeeded . 'M'));
                $result[$label] = $exitTime->format('H:i');
            } else {
                $result[$label] = "Già completato";
            }
        }
        
        return [
            'type' => 'exit_times',
            'e1' => $events[0]['time'],
            'u1' => $events[1]['time'],
            'e2' => $events[2]['time'],
            'times' => $result
        ];
    }
    elseif (count($events) === 4 && 
             $events[0]['type'] === 'E' && 
             $events[1]['type'] === 'U' && 
             $events[2]['type'] === 'E' && 
             $events[3]['type'] === 'U') {
        
        // Calcolo ore effettive lavorate
        $e1 = new DateTime($events[0]['time']);
        $u1 = new DateTime($events[1]['time']);
        $e2 = new DateTime($events[2]['time']);
        $u2 = new DateTime($events[3]['time']);
        
        $morning = $u1->diff($e1);
        $afternoon = $u2->diff($e2);
        
        $totalMinutes = ($morning->h * 60 + $morning->i) + ($afternoon->h * 60 + $afternoon->i);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return [
            'type' => 'hours_worked',
            'e1' => $events[0]['time'],
            'u1' => $events[1]['time'],
            'e2' => $events[2]['time'],
            'u2' => $events[3]['time'],
            'total' => sprintf('%d:%02d', $hours, $minutes)
        ];
    }
    
    return false;
}

$input = $_POST['input'] ?? '';
$events = [];
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $events = parseInput($input);
    if (!empty($events)) {
        $result = calculateTimes($events);
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore Orario Lavorativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            font-weight: 600;
            padding: 1.5rem;
            border-bottom: none;
        }
        
        .form-control, .btn {
            border-radius: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--secondary), var(--primary));
            transform: translateY(-2px);
        }
        
        .result-card {
            background: white;
            border-left: 5px solid var(--success);
            animation: fadeIn 0.5s;
        }
        
        .timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 30px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
            z-index: 1;
        }
        
        .timeline-item {
            text-align: center;
            z-index: 2;
            position: relative;
            flex: 1;
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 18px;
        }
        
        .timeline-time {
            font-weight: bold;
            font-size: 18px;
            color: var(--dark);
        }
        
        .timeline-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        .exit-time-card {
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin: 15px 0;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .exit-time-label {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        
        .exit-time-value {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .total-hours {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            padding: 20px;
            background: rgba(76, 201, 240, 0.15);
            border-radius: 12px;
            margin-top: 20px;
        }
        
        .example {
            background: rgba(67, 97, 238, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .example-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(67, 97, 238, 0); }
            100% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card mb-4 pulse">
                    <div class="card-header text-center">
                        <h1 class="mb-0"><i class="fas fa-clock me-2"></i>Calcolatore Orario Lavorativo</h1>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-4">
                                <label for="input" class="form-label fw-bold">Inserisci i tuoi orari:</label>
                                <input type="text" class="form-control form-control-lg" id="input" name="input" 
                                       placeholder="Es: E 09:27 U 12:24 E 12:56 oppure E 09:35 U 12:30 E 12:51 U 18:12"
                                       value="<?= htmlspecialchars($input) ?>">
                                <div class="form-text">
                                    Usa "E" per entrata e "U" per uscita, seguiti dall'orario (HH:MM)
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-calculator me-2"></i>Calcola
                                </button>
                            </div>
                        </form>
                        
                        <div class="example mt-4">
                            <div class="example-title"><i class="fas fa-lightbulb me-2"></i>Esempi di utilizzo:</div>
                            <div class="mb-2"><strong>Calcolo orari di uscita:</strong> E 08:30 U 13:00 E 14:00</div>
                            <div><strong>Calcolo ore lavorate:</strong> E 08:45 U 13:15 E 14:30 U 18:10</div>
                        </div>
                    </div>
                </div>
                
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <?php if (!empty($events) && $result): ?>
                        <div class="card result-card">
                            <div class="card-header text-center">
                                <h2 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Risultato</h2>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <?php if ($result['type'] === 'exit_times'): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-icon"><i class="fas fa-sign-in-alt"></i></div>
                                            <div class="timeline-time"><?= $result['e1'] ?></div>
                                            <div class="timeline-label">Entrata</div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-icon"><i class="fas fa-utensils"></i></div>
                                            <div class="timeline-time"><?= $result['u1'] ?></div>
                                            <div class="timeline-label">Inizio pausa</div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-icon"><i class="fas fa-sign-in-alt"></i></div>
                                            <div class="timeline-time"><?= $result['e2'] ?></div>
                                            <div class="timeline-label">Fine pausa</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="timeline-item">
                                            <div class="timeline-icon"><i class="fas fa-sign-in-alt"></i></div>
                                            <div class="timeline-time"><?= $result['e1'] ?></div>
                                            <div class="timeline-label">Entrata</div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-icon"><i class="fas fa-utensils"></i></div>
                                            <div class="timeline-time"><?= $result['u1'] ?></div>
                                            <div class="timeline-label">Inizio pausa</div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-icon"><i class="fas fa-sign-in-alt"></i></div>
                                            <div class="timeline-time"><?= $result['e2'] ?></div>
                                            <div class="timeline-label">Fine pausa</div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-icon"><i class="fas fa-sign-out-alt"></i></div>
                                            <div class="timeline-time"><?= $result['u2'] ?></div>
                                            <div class="timeline-label">Uscita</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($result['type'] === 'exit_times'): ?>
                                    <h4 class="text-center mb-4">Orari di uscita per fasce orarie:</h4>
                                    <div class="row">
                                        <?php foreach ($result['times'] as $label => $time): ?>
                                            <div class="col-md-4">
                                                <div class="exit-time-card">
                                                    <div class="exit-time-label"><?= $label ?></div>
                                                    <div class="exit-time-value"><?= $time ?></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <h4 class="text-center mb-4">Ore lavorate totali:</h4>
                                    <div class="total-hours">
                                        <i class="fas fa-business-time me-2"></i>
                                        <?= $result['total'] ?> ore
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger mt-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Formato input non valido. Assicurati di inserire una sequenza valida come negli esempi.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
