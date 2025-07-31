<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore Orario Lavorativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --warning: #ff9e00;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--dark);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 25px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .card-header h1 {
            font-size: 1.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .input-container {
            position: relative;
        }
        
        input {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            outline: none;
        }
        
        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .input-hint {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn:hover {
            background: linear-gradient(to right, var(--secondary), var(--primary));
            transform: translateY(-2px);
        }
        
        .examples {
            background-color: rgba(67, 97, 238, 0.08);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            margin-top: 1.5rem;
        }
        
        .examples-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.75rem;
        }
        
        .example {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .example strong {
            color: var(--secondary);
        }
        
        .result-container {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 2rem 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            top: 22px;
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
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 18px;
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
        }
        
        .timeline-time {
            font-weight: 700;
            font-size: 18px;
            color: var(--dark);
        }
        
        .timeline-label {
            font-size: 14px;
            color: var(--gray);
            font-weight: 500;
        }
        
        .results {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 2rem 0;
        }
        
        .result-card {
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .result-label {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }
        
        .result-value {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .total-hours {
            background: linear-gradient(to right, #ff9e00, #ff6b00);
            color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            margin: 2rem 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .total-label {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }
        
        .total-value {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 1px;
        }
        
        .error {
            display: none;
            background: linear-gradient(to right, var(--danger), #d90429);
            color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin: 1.5rem 0;
            text-align: center;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            font-size: 14px;
        }
        
        .footer {
            text-align: center;
            color: var(--gray);
            font-size: 0.9rem;
            margin-top: 1.5rem;
            padding: 1rem;
        }
        
        @media (max-width: 768px) {
            .timeline {
                flex-wrap: wrap;
            }
            
            .timeline-item {
                flex: 0 0 50%;
                margin-bottom: 1.5rem;
            }
            
            .timeline::before {
                display: none;
            }
            
            .results {
                grid-template-columns: 1fr;
            }
            
            .card-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-clock"></i> Calcolatore Orario Lavorativo</h1>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="timeInput">Inserisci i tuoi orari:</label>
                    <div class="input-container">
                        <input type="text" id="timeInput" placeholder="Es: E 09:27 U 12:24 E 12:56 oppure E 09:35 U 12:30 E 12:51 U 18:12">
                    </div>
                    <div class="input-hint">
                        Usa "E" per entrata e "U" per uscita, seguiti dall'orario (HH:MM)
                    </div>
                </div>
                
                <button id="calculateBtn" class="btn">
                    <i class="fas fa-calculator"></i> Calcola
                </button>
                
                <div class="examples">
                    <div class="examples-title">
                        <i class="fas fa-lightbulb"></i> Esempi di utilizzo
                    </div>
                    <div class="example">
                        <strong>Calcolo orari di uscita:</strong> E 08:30 U 13:00 E 14:00
                    </div>
                    <div class="example">
                        <strong>Calcolo ore lavorate:</strong> E 08:45 U 13:15 E 14:30 U 18:10
                    </div>
                </div>
                
                <div id="error" class="error">
                    <i class="fas fa-exclamation-triangle"></i> Formato non valido! Utilizza il formato: E 09:27 U 12:24 E 12:56
                </div>
                
                <div id="resultContainer" class="result-container">
                    <div class="timeline" id="timeline">
                        <!-- Timeline will be generated dynamically -->
                    </div>
                    
                    <div id="exitTimes">
                        <h2 style="text-align: center; margin-bottom: 1.5rem; color: var(--primary);">
                            Orari di uscita per fasce orarie
                        </h2>
                        <div class="results">
                            <div class="result-card">
                                <div class="result-label">6 ore lavorate</div>
                                <div class="result-value" id="exit6">--:--</div>
                            </div>
                            <div class="result-card">
                                <div class="result-label">7 ore e 12 minuti</div>
                                <div class="result-value" id="exit712">--:--</div>
                            </div>
                            <div class="result-card">
                                <div class="result-label">9 ore lavorate</div>
                                <div class="result-value" id="exit9">--:--</div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="totalHours">
                        <div class="total-hours">
                            <div class="total-label">Ore lavorate totali</div>
                            <div class="total-value" id="hoursTotal">--:--</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            Inserisci la sequenza di orari per calcolare quando puoi uscire o le ore effettivamente lavorate
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeInput = document.getElementById('timeInput');
            const calculateBtn = document.getElementById('calculateBtn');
            const errorDiv = document.getElementById('error');
            const resultContainer = document.getElementById('resultContainer');
            const timeline = document.getElementById('timeline');
            const exitTimes = document.getElementById('exitTimes');
            const totalHours = document.getElementById('totalHours');
            const exit6 = document.getElementById('exit6');
            const exit712 = document.getElementById('exit712');
            const exit9 = document.getElementById('exit9');
            const hoursTotal = document.getElementById('hoursTotal');
            
            // Focus on input when page loads
            timeInput.focus();
            
            calculateBtn.addEventListener('click', calculateTimes);
            timeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    calculateTimes();
                }
            });
            
            function calculateTimes() {
                const input = timeInput.value.trim();
                
                // Reset UI
                errorDiv.style.display = 'none';
                resultContainer.style.display = 'none';
                exitTimes.style.display = 'none';
                totalHours.style.display = 'none';
                
                // Validate input
                if (!input) {
                    showError('Inserisci una sequenza di orari');
                    return;
                }
                
                // Parse input
                const tokens = input.split(/\s+/);
                if (tokens.length % 2 !== 0) {
                    showError('Formato non valido. Il numero di elementi deve essere pari');
                    return;
                }
                
                // Parse events
                const events = [];
                let isValid = true;
                
                for (let i = 0; i < tokens.length; i += 2) {
                    const type = tokens[i].toUpperCase();
                    const time = tokens[i + 1];
                    
                    if ((type === 'E' || type === 'U') && isValidTime(time)) {
                        events.push({ type, time });
                    } else {
                        isValid = false;
                        break;
                    }
                }
                
                if (!isValid || events.length < 3) {
                    showError('Formato non valido. Usa "E" o "U" seguiti da orario (es: E 09:30)');
                    return;
                }
                
                // Process events
                if (events.length === 3 && 
                    events[0].type === 'E' && 
                    events[1].type === 'U' && 
                    events[2].type === 'E') {
                    // Calculate exit times
                    calculateExitTimes(events);
                } else if (events.length === 4 && 
                           events[0].type === 'E' && 
                           events[1].type === 'U' && 
                           events[2].type === 'E' && 
                           events[3].type === 'U') {
                    // Calculate total hours
                    calculateTotalHours(events);
                } else {
                    showError('Sequenza non valida. Usa E-U-E per orari uscita o E-U-E-U per ore lavorate');
                }
            }
            
            function isValidTime(time) {
                return /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(time);
            }
            
            function showError(message) {
                errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
                errorDiv.style.display = 'block';
            }
            
            function timeToMinutes(time) {
                const [hours, minutes] = time.split(':').map(Number);
                return hours * 60 + minutes;
            }
            
            function minutesToTime(totalMinutes) {
                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            }
            
            function calculateExitTimes(events) {
                const [e1, u1, e2] = events.map(event => timeToMinutes(event.time));
                
                // Calculate morning minutes worked
                const morningMinutes = u1 - e1;
                
                // Target minutes (6h, 7:12h, 9h)
                const target6 = 360; // 6 hours
                const target712 = 432; // 7 hours 12 minutes
                const target9 = 540; // 9 hours
                
                // Calculate exit times
                const exit6Time = minutesToTime(e2 + (target6 - morningMinutes));
                const exit712Time = minutesToTime(e2 + (target712 - morningMinutes));
                const exit9Time = minutesToTime(e2 + (target9 - morningMinutes));
                
                // Update UI
                updateTimeline(events);
                exit6.textContent = exit6Time;
                exit712.textContent = exit712Time;
                exit9.textContent = exit9Time;
                
                exitTimes.style.display = 'block';
                totalHours.style.display = 'none';
                resultContainer.style.display = 'block';
            }
            
            function calculateTotalHours(events) {
                const [e1, u1, e2, u2] = events.map(event => timeToMinutes(event.time));
                
                // Calculate total minutes worked
                const morningMinutes = u1 - e1;
                const afternoonMinutes = u2 - e2;
                const totalMinutes = morningMinutes + afternoonMinutes;
                
                // Convert to HH:MM
                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                const totalTime = `${hours}:${minutes.toString().padStart(2, '0')}`;
                
                // Update UI
                updateTimeline(events);
                hoursTotal.textContent = totalTime;
                
                exitTimes.style.display = 'none';
                totalHours.style.display = 'block';
                resultContainer.style.display = 'block';
            }
            
            function updateTimeline(events) {
                timeline.innerHTML = '';
                
                const labels = {
                    'E': { icon: 'sign-in-alt', text: 'Entrata' },
                    'U': { icon: 'sign-out-alt', text: 'Uscita' }
                };
                
                events.forEach((event, index) => {
                    const item = document.createElement('div');
                    item.className = 'timeline-item';
                    
                    const icon = document.createElement('div');
                    icon.className = 'timeline-icon';
                    icon.innerHTML = `<i class="fas fa-${labels[event.type].icon}"></i>`;
                    
                    const time = document.createElement('div');
                    time.className = 'timeline-time';
                    time.textContent = event.time;
                    
                    const label = document.createElement('div');
                    label.className = 'timeline-label';
                    
                    if (index === 1) {
                        label.textContent = 'Inizio pausa';
                    } else if (index === 2 && events.length === 4) {
                        label.textContent = 'Fine pausa';
                    } else {
                        label.textContent = labels[event.type].text;
                    }
                    
                    item.appendChild(icon);
                    item.appendChild(time);
                    item.appendChild(label);
                    
                    timeline.appendChild(item);
                });
            }
            
            // Example input for demo
            timeInput.value = "E 09:27 U 12:24 E 12:56";
        });
    </script>
</body>
</html>
