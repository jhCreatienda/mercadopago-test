<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Mercado Pago - Checkout Bricks</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        @media (max-width: 968px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 2px solid #009ee3;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #009ee3;
        }
        button {
            background: #009ee3;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        button:hover {
            background: #0084c2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 158, 227, 0.3);
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .log {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .log h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .log-content {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }
        .log-content pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .log-entry {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
        }
        .log-info {
            color: #4fc3f7;
        }
        .log-success {
            color: #66bb6a;
        }
        .log-error {
            color: #ef5350;
        }
        .log-warning {
            color: #ffa726;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        .status-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-pending {
            background: #fff3e0;
            color: #f57c00;
        }
        .status-error {
            background: #ffebee;
            color: #c62828;
        }
        #cardPaymentBrick_container {
            margin-top: 20px;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
        .info-box p {
            color: #1565c0;
            font-size: 14px;
            margin: 0;
        }
        .customer-info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
        .customer-info.active {
            display: block;
        }
        .customer-info strong {
            color: #333;
        }
        .step {
            opacity: 0.5;
            pointer-events: none;
        }
        .step.active {
            opacity: 1;
            pointer-events: all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Test Mercado Pago - Checkout Bricks</h1>
            <p>Prueba la creación de customers, guardado de tarjetas y procesamiento de pagos</p>
        </div>

        <div class="grid">
            <!-- PASO 1: Crear Customer -->
            <div class="card step active" id="step1">
                <h2>Paso 1: Crear Customer</h2>
                <div class="info-box">
                    <p>Primero creamos un customer en Mercado Pago con los datos del usuario</p>
                </div>

                <form id="customerForm">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="email" name="email" required value="test@example.com">
                    </div>
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" id="first_name" name="first_name" required value="Juan">
                    </div>
                    <div class="form-group">
                        <label>Apellido *</label>
                        <input type="text" id="last_name" name="last_name" required value="Pérez">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" id="phone" name="phone" value="3001234567">
                    </div>
                    <div class="form-group">
                        <label>Tipo de Documento</label>
                        <select id="identification_type" name="identification_type">
                            <option value="">Seleccionar</option>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="CE">Cédula de Extranjería</option>
                            <option value="NIT">NIT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Número de Documento</label>
                        <input type="text" id="identification_number" name="identification_number">
                    </div>
                    <button type="submit" id="btnCreateCustomer">Crear Customer</button>
                </form>

                <div class="customer-info" id="customerInfo">
                    <strong>Customer ID:</strong> <span id="customerId"></span>
                    <div class="status-badge status-success">✓ Customer Creado</div>
                </div>
            </div>

            <!-- PASO 2: Guardar Tarjeta -->
            <div class="card step" id="step2">
                <h2>Paso 2: Guardar Tarjeta</h2>
                <div class="info-box">
                    <p>Usa el Checkout Brick para capturar los datos de la tarjeta de forma segura</p>
                </div>

                <div id="cardPaymentBrick_container"></div>
                <button id="btnSaveCard" style="margin-top: 20px;">Guardar Tarjeta</button>

                <div class="customer-info" id="cardInfo">
                    <strong>Card ID:</strong> <span id="cardId"></span><br>
                    <strong>Últimos 4 dígitos:</strong> <span id="lastFourDigits"></span><br>
                    <strong>Método de pago:</strong> <span id="paymentMethod"></span>
                    <div class="status-badge status-success">✓ Tarjeta Guardada</div>
                </div>
            </div>

            <!-- PASO 3: Procesar Pago -->
            <div class="card step" id="step3">
                <h2>Paso 3: Procesar Pago con Tarjeta Guardada</h2>
                <div class="info-box">
                    <p>Cobra usando la tarjeta previamente guardada</p>
                </div>

                <form id="paymentForm">
                    <div class="form-group">
                        <label>Monto a cobrar *</label>
                        <input type="number" id="amount" name="amount" step="0.01" required value="100.00">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" id="description" name="description" value="Pago de suscripción mensual">
                    </div>
                    <button type="submit" id="btnProcessPayment">Procesar Pago</button>
                </form>

                <div class="customer-info" id="paymentInfo">
                    <strong>Payment ID:</strong> <span id="paymentId"></span><br>
                    <strong>Estado:</strong> <span id="paymentStatus"></span><br>
                    <strong>Monto:</strong> $<span id="paymentAmount"></span>
                    <div class="status-badge status-success" id="paymentBadge">✓ Pago Procesado</div>
                </div>
            </div>

            <!-- LOG -->
            <div class="card step active">
                <h2>📋 Log de Depuración</h2>
                <div class="log-content" id="logContent"></div>
            </div>
        </div>
    </div>

    <script>
        const mp = new MercadoPago('{{ config("services.mercadopago.public_key") }}');
        let customerId = null;
        let cardToken = null;
        let savedCardId = null;
        let bricksBuilder = null;
        let cardPaymentBrickController = null;

        function log(message, type = 'info') {
            const logContent = document.getElementById('logContent');
            const timestamp = new Date().toLocaleTimeString();
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;
            logContent.appendChild(entry);
            logContent.scrollTop = logContent.scrollHeight;
        }

        function logError(data) {
            log(`❌ ERROR: ${data.error}`, 'error');

            if (data.error_detail) {
                log(`📄 Archivo: ${data.error_detail.file}`, 'error');
                log(`📍 Línea: ${data.error_detail.line}`, 'error');
                log(`🔢 Código: ${data.error_detail.code}`, 'error');

                if (data.error_detail.trace && data.error_detail.trace.length > 0) {
                    log('📚 Stack Trace:', 'warning');
                    data.error_detail.trace.forEach((line, index) => {
                        if (line.trim()) {
                            log(`  ${index + 1}. ${line.trim()}`, 'warning');
                        }
                    });
                }
            }

            // Mostrar el objeto completo en consola para debugging adicional
            console.error('Error completo:', data);
        }

        function activateStep(stepNumber) {
            document.querySelectorAll('.step').forEach((step, index) => {
                if (step.id === `step${stepNumber}` || step.classList.contains('log')) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });
        }

        // PASO 1: Crear Customer
        document.getElementById('customerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnCreateCustomer');
            btn.disabled = true;
            btn.textContent = 'Creando...';

            log('🚀 Iniciando creación de customer...', 'info');

            try {
                const formData = {
                    email: document.getElementById('email').value,
                    first_name: document.getElementById('first_name').value,
                    last_name: document.getElementById('last_name').value,
                    phone: document.getElementById('phone').value,
                    identification_type: document.getElementById('identification_type').value,
                    identification_number: document.getElementById('identification_number').value
                };

                log(`📧 Email: ${formData.email}`, 'info');
                log(`👤 Nombre: ${formData.first_name} ${formData.last_name}`, 'info');

                const response = await fetch('/mercadopago/create-customer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    customerId = data.customer_id;
                    log(`✅ Customer creado exitosamente`, 'success');
                    log(`🆔 Customer ID: ${customerId}`, 'success');

                    document.getElementById('customerId').textContent = customerId;
                    document.getElementById('customerInfo').classList.add('active');

                    // Activar paso 2 e inicializar el brick
                    activateStep(2);
                    initializeCardBrick();
                } else {
                    log(`❌ Error al crear customer: ${data.error}`, 'error');
                }
            } catch (error) {
                log(`❌ Error de conexión: ${error.message}`, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Crear Customer';
            }
        });

        // Inicializar Card Payment Brick
        async function initializeCardBrick() {
            log('🎨 Inicializando Checkout Brick...', 'info');

            try {
                bricksBuilder = mp.bricks();

                const settings = {
                    initialization: {
                        amount: 100.00,
                    },
                    customization: {
                        visual: {
                            style: {
                                theme: 'default'
                            }
                        },
                        paymentMethods: {
                            maxInstallments: 1
                        }
                    },
                    callbacks: {
                        onReady: () => {
                            log('✅ Checkout Brick cargado', 'success');
                        },
                        onSubmit: async (cardFormData) => {
                            return new Promise((resolve, reject) => {
                                cardToken = cardFormData.token;
                                log(`🎫 Token de tarjeta obtenido: ${cardToken}`, 'success');
                                resolve();
                            });
                        },
                        onError: (error) => {
                            log(`❌ Error en Brick: ${error.message}`, 'error');
                        }
                    }
                };

                cardPaymentBrickController = await bricksBuilder.create(
                    'cardPayment',
                    'cardPaymentBrick_container',
                    settings
                );

            } catch (error) {
                log(`❌ Error al inicializar Brick: ${error.message}`, 'error');
            }
        }

        // PASO 2: Guardar Tarjeta
        document.getElementById('btnSaveCard').addEventListener('click', async () => {
            const btn = document.getElementById('btnSaveCard');
            btn.disabled = true;
            btn.textContent = 'Guardando...';

            log('💳 Intentando guardar tarjeta...', 'info');

            try {
                // Obtener el token de la tarjeta
                const cardFormData = await cardPaymentBrickController.getFormData();

                if (!cardFormData.token) {
                    throw new Error('No se pudo obtener el token de la tarjeta');
                }

                log(`🎫 Token obtenido: ${cardFormData.token}`, 'info');

                const response = await fetch('/mercadopago/save-card', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_id: customerId,
                        token: cardFormData.token
                    })
                });

                const data = await response.json();

                if (data.success) {
                    savedCardId = data.card_id;
                    log(`✅ Tarjeta guardada exitosamente`, 'success');
                    log(`🆔 Card ID: ${savedCardId}`, 'success');
                    log(`🔢 Últimos 4 dígitos: ${data.last_four_digits}`, 'info');

                    document.getElementById('cardId').textContent = savedCardId;
                    document.getElementById('lastFourDigits').textContent = data.last_four_digits;
                    document.getElementById('paymentMethod').textContent = data.payment_method;
                    document.getElementById('cardInfo').classList.add('active');

                    // Activar paso 3
                    activateStep(3);
                } else {
                    log(`❌ Error al guardar tarjeta: ${data.error}`, 'error');
                }
            } catch (error) {
                log(`❌ Error: ${error.message}`, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Guardar Tarjeta';
            }
        });

        // PASO 3: Procesar Pago
        document.getElementById('paymentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnProcessPayment');
            btn.disabled = true;
            btn.textContent = 'Procesando...';

            log('💰 Procesando pago...', 'info');

            try {
                const amount = document.getElementById('amount').value;
                const description = document.getElementById('description').value;

                log(`💵 Monto: $${amount}`, 'info');
                log(`📝 Descripción: ${description}`, 'info');

                const response = await fetch('/mercadopago/process-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_id: customerId,
                        card_id: savedCardId,
                        amount: amount,
                        description: description
                    })
                });

                const data = await response.json();

                if (data.success) {
                    log(`✅ Pago procesado exitosamente`, 'success');
                    log(`🆔 Payment ID: ${data.payment_id}`, 'success');
                    log(`📊 Estado: ${data.status}`, 'info');

                    document.getElementById('paymentId').textContent = data.payment_id;
                    document.getElementById('paymentStatus').textContent = data.status;
                    document.getElementById('paymentAmount').textContent = data.amount;

                    const badge = document.getElementById('paymentBadge');
                    if (data.status === 'approved') {
                        badge.className = 'status-badge status-success';
                        badge.textContent = '✓ Pago Aprobado';
                    } else if (data.status === 'pending') {
                        badge.className = 'status-badge status-pending';
                        badge.textContent = '⏳ Pago Pendiente';
                    } else {
                        badge.className = 'status-badge status-error';
                        badge.textContent = '✗ Pago Rechazado';
                    }

                    document.getElementById('paymentInfo').classList.add('active');
                } else {
                    log(`❌ Error al procesar pago: ${data.error}`, 'error');
                }
            } catch (error) {
                log(`❌ Error: ${error.message}`, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Procesar Pago';
            }
        });

        // Log inicial
        log('🎬 Sistema iniciado. Completa el Paso 1 para comenzar.', 'info');
    </script>
</body>
</html>
