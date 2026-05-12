<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predlog Kompenzacije - {{ $compenzation->name }}</title>
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
        $entities = collect($compenzation->compenzationEntity ?? []);
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

    {{-- Document Number --}}
    <div class="document-number">
        ŠT.KOMPENZACIJE:{{ str_replace('Kompenzacija-', '', $compenzation->name) }}
    </div>

    {{-- Date and Location --}}
    <div class="document-date">
        Ljubljana, {{ \Carbon\Carbon::parse($compenzation->date)->format('d.m.Y') }}
    </div>

    {{-- Main Title --}}
    <div class="main-title">
        PREDLOG VERIŽNE KOMPENZACIJE V ZNESKU: {{ number_format((float)$compenzation->amount, 2, ',', '.') }} €
    </div>

    {{-- Introduction --}}
    <div class="intro-text">
        <p>
            Na osnovi <strong>Zakona o obligacijskih razmerjih</strong> podpisniki predloga verižne kompenzacije soglašamo, 
            da se terjatve in obveznosti poravnajo na naslednji način:
        </p>
    </div>

    {{-- Entities List (Chain) --}}
    <div class="entities-list">
        {{-- First entity: MATEVŽ KORENJAK S.P. --}}
        <div class="entity-item">
            <div class="entity-row">
                <div class="entity-address">
                    <span class="entity-number">1.</span>
                    <strong>MATEVŽ KORENJAK S.P.</strong>, Litostrojska cesta 12, 1000 Ljubljana
                </div>
                <div class="entity-email">
                    matevz.korenjak@kompenzacije.eu
                </div>
            </div>
            @if($entities->count() > 0)
            <div class="entity-debt">dolguje:</div>
            @else
            {{-- Stamp --}}
            <div class="stamp">
                <img src="{{ public_path('images/pdf/zig.jpg') }}" alt="Žig">
            </div>
            @endif
        </div>

        {{-- Other entities --}}
        @if($entities->count() > 0)
            @foreach($entities as $index => $compenzationEntity)
                @php
                    $entityNumber = $index + 2; // Start from 2 (1 is already used for MATEVŽ KORENJAK)
                @endphp
                <div class="entity-item">
                    <div class="entity-row">
                        <div class="entity-address">
                            <span class="entity-number">{{ $entityNumber }}.</span>
                            <strong>{{ strtoupper($compenzationEntity->entity->company_name ?? 'N/A') }}</strong>
                            @if($compenzationEntity->entity->address || $compenzationEntity->entity->post_num || $compenzationEntity->entity->post_town)
                                , {{ $compenzationEntity->entity->address ?? '' }}{{ $compenzationEntity->entity->address ? ', ' : '' }}{{ $compenzationEntity->entity->post_num ?? '' }} {{ $compenzationEntity->entity->post_town ?? '' }}
                            @endif
                        </div>
                        <div class="entity-email">
                            {{ $compenzationEntity->entity->email ?? '' }}
                        </div>
                    </div>
                    @if($index < $entities->count() - 1)
                    <div class="entity-debt">dolguje:</div>
                    @endif
                </div>
            @endforeach
        @endif

        {{-- Last entity: MATEVŽ KORENJAK S.P. --}}
        @if($entities->count() > 0)
        <div class="entity-item">
            <div class="entity-debt">dolguje:</div>
            <div class="entity-row">
                <div class="entity-address">
                    <span class="entity-number">{{ $entities->count() + 2 }}.</span>
                    <strong>MATEVŽ KORENJAK S.P.</strong>, Litostrojska cesta 12, 1000 Ljubljana
                </div>
                <div class="entity-email">
                    matevz.korenjak@kompenzacije.eu
                </div>
            </div>
            {{-- Stamp --}}
            <div class="stamp">
                <img src="{{ public_path('images/pdf/zig.jpg') }}" alt="Žig">
            </div>
        </div>
        @endif
    </div>

    {{-- Declaration Text --}}
    <div class="declaration-text">
        <p>
            Stranke naprošamo, da potrjen izvod pošljejo predlagatelju. Kompenzacija se izvrši po prejetju vseh potrditev. Datum izvršitve:
        </p>
        <p>
            Dolžniki kompenzacije izjavljamo, da je predlagana kompenzacija običajen način plačila obveznosti in zato ne bomo uveljavljali morebitnih zahtev po <strong>Zakonu o finančnem poslovanju podjetij (ZFPP)</strong> in po <strong>Zakonu o prisilni poravnavi, stečaju in likvidaciji (ZPPSL)</strong>.
        </p>
    </div>
</body>
</html>
