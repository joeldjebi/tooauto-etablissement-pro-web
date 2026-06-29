<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu d'abonnement - {{ $entreprise['nom'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .company-details {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        .receipt-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .info-section {
            flex: 1;
            min-width: 250px;
            margin: 10px;
        }
        .info-section h3 {
            color: #007bff;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-item {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .subscription-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .subscription-details h3 {
            color: #007bff;
            margin-bottom: 15px;
            text-align: center;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            color: #007bff;
        }
        .detail-label {
            font-weight: 500;
        }
        .detail-value {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 12px;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,123,255,0.3);
            transition: all 0.3s ease;
        }
        .print-button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .print-button:active {
            transform: translateY(0);
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
            }
            .print-button {
                display: none;
            }
        }
        @media (max-width: 768px) {
            .receipt-info {
                flex-direction: column;
            }
            .info-section {
                min-width: 100%;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        🖨️ Imprimer le reçu
    </button>

    <div class="receipt-container">
        <!-- En-tête de l'entreprise -->
        <div class="header">
            @if($entreprise['logo'] && file_exists(public_path($entreprise['logo'])))
                <img src="{{ asset($entreprise['logo']) }}" alt="Logo" class="logo">
            @elseif($entreprise['logo'])
                <img src="{{ asset($entreprise['logo']) }}" alt="Logo" class="logo" onerror="this.style.display='none'">
            @endif
            <div class="company-name">{{ $entreprise['nom'] }}</div>
            <div class="company-details">
                {{ $entreprise['adresse'] }}<br>
                Tél: {{ $entreprise['telephone'] }}<br>
                Email: {{ $entreprise['email'] }}
            </div>
        </div>

        <!-- Titre du reçu -->
        <div class="receipt-title">Reçu d'abonnement</div>

        <!-- Informations du reçu -->
        <div class="receipt-info">
            <div class="info-section">
                <h3>Informations de l'abonnement</h3>
                <div class="info-item">
                    <span class="info-label">N° Abonnement:</span>
                    <span class="info-value">#{{ $abonnement->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date d'émission:</span>
                    <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Période:</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}
                    </span>
                </div>
            </div>

            <div class="info-section">
                <h3>Informations du client</h3>
                <div class="info-item">
                    <span class="info-label">Établissement:</span>
                    <span class="info-value">{{ $abonnement->etablissement->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Adresse:</span>
                    <span class="info-value">{{ $abonnement->etablissement->adresse ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Téléphone:</span>
                    <span class="info-value">{{ $abonnement->etablissement->mobile ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Détails de l'abonnement -->
        <div class="subscription-details">
            <h3>Détails de l'abonnement</h3>
            <div class="detail-row">
                <span class="detail-label">Forfait souscrit:</span>
                <span class="detail-value">{{ $abonnement->forfait->nom ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ $abonnement->forfait->avantages ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Durée:</span>
                <span class="detail-value">{{ $abonnement->forfait->duree ?? 'N/A' }} jours</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date de début:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date de fin:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Montant total:</span>
                <span class="detail-value">{{ number_format($abonnement->forfait->prix ?? 0, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p><strong>Merci pour votre confiance !</strong></p>
            <p>Ce reçu fait foi de votre abonnement. Conservez-le précieusement.</p>
            <p>Pour toute question, contactez-nous au {{ $entreprise['telephone'] }} ou par email à {{ $entreprise['email'] }}</p>
            <p>Reçu généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // };

        // Handle print event
        window.addEventListener('beforeprint', function() {
            console.log('Impression en cours...');
        });

        window.addEventListener('afterprint', function() {
            console.log('Impression terminée');
        });
    </script>
</body>
</html>
