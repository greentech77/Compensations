<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Račun {{ $bill->id }}/{{ $bill->year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 4px 20px 12px 20px;
        }
        .company-header {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .header-logo {
            vertical-align: top;
            width: 100px;
            padding-top: 0;
            text-align: left;
            padding-right: 10px;
        }
        .header-logo img {
            max-width: 80px;
            height: auto;
            display: block;
            vertical-align: top;
        }
        .header-text {
            vertical-align: top;
            text-align: right;
            font-size: 11px;
            line-height: 1.4;
            padding-top: 0;
        }
        .header-text p {
            margin: 0 0 2px 0;
            font-size: 10px;
        }
        .header-line {
            border-bottom: 1px solid #000;
            margin-bottom: 8px;
            padding-bottom: 4px;
        }
        .client-info {
            margin: 10px 0;
        }
        .bill-title {
            font-size: 13px;
            font-weight: bold;
            margin: 10px 0 8px 0;
        }
        .bill-info {
            margin: 6px 0;
        }
        .contracts {
            margin: 8px 0;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        .amount-section {
            margin: 10px 0;
        }
        .amount-table {
            width: 100%;
            border-collapse: collapse;
        }
        .amount-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .amount-label {
            text-align: left;
            width: 70%;
        }
        .amount-value {
            text-align: right;
            width: 30%;
            white-space: nowrap;
        }
        .amount-total td {
            border-top: 1px solid #000;
            font-weight: bold;
            font-size: 12px;
            padding-top: 4px;
        }
        .signature {
            margin-top: 20px;
        }
        .signature-row {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-left {
            text-align: left;
            vertical-align: top;
            width: 50%;
        }
        .signature-right {
            text-align: right;
            vertical-align: top;
            width: 50%;
        }
        .specification-title {
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-align: center;
        }
        .specification-period {
            margin: 6px 0;
            text-align: center;
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
    @php
        $pdfLogoPath = public_path('images/pdf/logo.jpg');
    @endphp
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
    <div class="header-line"></div>

    <div class="client-info">
        @if($bill->entity)
            <div>{{ $bill->entity->company_name }}</div>
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
        @php
            $contractNumbers = [];
            $currentYear = \Carbon\Carbon::now()->format('Y');
            foreach($bill->compenzations as $compenzation) {
                $contractNumbers[] = 'U' . $compenzation->id . '/' . $currentYear;
            }
        @endphp
        Račun po pogodbah št. {{ implode(', ', $contractNumbers) }}
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
        <table class="amount-table">
            <tr>
                <td class="amount-label">Osnova za DDV</td>
                <td class="amount-value"><strong>{{ number_format($commissionAmount, 2, ',', '.') }} €</strong></td>
            </tr>
            <tr>
                <td class="amount-label">DDV 22%</td>
                <td class="amount-value"><strong>{{ number_format($commissionDdvAmount, 2, ',', '.') }} €</strong></td>
            </tr>
            <tr class="amount-total">
                <td class="amount-label"><strong>SKUPAJ ZA PLAČILO</strong></td>
                <td class="amount-value"><strong>{{ number_format($transferAmount, 2, ',', '.') }} €</strong></td>
            </tr>
        </table>
    </div>

    <div class="signature">
        <table class="signature-row">
            <tr>
                <td class="signature-left">V Ljubljani, {{ \Carbon\Carbon::parse($bill->date)->format('d.m.Y') }}</td>
                <td class="signature-right">Direktor: <br />Matevž Korenjak</td>
            </tr>
        </table>
    </div>

    <!-- Page 2: Specification -->
    <div style="page-break-before: always;">
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
        <div class="header-line"></div>

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

