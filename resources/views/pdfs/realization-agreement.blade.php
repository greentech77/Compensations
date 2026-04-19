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
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 30px 40px;
        }
        .company-header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 120px;
            padding-top: 0;
        }
        .header-logo img {
            max-width: 100px;
            height: auto;
            display: block;
            vertical-align: middle;
        }
        .header-text {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 11px;
            line-height: 1.4;
            padding-top: 0;
        }
        .header-text h2 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .header-text p {
            margin: 2px 0;
            font-size: 10px;
        }
        .header-line {
            border-bottom: 1px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .parties-section {
            margin: 30px 0;
            line-height: 1.8;
            text-align: left;
        }
        .party-info {
            margin-bottom: 10px;
        }
        .party-separator {
            text-align: left;
            margin: 10px 0;
        }
        .contract-title {
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            margin: 30px 0;
        }
        .article {
            margin: 20px 0;
            line-height: 1.8;
            text-align: left;
        }
        .article-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
        }
        .signature-left {
            display: table-cell;
            width: 50%;
            text-align: left;
            vertical-align: top;
        }
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
            padding-left: 20px;
        }
        .signature-line {
            margin-top: 40px;
        }
        .stamp {
            text-align: right;
            margin-top: 10px;
        }
        .stamp img {
            max-width: 150px;
            height: auto;
        }
        .date-location {
            margin: 30px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    {{-- Company Header with Logo --}}
    <div class="company-header">
        <div class="header-logo">
            <img src="{{ public_path('images/pdf/logo.jpg') }}" alt="Logo">
        </div>
        <div class="header-text">
            <h2>KORENJAK Finančno svetovanje</h2>
            <p>Matevž Korenjak s.p., Litostrojska cesta 12, 1000 Ljubljana</p>
            <p>Email: matevz.korenjak@kompenzacije.eu</p>
            <p>Telefon: 031 227 139, Faks: 08 288 00 77</p>
            <p><strong>SI98789309</strong></p>
        </div>
    </div>
    <div class="header-line"></div>

    <div class="parties-section">
        <div class="party-info">
            <strong>MATEVŽ KORENJAK S.P.</strong>, Litostrojska cesta 12, 1000 Ljubljana<br>
            ID DDV:SI98789309 (v nadaljevanju <strong>prevzemnik</strong>)
        </div>
        <div class="party-separator">in</div>
        @php
            $lastEntity = $compenzation->compenzationEntity->last();
        @endphp
        @if($lastEntity && $lastEntity->entity)
        <div class="party-info">
            <strong>{{ strtoupper($lastEntity->entity->company_name) }}</strong> {{ $lastEntity->entity->address }}, {{ $lastEntity->entity->post_num }} {{ $lastEntity->entity->post_town }}<br>
            ID DDV:{{ $lastEntity->entity->vat_num }} (v nadaljevanju <strong>odstopnik</strong>)
        </div>
        @endif
    </div>

    <div class="contract-title">
        skleneta naslednjo: <strong>POGODBO O UNOVČENJU TERJATEV ŠT. U{{ $compenzation->id }}/ {{ $compenzation->year }}</strong>
    </div>

    <div class="article">
        <div class="article-title">1. člen</div>
        <p>Predmet te pogodbe so nesporne, neizpodbitne in zapadle terjatve v vrednosti {{ number_format((float)$compenzation->amount, 2, ',', '.') }} €, katere ima odstopnik do svojih dolžnikov.</p>
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
        @php
            $amount = (float)$compenzation->amount;
            $commissionRate = (float)($agreement->commission ?? 8);
            $commission = $amount * ($commissionRate / 100);
            $vatRate = 0.22;
            $vatAmount = $commission * $vatRate;
            $totalCommission = $commission + $vatAmount;
        @endphp
        <p>Kot plačilo za opravljeno storitev unovčenja terjatve, zaračuna prevzemnik odstopniku {{ $commissionRate }} % provizijo obračunano od vrednosti obračunano od vrednosti iz 1. člena te pogodbe. To znaša {{ number_format($commission, 2, ',', '.') }} €. Na to vrednost se obračuna 22% DDV v znesku {{ number_format($vatAmount, 2, ',', '.') }} €. Unovčenje terjatve se praviloma izvaja postopno. Storitev in DDV se zato obračunavata v skladu s 33.členom pravilnika o izvajanju ZDDV. Kot obračunsko obdobje šteje koledarski mesec, prevzemnik pa izstavlja račune in obračunava DDV najkasneje zadnji dan v mesecu.</p>
    </div>

    <div class="article">
        <div class="article-title">5. člen</div>
        @php
            $transferAmount = $amount - $totalCommission;
            $accountNumber = $lastEntity && $lastEntity->entity ? ($lastEntity->entity->bank_account ?? '06382 0133110880') : '06382 0133110880';
        @endphp
        <p>Prevzemnik bo obveznost plačila terjatev, ki nastane s potrditvijo vseh udeležencev predlaganega pobota poravnal z nakazilom sredstev na račun odstopnika. Znesek kupnine v višini {{ number_format($transferAmount, 2, ',', '.') }} €, ki je zmanjšan za v 4.členu dogovorjeno provizijo in za DDV, bo prevzemnik nakazoval po izvršenih pobotih na TRR: {{ $accountNumber }}</p>
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

    <div class="signature-section">
        <div class="signature-left">
            <div class="signature-line">
                <strong>ODSTOPNIK:</strong>
            </div>
        </div>
        <div class="signature-right">
            <div class="signature-line">
                <strong>PREVZEMNIK:</strong> KORENJAK Finančno svetovanje MATEVŽ Korenjak
            </div>
            <div class="stamp">
                <img src="{{ public_path('images/pdf/zig.jpg') }}" alt="Žig">
            </div>
        </div>
    </div>
</body>
</html>
