<?php

namespace Api\Controllers\partials;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.1.0',
    info: new OA\Info(
        version: 'v1',
        description: 'AlQuran API - Ayah',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Ayah')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200EditionQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: 'quran-uthmani-quran-academy'),
                new OA\Property(property: 'language', type: 'string', example: 'ar'),
                new OA\Property(property: 'name', type: 'string', example: "القرآن الكريم برسم العثماني (quran-academy)" ),
                new OA\Property(property: 'englishName', type: 'string', example: "Modified Quran Uthmani Text from the Quran Academy to work with the Kitab font"),
                new OA\Property(property: 'format', type: 'string', example: 'text'),
                new OA\Property(property: 'type', type: 'string', example: 'quran'),
                new OA\Property(property: 'direction', type: 'string', example: 'rtl')
            ]
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
            schema: '200AyahQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 5),
                new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranUthmaniResponse', type: 'object'),
                new OA\Property(property: 'surah',
                    properties: [
                        new OA\Property(property: 'number', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                        new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                        new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                        new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                        new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
                    ], type: 'object'
                ),
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
            schema: '200AyahQuranSimpleResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 5),
                new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranSimpleResponse', type: 'object'),
                new OA\Property(property: 'surah',
                    properties: [
                        new OA\Property(property: 'number', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                        new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                        new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                        new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                        new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
                    ], type: 'object'
                ),
                new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                new OA\Property(property: 'juz', type: 'integer', example: 1),
                new OA\Property(property: 'manzil', type: 'integer', example: 1),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'ruku', type: 'integer', example: 1),
                new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                new OA\Property(property: 'sajda', type: 'boolean', example: false)
            ], type: 'object'
        )
    ],
    parameters: [
        new OA\PathParameter(parameter: 'AyahNumberParameter', name: 'number', description: 'Ayah Number',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 5),
        new OA\PathParameter(parameter: 'AyahEditionNameParameter', name: 'edition', description: 'Edition name',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
        new OA\PathParameter(parameter: 'AyahEditionsListParameter', name: 'editions', description: 'Comma separated list of edition names',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy,quran-simple')
    ]
)]
class Ayat
{

}