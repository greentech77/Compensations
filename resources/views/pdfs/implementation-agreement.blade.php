<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pogodba o izvedbi - {{ $compenzation->id }}/{{ $compenzation->year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 8px 40px 20px 40px;
        }
        .company-header {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .header-logo {
            vertical-align: top;
            width: 120px;
            padding-top: 0;
            text-align: left;
            padding-right: 10px;
        }
        .header-logo img {
            max-width: 100px;
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
            margin-bottom: 10px;
            padding-bottom: 4px;
        }
        .parties-section {
            margin: 6px 0;
            line-height: 1.3;
            text-align: left;
        }
        .party-info {
            margin-bottom: 3px;
        }
        .party-separator {
            text-align: left;
            margin: 2px 0;
        }
        .contract-title {
            text-align: left;
            font-size: 11px;
            margin: 8px 0;
        }
        .findings-section {
            margin: 6px 0;
            line-height: 1.3;
            text-align: left;
        }
        .findings-section p {
            margin: 1px 0;
        }
        .article {
            margin: 8px 0;
            line-height: 1.5;
            text-align: left;
        }
        .article-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
        }
        .calculation-item {
            display: table;
            width: 100%;
            margin: 5px 0;
        }
        .calculation-label {
            display: table-cell;
            text-align: left;
        }
        .calculation-value {
            display: table-cell;
            text-align: right;
            padding-left: 20px;
            white-space: nowrap;
        }
        .calculation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .calculation-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .calculation-table td:first-child {
            width: 70%;
        }
        .calculation-table td:last-child {
            text-align: right;
            width: 30%;
        }
        .signature-section {
            margin-top: 8px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-left {
            width: 50%;
            vertical-align: top;
            text-align: left;
        }
        .signature-right {
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .stamp {
            margin-top: 2px;
            text-align: right;
        }
        .stamp img {
            max-width: 70px;
            height: auto;
        }
        .signature-line {
            margin-top: 0;
        }
        .amount-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0;
        }
        .amount-table td {
            padding: 0;
            vertical-align: top;
        }
        .amount-table .amount-label {
            text-align: left;
            width: 75%;
        }
        .amount-table .amount-value {
            text-align: right;
            width: 25%;
            white-space: nowrap;
            padding-left: 12px;
        }
        .date-location {
            margin: 6px 0 3px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    @php
        $pdfLogoPath = public_path('images/pdf/logo.jpg');
        $firstEntityRelation = $compenzation->compenzationEntity->firstWhere('num', 1) ?? $compenzation->compenzationEntity->first();
        $firstEntity = optional($firstEntityRelation)->entity;
        $firstEntityName = $firstEntity ? strtoupper($firstEntity->company_name) : 'PRVA STRANKA';
        $firstEntityAddress = $firstEntity ? trim(($firstEntity->address ?? '').', '.($firstEntity->post_num ?? '').' '.($firstEntity->post_town ?? '')) : '';
        $firstEntityVat = $firstEntity->vat_num ?? '';
    @endphp
    {{-- Company Header with Logo --}}
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

    <div class="parties-section">
        <div class="party-info">
            {{ $firstEntityName }}@if($firstEntityAddress) {{ $firstEntityAddress }}@endif<br>
            ID DDV:{{ $firstEntityVat }} (v nadaljevanju <strong>prva stranka</strong>)
        </div>
        <div class="party-separator">in</div>
        <div class="party-info">
            MATEVŽ KORENJAK S.P., Litostrojska cesta 12, 1000 Ljubljana<br>
            ID DDV:SI98789309 ( v nadaljevanju <strong>druga stranka</strong>)
        </div>
    </div>

    <div class="contract-title">
        skleneta naslednjo: <strong>POGODBO O IZVEDBI VERIŽNE KOMPENZACIJE ŠT. {{ $compenzation->id }}/ {{ $compenzation->year }}</strong>
    </div>

    <div class="findings-section">
    <strong>Ugotovi se:</strong>
        <ul>
            @if($compenzation->compenzationEntity && $compenzation->compenzationEntity->count() > 1)
                @php
                    $secondEntity = $compenzation->compenzationEntity->skip(1)->first();
                @endphp
                @if($secondEntity && $secondEntity->entity)
                <li>da ima prva stranka obveznost do upnika {{ strtoupper($secondEntity->entity->company_name) }} {{ $secondEntity->entity->address }}, {{ $secondEntity->entity->post_num }} {{ $secondEntity->entity->post_town }} v višini <strong>{{ number_format((float)$compenzation->amount, 2, ',', '.') }} €</strong></li>
                @endif
            @endif
            <li>prva stranka ima interes, da poplača svoje obveznosti do upnika s popustom <strong>{{ $agreement->discount ?? '5' }} %</strong></li>
            <li>druga stranka je nosilec pravic in obveznosti po predlogu verižne kompenzacije, katere izvedba je predmet te pogodbe</li>
        </ul>
    </div>

    <div class="article">
        <div class="article-title">1. člen</div>
        <p>Druga stranka se obvezuje, da bo predlog verižne kompenzacije, ki je predmet te pogodbe sprejela v celoti in ga podpisala, ter predložila v podpis in potrditev drugim pravnim subjektom iz predmetne verižne kompenzacije. Potrjeni predlog verižne kompenzacije je sestavni del te pogodbe.</p>
    </div>

    <div class="article">
        <div class="article-title">2. člen</div>
        <p>Prva stranka se vsled izvedbe verižne kompenzacije obvezuje drugi stranki nakazati znesek verižne kompenzacije, zmanjšan za provizijo s pripadajočim DDV, na njen transakcijski račun : SI56 6100 0002 5604 758 najkasneje do ______________. Vendar le pod pogojem, da je predlog verižne kompenzacije v celoti potrjen iz strani vseh udeležencev.</p>
    </div>

    <div class="article">
        <div class="article-title">3. člen</div>
        <p>Druga stranka se obvezuje prvi stranki, da prevzema vsled nakazila iz 2.člena te pogodbe, obveznost do prve stranke v višini potrjene kompenzacije, ki pa bo predmet medsebojne kompenzacije iz te pogodbe.</p>
    </div>

    <div class="article">
        <div class="article-title">4. člen</div>
        <p>Stranki sta soglasni, da je ta pogodba v skladu z 2. in 3. odstavkom 136. člena Pravilnika o izvajanju ZDDV, v povezavi z 81. členom Pravilnika o izvajanju ZDDV-1 in se šteje kot račun.</p>
    </div>

    <div class="article">
        <div class="article-title">5. člen</div>
        @php
            $amount = (float)$compenzation->amount;
            $discount = (float)($agreement->discount ?? 5);
            $provisionWithVAT = $amount * ($discount / 100);
            $vatRate = 0.22;
            $netProvision = $provisionWithVAT / (1 + $vatRate);
            $vatAmount = $provisionWithVAT - $netProvision;
            $transferAmount = $amount - $provisionWithVAT;
        @endphp
        <p style="margin-bottom: 0;">Po pogodbi o izvedbi verižne kompenzacije prva stranka zaračuna drugi stranki provizijo v višini:</p>
        <table class="amount-table">
            <tr>
                <td class="amount-label"><strong>{{ $discount }}%</strong> z vključenim DDV od zneska realizirane verižne kompenzacije, v višini:</td>
                <td class="amount-value"><strong>{{ number_format($provisionWithVAT, 2, ',', '.') }} €</strong></td>
            </tr>
            <tr>
                <td class="amount-label">Od katerega je 22% DDV:</td>
                <td class="amount-value"><strong>{{ number_format($vatAmount, 2, ',', '.') }} €</strong></td>
            </tr>
            <tr>
                <td class="amount-label">Neto znesek brez DDV (davčna osnova):</td>
                <td class="amount-value"><strong>{{ number_format($netProvision, 2, ',', '.') }} €</strong></td>
            </tr>
            <tr>
                <td class="amount-label"><strong>Znesek nakazila je:</strong></td>
                <td class="amount-value"><strong>{{ number_format($transferAmount, 2, ',', '.') }} €</strong></td>
            </tr>
        </table>
        <p>Dogovorjena provizija z DDV se pri nakazilu enostransko pobota.</p>
    </div>

    <div class="article">
        <div class="article-title">6. člen</div>
        <p>Pogodbeni stranki soglašata, da bosta morebitne spore reševala mirno, v nasprotnem primeru je za to pristojno sodišče v Ljubljani.</p>
    </div>

    <div class="date-location">
        V Ljubljani, {{ \Carbon\Carbon::parse($compenzation->date)->format('d.m.Y') }}
    </div>

    <div class="signature-section">
        @if($firstEntity)
        <table class="signature-table">
            <tr>
                <td class="signature-left">
                    <div class="signature-line">
                        Prva stranka:<strong>{{ strtoupper($firstEntity->company_name) }}</strong>
                    </div>
                </td>
                <td class="signature-right">
                    <div class="signature-line">
                        Druga stranka: <strong>MATEVŽ KORENJAK S.P.</strong>
                    </div>
                    <div class="stamp">
                        <img src="{{ public_path('images/pdf/zig.jpg') }}" alt="Žig">
                    </div>
                </td>
            </tr>
        </table>
        @endif
    </div>
</body>
</html>
