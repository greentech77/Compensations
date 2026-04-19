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
        .findings-section {
            margin: 20px 0;
            line-height: 1.8;
            text-align: left;
        }
        .findings-section ul {
            margin: 8px 0;
            padding-left: 20px;
            list-style-type: disc;
        }
        .findings-section li {
            margin: 5px 0;
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
            margin: 30px 0 10px 0;
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
            <p>Matevž Korenjak s.p., Litostrojska cesta 12, 1000 Ljubljana</p>
            <p>Email: matevz.korenjak@kompenzacije.eu</p>
            <p>Telefon: 031 227 139, Faks: 08 288 00 77</p>
            <p><strong>SI98789309</strong></p>
        </div>
    </div>
    <div class="header-line"></div>

    @php
        $firstEntity = $compenzation->compenzationEntity->first();
        $lastEntity = $compenzation->compenzationEntity->last();
    @endphp

    <div class="parties-section">
        @if($firstEntity && $firstEntity->entity)
        <div class="party-info">
            <strong>{{ strtoupper($firstEntity->entity->company_name) }}</strong> {{ $firstEntity->entity->address }}, {{ $firstEntity->entity->post_num }} {{ $firstEntity->entity->post_town }}<br>
            ID DDV:{{ $firstEntity->entity->vat_num }} (v nadaljevanju <strong>prva stranka</strong>)
        </div>
        @endif
        <div class="party-separator">in</div>
        <div class="party-info">
            <strong>MATEVŽ KORENJAK S.P.</strong>, Litostrojska cesta 12, 1000 Ljubljana<br>
            ID DDV:SI98789309 (v nadaljevanju <strong>druga stranka</strong>)
        </div>
    </div>

    <div class="contract-title">
        skleneta naslednjo: <strong>POGODBO O IZVEDBI VERIŽNE KOMPENZACIJE ŠT. {{ $compenzation->id }}/ {{ $compenzation->year }}</strong>
    </div>

    <div class="findings-section">
        <p><strong>Ugotovi se:</strong></p>
        <ul>
            @if($compenzation->compenzationEntity && $compenzation->compenzationEntity->count() > 1)
                @php
                    $secondEntity = $compenzation->compenzationEntity->skip(1)->first();
                @endphp
                @if($secondEntity && $secondEntity->entity)
                <li>da ima prva stranka obveznost do upnika <strong>{{ strtoupper($secondEntity->entity->company_name) }}</strong> {{ $secondEntity->entity->address }}, {{ $secondEntity->entity->post_num }} {{ $secondEntity->entity->post_town }} v višini {{ number_format((float)$compenzation->amount, 2, ',', '.') }} €</li>
                @endif
            @endif
            <li>prva stranka ima interes, da poplača svoje obveznosti do upnika s popustom {{ $agreement->discount ?? '5' }} %</li>
            <li>druga stranka je nosilec pravic in obveznosti po predlogu verižne kompenzacije, katere izvedba je predmet te pogodbe</li>
        </ul>
    </div>

    <div class="article">
        <div class="article-title">1.člen</div>
        <p>Druga stranka se obvezuje, da bo predlog verižne kompenzacije, ki je predmet te pogodbe sprejela v celoti in ga podpisala, ter predložila v podpis in potrditev drugim pravnim subjektom iz predmetne verižne kompenzacije. Potrjeni predlog verižne kompenzacije je sestavni del te pogodbe.</p>
    </div>

    <div class="article">
        <div class="article-title">2.člen</div>
        <p>Prva stranka se vsled izvedbe verižne kompenzacije obvezuje drugi stranki nakazati znesek verižne kompenzacije, zmanjšan za provizijo s pripadajočim DDV, na njen transakcijski račun: <strong>10100-0050372968</strong> najkasneje do __________. Vendar le pod pogojem, da je predlog verižne kompenzacije v celoti potrjen iz strani vseh udeležencev.</p>
    </div>

    <div class="article">
        <div class="article-title">3.člen</div>
        <p>Druga stranka se obvezuje prvi stranki, da prevzema vsled nakazila iz 2.člena te pogodbe, obveznost do prve stranke v višini potrjene kompenzacije, ki pa bo predmet medsebojne kompenzacije iz te pogodbe.</p>
    </div>

    <div class="article">
        <div class="article-title">4.člen</div>
        <p>Stranki sta soglasni, da je ta pogodba v skladu z 2. in 3. odstavkom 136. člena Pravilnika o izvajanju ZDDV, v povezavi z 81. členom Pravilnika o izvajanju ZDDV-1 in se šteje kot račun.</p>
    </div>

    <div class="article">
        <div class="article-title">5.člen</div>
        @php
            $amount = (float)$compenzation->amount;
            $discount = (float)($agreement->discount ?? 5);
            $provisionWithVAT = $amount * ($discount / 100);
            $vatRate = 0.22;
            $netProvision = $provisionWithVAT / (1 + $vatRate);
            $vatAmount = $provisionWithVAT - $netProvision;
            $transferAmount = $amount - $provisionWithVAT;
        @endphp
        <div class="calculation-item">
            <div class="calculation-label">Po pogodbi o izvedbi verižne kompenzacije prva stranka zaračuna drugi stranki provizijo v višini:</div>
            <div class="calculation-value">{{ number_format($provisionWithVAT, 2, ',', '.') }} €</div>
        </div>
        <div class="calculation-item">
            <div class="calculation-label">{{ $discount }}% z vključenim DDV od zneska realizirane verižne kompenzacije, v višini:</div>
            <div class="calculation-value">{{ number_format($provisionWithVAT, 2, ',', '.') }}€</div>
        </div>
        <div class="calculation-item">
            <div class="calculation-label">Od katerega je 22% DDV:</div>
            <div class="calculation-value">{{ number_format($vatAmount, 2, ',', '.') }}€</div>
        </div>
        <div class="calculation-item">
            <div class="calculation-label">Neto znesek brez DDV (davčna osnova):</div>
            <div class="calculation-value">{{ number_format($netProvision, 2, ',', '.') }}€</div>
        </div>
        <p>Dogovorjena provizija z DDV se pri nakazilu enostransko pobota.</p>
        <div class="calculation-item">
            <div class="calculation-label"><strong>Znesek nakazila je:</strong></div>
            <div class="calculation-value"><strong>{{ number_format($transferAmount, 2, ',', '.') }}€</strong></div>
        </div>
    </div>

    <div class="article">
        <div class="article-title">6.člen</div>
        <p>Pogodbeni stranki soglašata, da bosta morebitne spore reševala mirno, v nasprotnem primeru je za to pristojno sodišče v Ljubljani.</p>
    </div>

    <div class="date-location">
        V Ljubljani, {{ \Carbon\Carbon::parse($compenzation->date)->format('d.m.Y') }}
    </div>

    <div class="signature-section">
        <div class="signature-left">
            @if($firstEntity && $firstEntity->entity)
            <div class="signature-line">
                Prva stranka: <strong>{{ strtoupper($firstEntity->entity->company_name) }}</strong>
            </div>
            @endif
        </div>
        <div class="signature-right">
            <div class="signature-line">
                Druga stranka: <strong>MATEVŽ KORENJAK S.P.</strong>
            </div>
            <div class="stamp" style="margin-top: 10px;">
                <img src="{{ public_path('images/pdf/zig.jpg') }}" alt="Žig">
            </div>
        </div>
    </div>
</body>
</html>
