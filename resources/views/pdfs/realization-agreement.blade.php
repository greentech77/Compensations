<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pogodba o unovčenju - U{{ $compenzation->id }}/{{ $compenzation->year }}</title>
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
            width: 120px;
            vertical-align: top;
            text-align: left;
            padding-right: 10px;
            padding-top: 0;
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
        .document-number {
            text-align: left;
            margin: 20px 0 15px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .document-date {
            text-align: right;
            margin-bottom: 25px;
            font-size: 11px;
        }
        .main-title {
            text-align: left;
            margin: 25px 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .intro-text {
            margin: 20px 0;
            text-align: justify;
            line-height: 1.8;
        }
        .entities-list {
            margin: 30px 0;
        }
        .entity-item {
            margin: 5px 0;
            line-height: 1.8;
        }
        .entity-row {
            display: table;
            width: 100%;
            margin-bottom: 0;
            table-layout: fixed;
        }
        .entity-address {
            display: table-cell;
            text-align: left;
            vertical-align: baseline;
            width: auto;
            word-wrap: break-word;
        }
        .entity-email {
            display: table-cell;
            text-align: right;
            vertical-align: baseline;
            padding-left: 0px;
            white-space: nowrap;
            width: 1%;
        }
        .entity-number {
            font-weight: bold;
            display: inline-block;
            margin-right: 5px;
        }
        .entity-debt {
            margin: 0;
            margin-top: 2px;
            font-style: italic;
            text-align: left;
        }
        .stamp {
            text-align: right;
            margin-top: 5px;
        }
        .stamp img {
            max-width: 150px;
            height: auto;
        }
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        .declaration-text {
            margin: 30px 0;
            text-align: justify;
            line-height: 1.8;
            font-size: 11px;
        }
    </style>
</head>
<body>
    @php
        $pdfLogoPath = public_path('images/pdf/logo.jpg');
        $lastEntity = optional($compenzation->compenzationEntity->last())->entity;
        $amount = (float)$compenzation->amount;
        $commission = $amount * (((float)($agreement->commission ?? 8)) / 100);
        $vatRate = 0.22;
        $vatAmount = $commission * $vatRate;
        $totalCommission = $commission + $vatAmount;
        $transferAmount = $amount - $totalCommission;
        $accountNumber = $lastEntity ? ($lastEntity->bank_account ?? '06382 0133110880') : '06382 0133110880';
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
            <strong>MATEVŽ KORENJAK S.P.</strong>, Litostrojska cesta 12, 1000 Ljubljana<br>
            ID DDV:SI98789309 (v nadaljevanju <strong>prevzemnik</strong>)
        </div>
        <div class="party-separator">in</div>
        @if($lastEntity)
        <div class="party-info">
            <strong>{{ strtoupper($lastEntity->company_name) }}</strong> {{ $lastEntity->address }}, {{ $lastEntity->post_num }} {{ $lastEntity->post_town }}<br>
            ID DDV:{{ $lastEntity->vat_num }} (v nadaljevanju <strong>odstopnik</strong>)
        </div>
        @endif
    </div>

    <div class="contract-title">
        skleneta naslednjo: <strong>POGODBO O UNOVČENJU TERJATEV ŠT. U{{ $compenzation->id }}/ {{ $compenzation->year }}</strong>
    </div>

    <div class="article">
        <div class="article-title">1. člen</div>
        <p>Predmet te pogodbe so nesporne, neizpodbitne in zapadle terjatve v vrednosti <strong>{{ number_format((float)$compenzation->amount, 2, ',', '.') }} €</strong>, katere ima odstopnik do svojih dolžnikov.</p>
    </div>

    <div class="article">
        <div class="article-title">2. člen</div>
        <p>Z namenom unovčenja se odstopnik zavezuje te terjatve prodati pod pogoji, določenim s pogodbo Prevzemnik se obvezuje, v skladu z določili te pogodbe, terjatve kupiti, jih unovčiti ter poravnati do odstopnika vse iz tega razmerja nastale obveznosti.</p>
    </div>

    <div class="article">
        <div class="article-title">3. člen</div>
        <p>S podpisom pogodbe nastane na strani odstopnika zapadla obveznost v znesku dolocenem v 1.členu, katero bo poravnal z izročitvijo terjatev. Izročitev terjatev se izvrši z enim ali vec poboti, katere predlaga prevzemnik. Odstopnik se obvezuje predlog vsakega pobota takoj potrditi, ter pridobiti tudi potrditev svojega dolžnika. Pobotna izjava je sestavni del pogodbe.</p>
    </div>

    <div class="article">
        <div class="article-title">4. člen</div>
        <p>Kot plačilo za opravljeno storitev unovčenja terjatve, zaračuna prevzemnik odstopniku <strong>{{ (float)($agreement->commission ?? 8) }} %</strong> provizijo obračunano od vrednosti obračunano od vrednosti iz 1. člena te pogodbe. To znaša <strong>{{ number_format($commission, 2, ',', '.') }} €</strong>. Na to vrednost se obračuna 22% DDV v znesku <strong>{{ number_format($vatAmount, 2, ',', '.') }} €</strong>. Unovčenje terjatve se praviloma izvaja postopno. Storitev in DDV se zato obračunavata v skladu s 33.členom pravilnika o izvajanju ZDDV. Kot obračunsko obdobje šteje koledarski mesec, prevzemnik pa izstavlja račune in obračunava DDV najkasneje zadnji dan v mesecu.</p>
    </div>

    <div class="article">
        <div class="article-title">5. člen</div>
        <p>Prevzemnik bo obveznost plačila terjatev, ki nastane s potrditvijo vseh udeležencev predlaganega pobota poravnal z nakazilom sredstev na račun odstopnika. Znesek kupnine v višini <strong>{{ number_format($transferAmount, 2, ',', '.') }} €</strong>, ki je zmanjšan za v 4.členu dogovorjeno provizijo in za DDV, bo prevzemnik nakazoval po izvršenih pobotih na TRR: <strong>{{ $accountNumber }}</strong></p>
    </div>

    <div class="article">
        <div class="article-title">6. člen</div>
        <p>Pogodbeni stranki soglašata, da bosta morebitne spore reševala mirno, v nasprotnem primeru je za to pristojno sodišče v Ljubljani.</p>
    </div>

    <div class="article">
        <div class="article-title">7. člen</div>
        <p>Pogodba stopi v veljavo z dnem podpisa obeh pogodbenih strank. Napisana je v dveh izvodih. Vsaka pogodbena stranka prejme en izvod.</p>
    </div>

    <div class="date-location">
        V Ljubljani, {{ \Carbon\Carbon::parse($compenzation->date)->format('d.m.Y') }}
    </div>

    <table class="signature-table">
        <tr>
            <td class="sig-left">
                <span class="sig-label">ODSTOPNIK:</span>
                <div class="sig-space"></div>
            </td>
            <td class="sig-right">
                <span class="sig-label">PREVZEMNIK:</span><br>
                <img src="{{ public_path('images/pdf/zig.jpg') }}" alt="Žig" style="max-width:150px; height:auto; margin-top:6px;">
            </td>
        </tr>
    </table>
</body>
</html>
