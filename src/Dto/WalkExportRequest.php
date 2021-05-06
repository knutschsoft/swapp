<?php
declare(strict_types=1);

namespace App\Dto;

//#[ApiResource(
//    collectionOperations: [
//    //"get",
//    "walk_export" => [
//        //"messenger" => true,
//        "messenger" => "input",
//        //"input_formats"     => [
//        //    "csv" => ["text/csv"],
//        //    "jsonld",
//        //],
//        "input" => WalkExportRequest::class,
//        "output" => false,
//        //"output" => Walk::class,
//        "method" => "get",
//        "path" => "/api/walks/export",
//    ],
//],
//    itemOperations: [
//    "get",
//],
//    //attributes: ["pagination_items_per_page" => 5],
//    //formats: ['jsonld', 'csv' => ['text/csv']],
//    //normalizationContext: ["groups" => ["team:read"]]
//)]
class WalkExportRequest
{

}
