<?php

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
// header('Content-Type: application/json; charset=utf-8');

function load_js($file_name){
    $list = file_get_contents($file_name.'.js');
    return json_decode($list,true);
}

$vr_popup = load_js($file_name='vr_popup');
$vr_popup_group = load_js($file_name='vr_popup_group');

$no_group="no-group";
$items_list="plist";

$groups=[ $no_group =>[
    $items_list => []
]];
foreach($vr_popup_group as $k=>$v){
    $item_slug=$v['slug'];
    $groups[$item_slug] = $v;
    $groups[$item_slug][$items_list] = [];
}

$popups=[];

foreach($vr_popup as $k=>$v){
    $item_slug=$v['slug'];
    $item_group=$v['group'];
    $popups[$item_slug] = $v;

    if($v['popup_images']){
        $imgs = json_decode($v['popup_images'],true);
        $img_0_url = $imgs[0]['url'];
    }else{
        $img_0_url = false;
    }
   
    // $popups[$item_slug]["img_0_url"] = $img_0_url;

    // $popups[$item_slug] = [
    //     "name"  => $v['description'],
    //     "id"    => $item_slug,
    //     "landscape" => 1,
    //     "img_0_url" => $img_0_url
    // ];

    if (array_key_exists($item_group,$groups)){
        array_push($groups[$item_group][$items_list],$item_slug);
    }else{
        array_push($groups[$no_group][$items_list],$item_slug);
    }
    // else{
    //     $groups[$item_group]=[$item_slug];
    // }
}
// $imgs = json_decode($v['popup_images'],true);
//     $popups[$item_slug] = [
//         // "name": v['description'],
//         // "id": k,
//         // "landscape": v["landscape"] === undefined ? "0" : v["landscape"],
//         // "img_0_url": imgs == null ? false : current_project.cms_url + imgs[0].url
//         "name"  => $v['description'],
//         "id"    => $v[$item_slug],
//         "landscape" => 1,
//         "img_0_url" => $imgs[0]['url']
//     ];
// echo json_encode( compact('popups','groups','vr_popup_group') );
// echo json_encode( compact('groups','vr_popup_group') );
echo json_encode( compact('popups','groups') );