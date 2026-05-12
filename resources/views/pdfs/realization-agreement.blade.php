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
            padding: 4px 20px 20px 20px;
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
            font-weight: bold;
            font-size: 11px;
            margin: 12px 0;
        }
        .article {
            margin: 8px 0;
            line-height: 1.5;
            text-align: left;
        }
        .article p {
            margin: 3px 0;
        }
        .article-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .sig-left {
            text-align: left;
        }
        .sig-right {
            text-align: right;
        }
        .sig-label {
            font-weight: bold;
        }
        .sig-space {
            height: 50px;
        }
        .stamp img {
            max-width: 75px;
            height: auto;
        }
        .date-location {
            margin: 12px 0;
            text-align: left;
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
            MATEVŽ KORENJAK S.P., Litostrojska cesta 12, 1000 Ljubljana<br>
            ID DDV:SI98789309 (v nadaljevanju <strong>prevzemnik</strong>)
        </div>
        <div class="party-separator">in</div>
        @if($lastEntity)
        <div class="party-info">
            {{ strtoupper($lastEntity->company_name) }} {{ $lastEntity->address }}, {{ $lastEntity->post_num }} {{ $lastEntity->post_town }}<br>
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
                <img src="{{ public_path('images/pdf/zig.jpg') }}" alt="Žig" style="max-width:75px; height:auto; margin-top:6px;">
            </td>
        </tr>
    </table>
</body>
</html>
