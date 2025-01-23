<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Api\Utils\Response;
use OpenApi as OApi;
use OpenApi\Attributes as OA;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[OA\OpenApi(
    openapi: '3.1.0',
    info: new OA\Info(
        version: 'v1',
        description: '<p><b />AlQuran API
        <br />The Holy Quran is a divine book from Allah SWT.</p>
        You can get the text for complete edition, search words in text using the endpoints below.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Ayah'),
        new OA\Tag(name: 'Edition'),
        new OA\Tag(name: 'HizbQuarter'),
        new OA\Tag(name: 'Juz'),
        new OA\Tag(name: 'Manzil'),
        new OA\Tag(name: 'Meta'),
        new OA\Tag(name: 'Page'),
        new OA\Tag(name: 'Quran'),
        new OA\Tag(name: 'Ruku'),
        new OA\Tag(name: 'Sajda'),
        new OA\Tag(name: 'Search'),
        new OA\Tag(name: 'Surah')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200EditionQuranUthmaniResponse',
            allOf: [
                new OA\Schema(ref: '#/components/schemas/200QuranUthmaniEditionResponse'),
                new OA\Schema(
                    properties: [
                        new OA\Property(property: 'direction', type: 'string', example: 'rtl')
                    ]
                )
            ]
        ),
        new OA\Schema(
            schema: '200QuranUthmaniEditionResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: 'quran-uthmani-quran-academy'),
                new OA\Property(property: 'language', type: 'string', example: 'ar'),
                new OA\Property(property: 'name', type: 'string', example: "القرآن الكريم برسم العثماني (quran-academy)" ),
                new OA\Property(property: 'englishName', type: 'string', example: "Modified Quran Uthmani Text from the Quran Academy to work with the Kitab font"),
                new OA\Property(property: 'format', type: 'string', example: 'text'),
                new OA\Property(property: 'type', type: 'string', example: 'quran')
            ]
        ),
        new OA\Schema(
            schema: '200QuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'surahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                            new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                            new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                            new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                            new OA\Property(property: 'ayahs', type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'number', type: 'integer', example: 1),
                                        new OA\Property(property: 'text', type: 'string', example: "بِسۡمِ ٱللَّهِ ٱلرَّحۡمَـٰنِ ٱلرَّحِیمِ"),
                                        new OA\Property(property: 'numberInSurah', type: 'integer', example: 1),
                                        new OA\Property(property: 'juz', type: 'integer', example: 1),
                                        new OA\Property(property: 'manzil', type: 'integer', example: 1),
                                        new OA\Property(property: 'page', type: 'integer', example: 1),
                                        new OA\Property(property: 'ruku', type: 'integer', example: 1),
                                        new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                                        new OA\Property(property: 'sajda', type: 'boolean', example: false)
                                    ], type: 'object'
                                )
                            )
                        ], type: 'object'
                    )
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200QuranUthmaniEditionResponse', type: 'object')
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200EditionQuranSimpleResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: 'quran-simple'),
                new OA\Property(property: 'language', type: 'string', example: 'ar'),
                new OA\Property(property: 'name', type: 'string', example: "القرآن الكريم المبسط (تشكيل بسيط) (simple)"),
                new OA\Property(property: 'englishName', type: 'string', example: "Simple"),
                new OA\Property(property: 'format', type: 'string', example: 'text'),
                new OA\Property(property: 'type', type: 'string', example: 'quran'),
                new OA\Property(property: 'direction', type: 'string', example: 'rtl')
            ]
        ),
        new OA\Schema(
            schema: 'partialSurahResponse',
            properties: [
                new OA\Property(property: 'surah', ref: '#/components/schemas/200SurahPartialSurahResponse', type: 'object'),
                new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                new OA\Property(property: 'juz', type: 'integer', example: 1),
                new OA\Property(property: 'manzil', type: 'integer', example: 1),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'ruku', type: 'integer', example: 1),
                new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                new OA\Property(property: 'sajda', type: 'boolean', example: false)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200AyahQuranUthmaniResponse',
            type: 'object', allOf: [
                new OA\Schema(
                    properties: [
                        new OA\Property(property: 'number', type: 'integer', example: 5),
                        new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                        new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranUthmaniResponse', type: 'object'),
                    ]
                ),
                new OA\Schema(ref: '#/components/schemas/partialSurahResponse')
            ]
        ),
        new OA\Schema(
            schema: '200AyahQuranSimpleResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 5),
                new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranSimpleResponse', type: 'object'),
                new OA\Property(property: 'surah', ref: '#/components/schemas/200SurahPartialSurahResponse', type: 'object'),
                new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                new OA\Property(property: 'juz', type: 'integer', example: 1),
                new OA\Property(property: 'manzil', type: 'integer', example: 1),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'ruku', type: 'integer', example: 1),
                new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                new OA\Property(property: 'sajda', type: 'boolean', example: false)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200QuranUthmaniPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SajdaQuranUthmaniPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 7),
                new OA\Property(property: 'name', type: 'string', example:  "سُورَةُ الأَعۡرَافِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-A'raaf"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Heights"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 206)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SurahPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SajdaQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 1160),
                            new OA\Property(property: 'text', type: 'string', example: "إِنَّ ٱلَّذِینَ عِندَ رَبِّكَ لَا یَسۡتَكۡبِرُونَ عَنۡ عِبَادَتِهِۦ وَیُسَبِّحُونَهُۥ وَلَهُۥ یَسۡجُدُونَ ۩"),
                            new OA\Property(property: 'surah', ref: '#/components/schemas/200SajdaQuranUthmaniPartialSurahResponse', type: 'object'),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 206),
                            new OA\Property(property: 'juz', type: 'integer', example: 9),
                            new OA\Property(property: 'manzil', type: 'integer', example: 2),
                            new OA\Property(property: 'page', type: 'integer', example: 176),
                            new OA\Property(property: 'ruku', type: 'integer', example: 145),
                            new OA\Property(property: 'hizbQuarter', type: 'integer', example: 70),
                            new OA\Property(property: 'sajda',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'recommended', type: 'boolean', example: true),
                                    new OA\Property(property: 'obligatory', type: 'boolean', example: false)
                                ], type: 'object'
                            )
                        ], type: 'object'
                    )
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranUthmaniResponse', type: 'object')
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200QuranUthmaniEntireResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 5),
                            new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                            new OA\Property(property: 'surah', ref: '#/components/schemas/200QuranUthmaniPartialSurahResponse', type: 'object'),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                            new OA\Property(property: 'juz', type: 'integer', example: 1),
                            new OA\Property(property: 'manzil', type: 'integer', example: 1),
                            new OA\Property(property: 'page', type: 'integer', example: 1),
                            new OA\Property(property: 'ruku', type: 'integer', example: 1),
                            new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                            new OA\Property(property: 'sajda', type: 'boolean', example: false)
                        ], type: 'object'
                    )
                ),
                new OA\Property(property: 'surahs',
                    properties: [
                        new OA\Property(property: '1', ref: '#/components/schemas/200QuranUthmaniPartialSurahResponse', type: 'object')
                    ], type: 'object'
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranUthmaniResponse', type: 'object')
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200MetaReferenceResponse',
            properties: [
                new OA\Property(property: 'surah', type: 'integer', example: 1),
                new OA\Property(property: 'ayah', type: 'integer', example: 1)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SearchEditionSahihResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: "en.sahih"),
                new OA\Property(property: 'language', type: 'string', example: 'en'),
                new OA\Property(property: 'name', type: 'string', example: "Saheeh International"),
                new OA\Property(property: 'englishName', type: 'string', example: "Saheeh International"),
                new OA\Property(property: 'type', type: 'string', example: 'translation')
            ]
        ),
        new OA\Schema(
            schema: '200SearchPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SearchSahihResponse',
            properties: [
                new OA\Property(property: 'count', type: 'integer', example: 25),
                new OA\Property(property: 'matches', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 6),
                            new OA\Property(property: 'text', type: 'string', example: "Guide us to the straight path -"),
                            new OA\Property(property: 'edition', ref: '#/components/schemas/200SearchEditionSahihResponse', type: 'object'),
                            new OA\Property(property: 'surah', ref: '#/components/schemas/200SearchPartialSurahResponse', type: 'object'),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 6),
                        ], type: 'object'
                    )
                )
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SurahResponsePartial',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 5),
                            new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                            new OA\Property(property: 'juz', type: 'integer', example: 1),
                            new OA\Property(property: 'manzil', type: 'integer', example: 1),
                            new OA\Property(property: 'page', type: 'integer', example: 1),
                            new OA\Property(property: 'ruku', type: 'integer', example: 1),
                            new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                            new OA\Property(property: 'sajda', type: 'boolean', example: false)
                        ], type: 'object'
                    )
                )
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SurahResponse',
            type: 'object',
            allOf: [
                new OA\Schema(ref: '#/components/schemas/200SurahResponsePartial'),
                new OA\Schema(properties: [new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranUthmaniResponse', type: 'object')])
            ]
        ),
        new OA\Schema(
            schema: '200SurahQuranSimpleResponse',
            type: 'object',
            allOf: [
                new OA\Schema(ref: '#/components/schemas/200SurahResponsePartial'),
                new OA\Schema(properties: [new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranSimpleResponse', type: 'object')])
            ]
        )
    ],
    responses:[
        new OA\Response(response: '404AyahResponse', description: 'Ayah - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Please specify an Ayah number (1 to 6236).")
                ]
            )
        )),
        new OA\Response(response: '404NotFoundResourceResponse', description: 'Unable to find the requested resource',
            content: new OA\MediaType(mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'code', type: 'integer', example: 404),
                        new OA\Property(property: 'status', type: 'string', example: 'RESOURCE_NOT_FOUND'),
                        new OA\Property(property: 'data', type: 'string', example: 'Not found.')
                    ]
                )
            )
        ),
        new OA\Response(response: '404HizbQuarterResponse', description: 'Hizb Quarter - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "HizbQuarter number should be between 1 and 240")
                ]
            )
        )),
        new OA\Response(response: '404JuzResponse', description: 'Juz - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Juz number should be between 1 and 30")
                ]
            )
        )),
        new OA\Response(response: '404ManzilResponse', description: 'Manzil - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Manzil number should be between 1 and 7")
                ]
            )
        )),
        new OA\Response(response: '404PageResponse', description: 'Page - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Page number should be between 1 and 604")
                ]
            )
        )),
        new OA\Response(response: '404RukuResponse', description: 'Ruku - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Ruku number should be between 1 and 556")
                ]
            )
        )),
        new OA\Response(response: '404SearchResponse', description: "User's Search - Not Found",content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Nothing matching your search was found..")
                ]
            )
        )),
        new OA\Response(response: '404SurahResponse', description: 'Surah - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Surat number should be between 1 and 114.")
                ]
            )
        )),

    ],
    parameters: [
        new OA\PathParameter(parameter: 'AyahNumberParameter', name: 'number', description: 'A number between 1 and 6236',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer', maximum: 6236, minimum: 1), example: 5),
        new OA\PathParameter(parameter: 'AyahEditionNameParameter', name: 'edition', description: 'Edition name',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
        new OA\PathParameter(parameter: 'AyahEditionsListParameter', name: 'editions', description: 'Comma separated list of edition names',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy,quran-simple'),

        new OA\PathParameter(parameter: 'HizbQuarterNumberParameter', name: 'number', description: 'A number between 1 and 240',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer', maximum: 240, minimum: 1), example: 1),
        new OA\QueryParameter(parameter: 'HizbQuarterOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Hizb Quarter by the given number',
            in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 4),

        new OA\PathParameter(parameter: 'JuzNumberParameter', name: 'number', description: 'A number between 1 and 30',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer', enum: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]), example: 1),
        new OA\QueryParameter(parameter: 'JuzOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Juz by the given number', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 4),

        new OA\PathParameter(parameter: 'ManzilNumberParameter', name: 'number', description: 'A number between 1 and 7',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer', enum: [1,2,3,4,5,6,7]), example: 1),
        new OA\QueryParameter(parameter: 'ManzilOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Manzil by the given number', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 4),

        new OA\PathParameter(parameter: 'PageNumberParameter', name: 'number', description: 'A number between 1 and 604',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer', maximum: 604, minimum: 1), example: 1),
        new OA\QueryParameter(parameter: 'PageOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Page by the given number', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 4),

        new OA\PathParameter(parameter: 'RukuNumberParameter', name: 'number', description: 'A number between 1 and 556',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer', maximum: 556, minimum: 1), example: 1),
        new OA\QueryParameter(parameter: 'RukuOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Ruku by the given number', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 4),

        new OA\PathParameter(parameter: 'SearchWordParameter', name: 'word', description: 'Word to search in text',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'Path'),
        new OA\PathParameter(parameter: 'SearchSurahParameter', name: 'surah', description: "Enter a surah number - An integer enum value (between 1 and 114) to search a specific surah or 'all' to search all the text",
            in: 'path', required: true, schema: new OA\Schema(type: 'string', maximum: 114, minimum: 1), example: 1),

        new OA\PathParameter(parameter: 'SurahNumberParameter', name: 'number', description: 'A number between 1 and 114',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer', maximum: 114, minimum: 1), example: 1),
        new OA\QueryParameter(parameter: 'SurahOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Surah by the given number', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 4),

        new OA\QueryParameter(parameter: 'LimitQueryParameter', name: 'limit', description: 'This is the number of ayahs that the response will be limited to', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 1),
    ]
)]

class Documentation extends AlQuranController
{
    public string $dir;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dir = realpath(__DIR__ . '/../../../');

    }
    public function generate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->mc = $this->container->get('cache.memcached.cache');
        $openapi = $this->mc->get('oa_alquran', function (ItemInterface $item) {
            $item->expiresAfter(300);

            return OApi\Generator::scan(
                [
                    $this->dir . '/api/Controllers/v1/Documentation.php',
                    $this->dir . '/api/Controllers/v1/Ayah.php',
                    $this->dir . '/api/Controllers/v1/Edition.php',
                    $this->dir . '/api/Controllers/v1/HizbQuarter.php',
                    $this->dir . '/api/Controllers/v1/Juz.php',
                    $this->dir . '/api/Controllers/v1/Manzil.php',
                    $this->dir . '/api/Controllers/v1/Meta.php',
                    $this->dir . '/api/Controllers/v1/Page.php',
                    $this->dir . '/api/Controllers/v1/Quran.php',
                    $this->dir . '/api/Controllers/v1/Ruku.php',
                    $this->dir . '/api/Controllers/v1/Sajda.php',
                    $this->dir . '/api/Controllers/v1/Search.php',
                    $this->dir . '/api/Controllers/v1/Surah.php'
                ]
            );
        });

        return Response::raw($response, $openapi->toYaml(), 200, ['Content-Type' => 'text/yaml']);
    }
}