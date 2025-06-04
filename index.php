<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Reader App - Hardware Interface</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .main-content {
            padding: 40px;
        }

        .connection-section {
            background: #f8f9ff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 2px solid #e0e8ff;
        }

        .connection-section.connected {
            border-color: #10b981;
            background: #ecfdf5;
        }

        .connection-status {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 10px;
            background: #ef4444;
        }

        .status-indicator.connected {
            background: #10b981;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1em;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 5px;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 172, 254, 0.4);
        }

        .btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .btn.danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .scanner-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .scanner-controls {
            text-align: center;
            margin-bottom: 20px;
        }

        .rfid-data {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #e5e7eb;
        }

        .data-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .data-label {
            font-weight: bold;
            color: #374151;
        }

        .data-value {
            color: #1f2937;
            font-family: 'Courier New', monospace;
            background: white;
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #d1d5db;
            word-break: break-all;
        }

        .settings-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .setting-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .setting-label {
            flex: 1;
            font-weight: bold;
            color: #374151;
        }

        select, input {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
        }

        .logs {
            background: #1f2937;
            color: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        .log-entry {
            margin: 3px 0;
            padding: 2px 0;
        }

        .timestamp {
            color: #60a5fa;
            margin-right: 8px;
        }

        .error { color: #fca5a5; }
        .success { color: #86efac; }
        .warning { color: #fde047; }

        .tag-history {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .tag-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s;
        }

        .tag-item:hover {
            background: #f9fafb;
        }

        .tag-id {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .tag-time {
            color: #6b7280;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .data-grid {
                grid-template-columns: 1fr;
            }
            
            .setting-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 RFID Hardware Reader</h1>
            <p>Direct connection to RFID sensors via Web Serial API</p>
        </div>

        <div class="main-content">
            <!-- Connection Section -->
            <div class="connection-section" id="connectionSection">
                <div class="connection-status">
                    <div class="status-indicator" id="statusIndicator"></div>
                    <span id="connectionStatus">Disconnected</span>
                </div>
                <p id="deviceInfo">No RFID device connected</p>
                <button class="btn" id="connectBtn" onclick="connectToDevice()">Connect to RFID Reader</button>
                <button class="btn danger" id="disconnectBtn" onclick="disconnectDevice()" disabled>Disconnect</button>
            </div>

            <!-- Settings Section -->
            <div class="settings-section">
                <h3>⚙️ Reader Settings</h3>
                <div class="setting-row">
                    <label class="setting-label">Baud Rate:</label>
                    <select id="baudRate">
                        <option value="9600">9600</option>
                        <option value="19200">19200</option>
                        <option value="38400">38400</option>
                        <option value="57600">57600</option>
                        <option value="115200" selected>115200</option>
                    </select>
                </div>
                <div class="setting-row">
                    <label class="setting-label">Data Bits:</label>
                    <select id="dataBits">
                        <option value="7">7</option>
                        <option value="8" selected>8</option>
                    </select>
                </div>
                <div class="setting-row">
                    <label class="setting-label">Stop Bits:</label>
                    <select id="stopBits">
                        <option value="1" selected>1</option>
                        <option value="2">2</option>
                    </select>
                </div>
                <div class="setting-row">
                    <label class="setting-label">Parity:</label>
                    <select id="parity">
                        <option value="none" selected>None</option>
                        <option value="even">Even</option>
                        <option value="odd">Odd</option>
                    </select>
                </div>
            </div>

            <!-- Scanner Section -->
            <div class="scanner-section">
                <h3>📡 RFID Scanner</h3>
                <div class="scanner-controls">
                    <button class="btn" id="scanBtn" onclick="startScanning()" disabled>Start Scanning</button>
                    <button class="btn danger" id="stopBtn" onclick="stopScanning()" disabled>Stop Scanning</button>
                    <button class="btn" onclick="sendCommand('INVENTORY')" disabled id="inventoryBtn">Inventory Tags</button>
                </div>

                <div class="rfid-data" id="rfidData" style="display: none;">
                    <h4>Current Tag Data</h4>
                    <div class="data-grid">
                        <span class="data-label">EPC/Tag ID:</span>
                        <span class="data-value" id="tagEPC">-</span>
                        
                        <span class="data-label">TID:</span>
                        <span class="data-value" id="tagTID">-</span>
                        
                        <span class="data-label">RSSI:</span>
                        <span class="data-value" id="tagRSSI">-</span>
                        
                        <span class="data-label">Read Count:</span>
                        <span class="data-value" id="readCount">-</span>
                        
                        <span class="data-label">Frequency:</span>
                        <span class="data-value" id="frequency">-</span>
                        
                        <span class="data-label">Timestamp:</span>
                        <span class="data-value" id="timestamp">-</span>
                    </div>
                </div>
            </div>

            <!-- Tag History -->
            <div class="tag-history">
                <h3>📋 Tag History</h3>
                <div id="tagHistory"></div>
                <button class="btn" onclick="clearHistory()">Clear History</button>
                <button class="btn" onclick="exportHistory()">Export CSV</button>
            </div>

            <!-- Activity Log -->
            <div class="logs">
                <h3>📄 Activity Log</h3>
                <div id="logContainer"></div>
                <button class="btn" onclick="clearLogs()" style="margin-top: 10px;">Clear Logs</button>
            </div>
        </div>
    </div>

    <script>
        let port = null;
        let reader = null;
        let writer = null;
        let isScanning = false;
        let tagHistory = [];

        // Check if Web Serial API is supported
        if (!('serial' in navigator)) {
            addLog('Web Serial API not supported. Please use Chrome/Edge 89+', 'error');
            document.getElementById('connectBtn').disabled = true;
        }

        function addLog(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.className = `log-entry ${type}`;
            logEntry.innerHTML = `<span class="timestamp">[${timestamp}]</span>${message}`;
            document.getElementById('logContainer').appendChild(logEntry);
            document.getElementById('logContainer').scrollTop = document.getElementById('logContainer').scrollHeight;
        }

        async function connectToDevice() {
            try {
                addLog('Requesting serial port access...');
                
                const baudRate = parseInt(document.getElementById('baudRate').value);
                const dataBits = parseInt(document.getElementById('dataBits').value);
                const stopBits = parseInt(document.getElementById('stopBits').value);
                const parity = document.getElementById('parity').value;

                // Request port access
                port = await navigator.serial.requestPort();
                
                // Open the port
                await port.open({
                    baudRate: baudRate,
                    dataBits: dataBits,
                    stopBits: stopBits,
                    parity: parity
                });

                // Set up reader and writer
                const textDecoder = new TextDecoderStream();
                const readableStreamClosed = port.readable.pipeTo(textDecoder.writable);
                reader = textDecoder.readable.getReader();

                const textEncoder = new TextEncoderStream();
                const writableStreamClosed = textEncoder.readable.pipeTo(port.writable);
                writer = textEncoder.writable.getWriter();

                // Update UI
                document.getElementById('statusIndicator').classList.add('connected');
                document.getElementById('connectionStatus').textContent = 'Connected';
                document.getElementById('deviceInfo').textContent = `Connected to RFID Reader (${baudRate} baud)`;
                document.getElementById('connectionSection').classList.add('connected');
                
                document.getElementById('connectBtn').disabled = true;
                document.getElementById('disconnectBtn').disabled = false;
                document.getElementById('scanBtn').disabled = false;
                document.getElementById('inventoryBtn').disabled = false;

                addLog(`Connected to RFID reader at ${baudRate} baud`, 'success');

                // Start reading data
                readData();

                // Send initialization commands
                await sendCommand('VERSION');
                await sendCommand('POWER?');
                
            } catch (error) {
                addLog(`Connection failed: ${error.message}`, 'error');
            }
        }

        async function disconnectDevice() {
            try {
                if (isScanning) {
                    await stopScanning();
                }

                if (reader) {
                    await reader.cancel();
                    reader = null;
                }

                if (writer) {
                    await writer.close();
                    writer = null;
                }

                if (port) {
                    await port.close();
                    port = null;
                }

                // Update UI
                document.getElementById('statusIndicator').classList.remove('connected');
                document.getElementById('connectionStatus').textContent = 'Disconnected';
                document.getElementById('deviceInfo').textContent = 'No RFID device connected';
                document.getElementById('connectionSection').classList.remove('connected');
                
                document.getElementById('connectBtn').disabled = false;
                document.getElementById('disconnectBtn').disabled = true;
                document.getElementById('scanBtn').disabled = true;
                document.getElementById('stopBtn').disabled = true;
                document.getElementById('inventoryBtn').disabled = true;

                addLog('Disconnected from RFID reader', 'warning');

            } catch (error) {
                addLog(`Disconnect error: ${error.message}`, 'error');
            }
        }

        async function readData() {
            try {
                while (port && port.readable) {
                    const { value, done } = await reader.read();
                    if (done) break;

                    const lines = value.split('\n');
                    for (let line of lines) {
                        line = line.trim();
                        if (line) {
                            addLog(`Received: ${line}`);
                            parseRFIDData(line);
                        }
                    }
                }
            } catch (error) {
                addLog(`Read error: ${error.message}`, 'error');
            }
        }

        async function sendCommand(command) {
            if (!writer) {
                addLog('No connection available', 'error');
                return;
            }

            try {
                await writer.write(command + '\r\n');
                addLog(`Sent: ${command}`);
            } catch (error) {
                addLog(`Send error: ${error.message}`, 'error');
            }
        }

        function parseRFIDData(data) {
            // Parse different RFID reader response formats
            
            // EPC tag data format: typically contains EPC, TID, RSSI
            if (data.includes('EPC:') || data.includes('epc:')) {
                const epcMatch = data.match(/EPC:?\s*([A-Fa-f0-9]+)/i);
                if (epcMatch) {
                    displayTagData({
                        epc: epcMatch[1],
                        rssi: extractRSSI(data),
                        tid: extractTID(data),
                        frequency: extractFrequency(data),
                        timestamp: new Date().toLocaleString()
                    });
                }
            }
            
            // Handle inventory responses
            else if (data.includes('TAG') || data.includes('ID:')) {
                const idMatch = data.match(/ID:?\s*([A-Fa-f0-9]+)/i);
                if (idMatch) {
                    displayTagData({
                        epc: idMatch[1],
                        rssi: extractRSSI(data),
                        timestamp: new Date().toLocaleString()
                    });
                }
            }
            
            // Handle hex data (raw tag responses)
            else if (/^[A-Fa-f0-9\s]{8,}$/.test(data)) {
                displayTagData({
                    epc: data.replace(/\s/g, ''),
                    timestamp: new Date().toLocaleString()
                });
            }
        }

        function extractRSSI(data) {
            const rssiMatch = data.match(/RSSI:?\s*(-?\d+)/i);
            return rssiMatch ? rssiMatch[1] + ' dBm' : 'N/A';
        }

        function extractTID(data) {
            const tidMatch = data.match(/TID:?\s*([A-Fa-f0-9]+)/i);
            return tidMatch ? tidMatch[1] : 'N/A';
        }

        function extractFrequency(data) {
            const freqMatch = data.match(/FREQ:?\s*(\d+\.?\d*)/i);
            return freqMatch ? freqMatch[1] + ' MHz' : 'N/A';
        }

        function displayTagData(tagData) {
            document.getElementById('tagEPC').textContent = tagData.epc || 'N/A';
            document.getElementById('tagTID').textContent = tagData.tid || 'N/A';
            document.getElementById('tagRSSI').textContent = tagData.rssi || 'N/A';
            document.getElementById('frequency').textContent = tagData.frequency || 'N/A';
            document.getElementById('timestamp').textContent = tagData.timestamp;
            
            // Update read count for this tag
            const existingTag = tagHistory.find(t => t.epc === tagData.epc);
            if (existingTag) {
                existingTag.count++;
                existingTag.lastSeen = tagData.timestamp;
            } else {
                tagData.count = 1;
                tagHistory.unshift(tagData);
            }
            
            document.getElementById('readCount').textContent = existingTag ? existingTag.count : 1;
            document.getElementById('rfidData').style.display = 'block';
            
            updateTagHistory();
            addLog(`Tag detected: ${tagData.epc}`, 'success');
        }

        function updateTagHistory() {
            const historyDiv = document.getElementById('tagHistory');
            historyDiv.innerHTML = '';
            
            tagHistory.slice(0, 10).forEach(tag => {
                const tagItem = document.createElement('div');
                tagItem.className = 'tag-item';
                tagItem.innerHTML = `
                    <span class="tag-id">${tag.epc}</span>
                    <span>Count: ${tag.count}</span>
                    <span class="tag-time">${tag.lastSeen || tag.timestamp}</span>
                `;
                historyDiv.appendChild(tagItem);
            });
        }

        async function startScanning() {
            if (!port) {
                addLog('No device connected', 'error');
                return;
            }

            isScanning = true;
            document.getElementById('scanBtn').disabled = true;
            document.getElementById('stopBtn').disabled = false;
            
            addLog('Starting continuous scan...', 'success');
            await sendCommand('START');
        }

        async function stopScanning() {
            if (!port) return;

            isScanning = false;
            document.getElementById('scanBtn').disabled = false;
            document.getElementById('stopBtn').disabled = true;
            
            addLog('Stopping scan...', 'warning');
            await sendCommand('STOP');
        }

        function clearHistory() {
            tagHistory = [];
            updateTagHistory();
            addLog('Tag history cleared');
        }

        function exportHistory() {
            if (tagHistory.length === 0) {
                addLog('No history to export', 'warning');
                return;
            }

            const csv = 'EPC,TID,RSSI,Frequency,Count,Timestamp\n' + 
                       tagHistory.map(tag => 
                           `${tag.epc},${tag.tid || ''},${tag.rssi || ''},${tag.frequency || ''},${tag.count},${tag.timestamp}`
                       ).join('\n');

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `rfid_tags_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            URL.revokeObjectURL(url);
            
            addLog('History exported to CSV', 'success');
        }

        function clearLogs() {
            document.getElementById('logContainer').innerHTML = '';
            addLog('Log cleared');
        }

        // Initialize
        addLog('RFID Hardware Reader initialized');
        addLog('Click "Connect to RFID Reader" to start');
    </script>
</body>
</html>