<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Račun {{ $bill->id }}/{{ $bill->year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 8px 20px 12px 20px;
        }
        .company-header {
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            border-collapse: collapse;
        }
        .header-logo {
            vertical-align: top;
            width: 120px;
            text-align: left;
            padding-right: 10px;
        }
        .header-text {
            vertical-align: top;
            text-align: right;
            font-size: 9px;
            line-height: 1.4;
        }
        .header-text p {
            margin: 0 0 2px 0;
        }
        .client-info {
            margin: 15px 0;
            font-size: 9px;
        }
        .bill-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }
        .bill-info {
            margin: 8px 0;
            font-size: 9px;
        }
        .contracts {
            margin: 15px 0;
            padding: 10px;
            border-bottom: 1px solid #000;
            font-size: 9px;
        }
        .amount-section {
            margin: 15px 0;
        }
        .amount-row {
            display: table;
            width: 100%;
            margin: 5px 0;
        }
        .amount-label {
            display: table-cell;
            width: 70%;
            text-align: left;
            padding: 5px 10px;
        }
        .amount-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            padding: 5px 10px;
        }
        .amount-total {
            border-top: 1px solid #000;
            font-weight: bold;
            font-size: 10px;
        }
        .signature {
            margin-top: 30px;
            font-size: 9px;
        }
        .specification-title {
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-align: center;
        }
        .specification-period {
            margin: 10px 0;
            text-align: center;
            font-size: 9px;
        }
        .specification-table {
            width: 100%;
            margin: 15px 0;
            font-size: 8px;
        }
        .specification-table-header {
            border-bottom: 1px solid #000;
            padding: 5px 2px;
            font-weight: bold;
            text-align: left;
        }
        .specification-table-row {
            padding: 5px 2px;
        }
        .specification-table-total {
            border-top: 1px solid #000;
            padding: 5px 2px;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    @php($pdfLogoPath = public_path('images/pdf/logo.jpg'))
    <!-- Page 1: Bill -->
    <table class="company-header">
        <tr>
        <td class="header-logo">
            <img src="{{ $pdfLogoPath }}" alt="Logo">
        </td>
        <td class="header-text">
            <p>Matevž Korenjak s.p., Litostrojska cesta 12, 1000 Ljubljana</p>
            <p>Email: matevz.korenjak@kompenzacije.eu</p>
            <p>Telefon: 031 227 139, Faks: 08 288 00 77</p>
            <p>SI98789309</p>
        </td>
        </tr>
    </table>

    <div class="client-info">
        @if($bill->entity)
            <div><strong>{{ $bill->entity->company_name }}</strong></div>
            @if($bill->entity->address)
                <div>{{ $bill->entity->address }}</div>
            @endif
            @if($bill->entity->post_num && $bill->entity->post_town)
                <div>{{ $bill->entity->post_num }} {{ $bill->entity->post_town }}</div>
            @endif
        @endif
    </div>

    <div class="bill-title">RAČUN ŠT {{ $bill->id }}/{{ $bill->year }}</div>

    <div class="bill-info">
        <div>Datum izdaje: {{ \Carbon\Carbon::parse($bill->date)->format('d.m.Y') }}</div>
        <div>Dat. Opr. Storitve: {{ \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d.m.Y') }}</div>
        <div>Valuta: POBOTANO</div>
        @if($bill->entity && $bill->entity->vat_num)
            <div>ID za DDV kupca: {{ $bill->entity->vat_num }}</div>
        @endif
    </div>

    <div class="contracts">
        <strong>Račun po pogodba št.</strong>
        @php
            $contractNumbers = [];
            $currentYear = \Carbon\Carbon::now()->format('Y');
            foreach($bill->compenzations as $compenzation) {
                $contractNumbers[] = 'U' . $compenzation->id . '/' . $currentYear;
            }
        @endphp
        <strong>{{ implode(', ', $contractNumbers) }}</strong>
    </div>

    @php
        $commissionAmount = 0;
        $commissionDdvAmount = 0;
        foreach($bill->compenzations as $compenzation) {
            if($compenzation->realizationAgreement) {
                $commissionAmount += (float)($compenzation->realizationAgreement->commission_amount ?? 0);
                $commissionDdvAmount += (float)($compenzation->realizationAgreement->commission_ddv_amount ?? 0);
            }
        }
        $transferAmount = $commissionAmount + $commissionDdvAmount;
    @endphp

    <div class="amount-section">
        <div class="amount-row">
            <div class="amount-label">Osnova za DDV</div>
            <div class="amount-value">{{ number_format($commissionAmount, 2, ',', '.') }} €</div>
        </div>
        <div class="amount-row">
            <div class="amount-label">DDV 22%</div>
            <div class="amount-value">{{ number_format($commissionDdvAmount, 2, ',', '.') }} €</div>
        </div>
        <div class="amount-row amount-total">
            <div class="amount-label"><strong>SKUPAJ ZA PLAČILO</strong></div>
            <div class="amount-value"><strong>{{ number_format($transferAmount, 2, ',', '.') }} €</strong></div>
        </div>
    </div>

    <div class="signature">
        <div>V Ljubljani, {{ \Carbon\Carbon::parse($bill->date)->format('d.m.Y') }}</div>
        <div style="margin-top: 20px; text-align: right;">
            <div>Direktor:</div>
            <div>Matevž Korenjak</div>
        </div>
    </div>

    <!-- Page 2: Specification -->
    <div style="page-break-before: always;">
        <table class="company-header">
            <tr>
            <td class="header-logo">
                <img src="{{ $pdfLogoPath }}" alt="Logo">
            </td>
            <td class="header-text">
                <div>Matevž Korenjak s.p., Litostrojska cesta 12, 1000 Ljubljana</div>
                <div>Email: matevz.korenjak@kompenzacije.eu</div>
                <div>Telefon: 031 227 139, Faks: 08 288 00 77</div>
                <div>SI98789309</div>
            </td>
            </tr>
        </table>

        <div class="specification-title">SPECIFIKACIJA UNOVČENIH TERJATEV</div>
        <div class="specification-period">V obdobju {{ \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d.m.Y') }}</div>

        <table class="specification-table">
            <thead>
                <tr>
                    <th class="specification-table-header" style="width: 15%;">DATUM NAK.</th>
                    <th class="specification-table-header" style="width: 15%;">ŠT. POGODBE</th>
                    <th class="specification-table-header text-right" style="width: 15%;">ZNESEK KOM.</th>
                    <th class="specification-table-header text-right" style="width: 8%;">%</th>
                    <th class="specification-table-header text-right" style="width: 15%;">ZNESEK PROV.</th>
                    <th class="specification-table-header text-right" style="width: 12%;">DDV</th>
                    <th class="specification-table-header text-right" style="width: 20%;">ZNESEK NAKAZILA</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount = 0;
                    $totalCommissionAmount = 0;
                    $totalCommissionDdvAmount = 0;
                    $totalTransferAmount = 0;
                    $currentYear = \Carbon\Carbon::now()->format('Y');
                @endphp
                @foreach($bill->compenzations as $compenzation)
                    @php
                        $ra = $compenzation->realizationAgreement;
                        $compenzationAmount = (float)$compenzation->amount;
                        $commissionAmount = (float)($ra->commission_amount ?? 0);
                        $commissionDdvAmount = (float)($ra->commission_ddv_amount ?? 0);
                        $transferAmount = $compenzationAmount - $commissionAmount - $commissionDdvAmount;
                        $commissionPercent = $ra->commission ?? 0;

                        $totalAmount += $compenzationAmount;
                        $totalCommissionAmount += $commissionAmount;
                        $totalCommissionDdvAmount += $commissionDdvAmount;
                        $totalTransferAmount += $transferAmount;
                    @endphp
                    <tr>
                        <td class="specification-table-row">{{ \Carbon\Carbon::parse($compenzation->date_payed)->format('d.m.Y') }}</td>
                        <td class="specification-table-row">U{{ $compenzation->id }}/{{ $currentYear }}</td>
                        <td class="specification-table-row text-right">{{ number_format($compenzationAmount, 2, ',', '.') }}</td>
                        <td class="specification-table-row text-right">{{ number_format($commissionPercent, 2, ',', '.') }}</td>
                        <td class="specification-table-row text-right">{{ number_format($commissionAmount, 2, ',', '.') }}</td>
                        <td class="specification-table-row text-right">{{ number_format($commissionDdvAmount, 2, ',', '.') }}</td>
                        <td class="specification-table-row text-right">{{ number_format($transferAmount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="specification-table-total" colspan="2"><strong>SKUPAJ</strong></td>
                    <td class="specification-table-total text-right"><strong>{{ number_format($totalAmount, 2, ',', '.') }}</strong></td>
                    <td class="specification-table-total"></td>
                    <td class="specification-table-total text-right"><strong>{{ number_format($totalCommissionAmount, 2, ',', '.') }}</strong></td>
                    <td class="specification-table-total text-right"><strong>{{ number_format($totalCommissionDdvAmount, 2, ',', '.') }}</strong></td>
                    <td class="specification-table-total text-right"><strong>{{ number_format($totalTransferAmount, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>

