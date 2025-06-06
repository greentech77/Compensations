<?php

namespace App\Services\Autocomplete\Traits;

trait HasPostcode {

    private $codes = [
        1000 => "Ljubljana",
        1001 => "Ljubljana - poštni predali",
        1002 => "Ljubljana",
        1210 => "Ljubljana - Šentvid",
        1211 => "Ljubljana - Šmartno",
        1215 => "Medvode",
        1216 => "Smlednik",
        1217 => "Vodice",
        1218 => "Komenda",
        1219 => "Laze v Tuhinju",
        1221 => "Motnik",
        1222 => "Trojane",
        1223 => "Blagovica",
        1225 => "Lukovica",
        1230 => "Domžale",
        1231 => "Ljubljana - Črnuče",
        1233 => "Dob",
        1234 => "Mengeš",
        1235 => "Radomlje",
        1236 => "Trzin",
        1241 => "Kamnik",
        1242 => "Stahovica",
        1251 => "Moravče",
        1252 => "Vače",
        1260 => "Ljubljana - Polje",
        1261 => "Ljubljana - Dobrunje",
        1262 => "Dol pri Ljubljani",
        1270 => "Litija",
        1272 => "Polšnik",
        1273 => "Dole pri Litiji",
        1274 => "Gabrovka",
        1275 => "Šmartno pri Litiji",
        1276 => "Primskovo",
        1281 => "Kresnice",
        1282 => "Sava",
        1290 => "Grosuplje",
        1291 => "Škofljica",
        1292 => "Ig",
        1293 => "Šmarje - Sap",
        1294 => "Višnja Gora",
        1295 => "Ivančna Gorica",
        1296 => "Šentvid pri Stični",
        1301 => "Krka",
        1303 => "Zagradec",
        1310 => "Ribnica",
        1311 => "Turjak",
        1312 => "Videm - Dobrepolje",
        1313 => "Struge",
        1314 => "Rob",
        1315 => "Velike Lašče",
        1316 => "Ortnek",
        1317 => "Sodražica",
        1318 => "Loški Potok",
        1319 => "Draga",
        1330 => "Kočevje",
        1331 => "Dolenja vas",
        1332 => "Stara Cerkev",
        1336 => "Kostel",
        1337 => "Osilnica",
        1338 => "Kočevska Reka",
        1351 => "Brezovica pri Ljubljani",
        1352 => "Preserje",
        1353 => "Borovnica",
        1354 => "Horjul",
        1355 => "Polhov Gradec",
        1356 => "Dobrova",
        1357 => "Notranje Gorice",
        1358 => "Log pri Brezovici",
        1360 => "Vrhnika",
        1370 => "Logatec",
        1371 => "Logatec",
        1372 => "Hotedršica",
        1373 => "Rovte",
        1380 => "Cerknica",
        1381 => "Rakek",
        1382 => "Begunje pri Cerknici",
        1384 => "Grahovo",
        1385 => "Nova vas",
        1386 => "Stari trg pri Ložu",
        1410 => "Zagorje ob Savi",
        1411 => "Izlake",
        1412 => "Kisovec",
        1413 => "Čemšenik",
        1414 => "Podkum",
        1420 => "Trbovlje",
        1423 => "Dobovec",
        1430 => "Hrastnik",
        1431 => "Dol pri Hrastniku",
        1432 => "Zidani Most",
        1433 => "Radeče",
        1434 => "Loka pri Zidanem Mostu",
        1501 => "Ljubljana",
        1502 => "Ljubljana",
        1503 => "Ljubljana",
        1504 => "Ljubljana",
        1505 => "Ljubljana",
        1506 => "Ljubljana",
        1507 => "Ljubljana",
        1509 => "Ljubljana",
        1510 => "Ljubljana",
        1511 => "Ljubljana",
        1512 => "Ljubljana",
        1513 => "Ljubljana",
        1514 => "Ljubljana",
        1516 => "Ljubljana",
        1517 => "Ljubljana",
        1518 => "Ljubljana",
        1519 => "Ljubljana",
        1520 => "Ljubljana",
        1521 => "Ljubljana",
        1522 => "Ljubljana",
        1523 => "Ljubljana",
        1524 => "Ljubljana",
        1525 => "Ljubljana",
        1526 => "Ljubljana",
        1527 => "Ljubljana",
        1528 => "Ljubljana",
        1529 => "Ljubljana",
        1532 => "Ljubljana",
        1533 => "Ljubljana",
        1534 => "Ljubljana",
        1535 => "Ljubljana",
        1536 => "Ljubljana",
        1537 => "Ljubljana",
        1538 => "Ljubljana",
        1540 => "Ljubljana",
        1542 => "Ljubljana",
        1543 => "Ljubljana",
        1544 => "Ljubljana",
        1545 => "Ljubljana",
        1546 => "Ljubljana",
        1547 => "Ljubljana",
        1550 => "Ljubljana",
        1600 => "Ljubljana",
        2000 => "Maribor",
        2001 => "Maribor - poštni predali",
        2002 => "Maribor",
        2201 => "Zgornja Kungota",
        2204 => "Miklavž na Dravskem polju",
        2205 => "Starše",
        2206 => "Marjeta na Dravskem polju",
        2208 => "Pohorje",
        2211 => "Pesnica pri Mariboru",
        2212 => "Šentilj v Slovenskih goricah",
        2213 => "Zgornja Velka",
        2214 => "Sladki Vrh",
        2215 => "Ceršak",
        2221 => "Jarenina",
        2222 => "Jakobski Dol",
        2223 => "Jurovski Dol",
        2229 => "Malečnik",
        2230 => "Lenart v Slovenskih goricah",
        2231 => "Pernica",
        2232 => "Voličina",
        2233 => "Sv. Ana v Slovenskih goricah",
        2234 => "Benedikt",
        2235 => "Sv. Trojica v Slovenskih goricah",
        2236 => "Cerkvenjak",
        2241 => "Spodnji Duplek",
        2242 => "Zgornja Korena",
        2250 => "Ptuj",
        2252 => "Dornava",
        2253 => "Destrnik",
        2254 => "Trnovska vas",
        2255 => "Vitomarci",
        2256 => "Juršinci",
        2257 => "Polenšak",
        2258 => "Sveti Tomaž",
        2259 => "Ivanjkovci",
        2270 => "Ormož",
        2272 => "Gorišnica",
        2273 => "Podgorci",
        2274 => "Velika Nedelja",
        2275 => "Miklavž pri Ormožu",
        2276 => "Kog",
        2277 => "Središče ob Dravi",
        2281 => "Markovci",
        2282 => "Cirkulane",
        2283 => "Zavrč",
        2284 => "Videm pri Ptuju",
        2285 => "Zgornji Leskovec",
        2286 => "Podlehnik",
        2287 => "Žetale",
        2288 => "Hajdina",
        2289 => "Stoperce",
        2310 => "Slovenska Bistrica",
        2311 => "Hoče",
        2312 => "Orehova vas",
        2313 => "Fram",
        2314 => "Zgornja Polskava",
        2315 => "Šmartno na Pohorju",
        2316 => "Zgornja Ložnica",
        2317 => "Oplotnica",
        2318 => "Laporje",
        2319 => "Poljčane",
        2321 => "Makole",
        2322 => "Majšperk",
        2323 => "Ptujska Gora",
        2324 => "Lovrenc na Dravskem polju",
        2325 => "Kidričevo",
        2326 => "Cirkovce",
        2327 => "Rače",
        2331 => "Pragersko",
        2341 => "Limbuš",
        2342 => "Ruše",
        2343 => "Fala",
        2344 => "Lovrenc na Pohorju",
        2345 => "Bistrica ob Dravi",
        2351 => "Kamnica",
        2352 => "Selnica ob Dravi",
        2353 => "Sveti Duh na Ostrem Vrhu",
        2354 => "Bresternica",
        2360 => "Radlje ob Dravi",
        2361 => "Ožbalt",
        2362 => "Kapla",
        2363 => "Podvelka",
        2364 => "Ribnica na Pohorju",
        2365 => "Vuhred",
        2366 => "Muta",
        2367 => "Vuzenica",
        2370 => "Dravograd",
        2371 => "Trbonje",
        2372 => "Libeliče",
        2373 => "Šentjanž pri Dravogradu",
        2380 => "Slovenj Gradec",
        2381 => "Podgorje pri Slovenj Gradcu",
        2382 => "Mislinja",
        2383 => "Šmartno pri Slovenj Gradcu",
        2390 => "Ravne na Koroškem",
        2391 => "Prevalje",
        2392 => "Mežica",
        2393 => "Črna na Koroškem",
        2394 => "Kotlje",
        2500 => "Maribor",
        2501 => "Maribor",
        2502 => "Maribor",
        2503 => "Maribor",
        2504 => "Maribor",
        2505 => "Maribor",
        2506 => "Maribor",
        2507 => "Maribor",
        2508 => "Maribor",
        2509 => "Maribor",
        2600 => "Maribor",
        2603 => "Maribor",
        2609 => "Maribor",
        2610 => "Maribor",
        3000 => "Celje",
        3001 => "Celje - poštni predali",
        3201 => "Šmartno v Rožni dolini",
        3202 => "Ljubečna",
        3203 => "Nova Cerkev",
        3204 => "Dobrna",
        3205 => "Vitanje",
        3206 => "Stranice",
        3210 => "Slovenske Konjice",
        3211 => "Škofja vas",
        3212 => "Vojnik",
        3213 => "Frankolovo",
        3214 => "Zreče",
        3215 => "Loče",
        3220 => "Štore",
        3221 => "Teharje",
        3222 => "Dramlje",
        3223 => "Loka pri Žusmu",
        3224 => "Dobje pri Planini",
        3225 => "Planina pri Sevnici",
        3230 => "Šentjur",
        3231 => "Grobelno",
        3232 => "Ponikva",
        3233 => "Kalobje",
        3240 => "Šmarje pri Jelšah",
        3241 => "Podplat",
        3250 => "Rogaška Slatina",
        3252 => "Rogatec",
        3253 => "Pristava pri Mestinju",
        3254 => "Podčetrtek",
        3255 => "Buče",
        3256 => "Bistrica ob Sotli",
        3257 => "Podsreda",
        3260 => "Kozje",
        3261 => "Lesično",
        3262 => "Prevorje",
        3263 => "Gorica pri Slivnici",
        3264 => "Sveti Štefan",
        3270 => "Laško",
        3271 => "Šentrupert",
        3272 => "Rimske Toplice",
        3273 => "Jurklošter",
        3301 => "Petrovče",
        3302 => "Griže",
        3303 => "Gomilsko",
        3304 => "Tabor",
        3305 => "Vransko",
        3310 => "Žalec",
        3311 => "Šempeter v Savinjski dolini",
        3312 => "Prebold",
        3313 => "Polzela",
        3314 => "Braslovče",
        3320 => "Velenje",
        3322 => "Velenje - poštni predali",
        3325 => "Šoštanj",
        3326 => "Topolšica",
        3327 => "Šmartno ob Paki",
        3330 => "Mozirje",
        3331 => "Nazarje",
        3332 => "Rečica ob Savinji",
        3333 => "Ljubno ob Savinji",
        3334 => "Luče",
        3335 => "Solčava",
        3341 => "Šmartno ob Dreti",
        3342 => "Gornji Grad",
        3503 => "Velenje",
        3504 => "Velenje",
        3505 => "Celje",
        3600 => "Celje",
        4000 => "Kranj",
        4001 => "Kranj - poštni predali",
        4200 => "Kranj",
        4201 => "Zgornja Besnica",
        4202 => "Naklo",
        4203 => "Duplje",
        4204 => "Golnik",
        4205 => "Preddvor",
        4206 => "Zgornje Jezersko",
        4207 => "Cerklje na Gorenjskem",
        4208 => "Šenčur",
        4209 => "Žabnica",
        4210 => "Brnik - Aerodrom",
        4211 => "Mavčiče",
        4212 => "Visoko",
        4220 => "Škofja Loka",
        4223 => "Poljane nad Škofjo Loko",
        4224 => "Gorenja vas",
        4225 => "Sovodenj",
        4226 => "Žiri",
        4227 => "Selca",
        4228 => "Železniki",
        4229 => "Sorica",
        4240 => "Radovljica",
        4243 => "Brezje",
        4244 => "Podnart",
        4245 => "Kropa",
        4246 => "Kamna Gorica",
        4247 => "Zgornje Gorje",
        4248 => "Lesce",
        4260 => "Bled",
        4263 => "Bohinjska Bela",
        4264 => "Bohinjska Bistrica",
        4265 => "Bohinjsko jezero",
        4267 => "Srednja vas v Bohinju",
        4270 => "Jesenice",
        4273 => "Blejska Dobrava",
        4274 => "Žirovnica",
        4275 => "Begunje na Gorenjskem",
        4276 => "Hrušica",
        4280 => "Kranjska Gora",
        4281 => "Mojstrana",
        4282 => "Gozd Martuljek",
        4283 => "Rateče - Planica",
        4290 => "Tržič",
        4294 => "Križe",
        4501 => "Naklo",
        4502 => "Kranj",
        4600 => "Kranj",
        5000 => "Nova Gorica",
        5001 => "Nova Gorica - poštni predali",
        5210 => "Deskle",
        5211 => "Kojsko",
        5212 => "Dobrovo v Brdih",
        5213 => "Kanal",
        5214 => "Kal nad Kanalom",
        5215 => "Ročinj",
        5216 => "Most na Soči",
        5220 => "Tolmin",
        5222 => "Kobarid",
        5223 => "Breginj",
        5224 => "Srpenica",
        5230 => "Bovec",
        5231 => "Log pod Mangartom",
        5232 => "Soča",
        5242 => "Grahovo ob Bači",
        5243 => "Podbrdo",
        5250 => "Solkan",
        5251 => "Grgar",
        5252 => "Trnovo pri Gorici",
        5253 => "Čepovan",
        5261 => "Šempas",
        5262 => "Črniče",
        5263 => "Dobravlje",
        5270 => "Ajdovščina",
        5271 => "Vipava",
        5272 => "Podnanos",
        5273 => "Col",
        5274 => "Črni Vrh nad Idrijo",
        5275 => "Godovič",
        5280 => "Idrija",
        5281 => "Spodnja Idrija",
        5282 => "Cerkno",
        5283 => "Slap ob Idrijci",
        5290 => "Šempeter pri Gorici",
        5291 => "Miren",
        5292 => "Renče",
        5293 => "Volčja Draga",
        5294 => "Dornberk",
        5295 => "Branik",
        5296 => "Kostanjevica na Krasu",
        5297 => "Prvačina",
        5600 => "Nova Gorica",
        6000 => "Koper/Capodistria",
        6001 => "Koper/Capodistria - poštni predali",
        6200 => "Koper",
        6210 => "Sežana",
        6215 => "Divača",
        6216 => "Podgorje",
        6217 => "Vremski Britof",
        6219 => "Lokev",
        6221 => "Dutovlje",
        6222 => "Štanjel",
        6223 => "Komen",
        6224 => "Senožeče",
        6225 => "Hruševje",
        6230 => "Postojna",
        6232 => "Planina",
        6240 => "Kozina",
        6242 => "Materija",
        6243 => "Obrov",
        6244 => "Podgrad",
        6250 => "Ilirska Bistrica",
        6251 => "Ilirska Bistrica-Trnovo",
        6253 => "Knežak",
        6254 => "Jelšane",
        6255 => "Prem",
        6256 => "Košana",
        6257 => "Pivka",
        6258 => "Prestranek",
        6271 => "Dekani",
        6272 => "Gračišče",
        6273 => "Marezige",
        6274 => "Šmarje",
        6275 => "Črni Kal",
        6276 => "Pobegi",
        6280 => "Ankaran/Ancarano",
        6281 => "Škofije",
        6310 => "Izola/Isola",
        6320 => "Portorož/Portorose",
        6330 => "Piran/Pirano",
        6333 => "Sečovlje/Sicciole",
        6501 => "Koper",
        6502 => "Koper",
        6503 => "Koper",
        6504 => "Koper",
        6600 => "Koper",
        8000 => "Novo mesto",
        8001 => "Novo mesto - poštni predali",
        8210 => "Trebnje",
        8211 => "Dobrnič",
        8212 => "Velika Loka",
        8213 => "Veliki Gaber",
        8216 => "Mirna Peč",
        8220 => "Šmarješke Toplice",
        8222 => "Otočec",
        8230 => "Mokronog",
        8231 => "Trebelno",
        8232 => "Šentrupert",
        8233 => "Mirna",
        8250 => "Brežice",
        8251 => "Čatež ob Savi",
        8253 => "Artiče",
        8254 => "Globoko",
        8255 => "Pišece",
        8256 => "Sromlje",
        8257 => "Dobova",
        8258 => "Kapele",
        8259 => "Bizeljsko",
        8261 => "Jesenice na Dolenjskem",
        8262 => "Krška vas",
        8263 => "Cerklje ob Krki",
        8270 => "Krško",
        8272 => "Zdole",
        8273 => "Leskovec pri Krškem",
        8274 => "Raka",
        8275 => "Škocjan",
        8276 => "Bučka",
        8280 => "Brestanica",
        8281 => "Senovo",
        8282 => "Koprivnica",
        8283 => "Blanca",
        8290 => "Sevnica",
        8292 => "Zabukovje",
        8293 => "Studenec",
        8294 => "Boštanj",
        8295 => "Tržišče",
        8296 => "Krmelj",
        8297 => "Šentjanž",
        8310 => "Šentjernej",
        8311 => "Kostanjevica na Krki",
        8312 => "Podbočje",
        8321 => "Brusnice",
        8322 => "Stopiče",
        8323 => "Uršna sela",
        8330 => "Metlika",
        8331 => "Suhor",
        8332 => "Gradac",
        8333 => "Semič",
        8340 => "Črnomelj",
        8341 => "Adlešiči",
        8342 => "Stari trg ob Kolpi",
        8343 => "Dragatuš",
        8344 => "Vinica",
        8350 => "Dolenjske Toplice",
        8351 => "Straža",
        8360 => "Žužemberk",
        8361 => "Dvor",
        8362 => "Hinje",
        8501 => "Novo mesto",
        8600 => "Novo mesto",
        9000 => "Murska Sobota",
        9001 => "Murska Sobota - poštni predali",
        9201 => "Puconci",
        9202 => "Mačkovci",
        9203 => "Petrovci",
        9204 => "Šalovci",
        9205 => "Hodoš/Hodos",
        9206 => "Križevci",
        9207 => "Prosenjakovci/Partosfalva",
        9208 => "Fokovci",
        9220 => "Lendava/Lendva",
        9221 => "Martjanci",
        9222 => "Bogojina",
        9223 => "Dobrovnik/Dobronak",
        9224 => "Turnišče",
        9225 => "Velika Polana",
        9226 => "Moravske Toplice",
        9227 => "Kobilje",
        9231 => "Beltinci",
        9232 => "Črenšovci",
        9233 => "Odranci",
        9240 => "Ljutomer",
        9241 => "Veržej",
        9242 => "Križevci pri Ljutomeru",
        9243 => "Mala Nedelja",
        9244 => "Sveti Jurij ob Ščavnici",
        9245 => "Spodnji Ivanjci",
        9246 => "Razkrižje",
        9250 => "Gornja Radgona",
        9251 => "Tišina",
        9252 => "Radenci",
        9253 => "Apače",
        9261 => "Cankova",
        9262 => "Rogašovci",
        9263 => "Kuzma",
        9264 => "Grad",
        9265 => "Bodonci",
        9501 => "Murska Sobota",
        9502 => "Radenci",
        9600 => "Murska Sobota"
    ];

}