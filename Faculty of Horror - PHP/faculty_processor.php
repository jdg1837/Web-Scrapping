<?php

include_once('./simple_html_dom.php');
$data = file_get_contents('./episodes_raw.json');
$index = json_decode($data);

function get_episode_number(&$entry){
    for($i = 0; $i < $entry->episode_count; $i++){
        $episode = &$entry->episodes[$i];
        $title = $episode->title;
        $matches = array();
        preg_match('/Episode ([0-9]+)/', $title, $matches);
        if(!is_null($matches)){
            $numbers = array();
            preg_match('/([0-9]+)/', $matches[0], $numbers);
            $episode->episode = $numbers[0];
        }
    }
}

function get_entry_full(&$entry){
    $index_item_full = $entry->index_item;
    $index_parts = explode(',', $index_item_full);
    if(count($index_parts) > 1){
        $index_item_full = $index_parts[1]." ".$index_parts[0];
    }
    $entry->index_item_full = trim($index_item_full);
}

function get_entry_type(&$entry){
    $media_type = "Unknown";
    $query = str_replace(" ", "+", $entry->index_item_full);
    $src = "https://www.imdb.com/find?q=".$query."&ref_=nv_sr_sm";
    $html = file_get_html($src, false);
    $result = $html->find('.findSectionHeader');
    if(!empty($result)){
        $media_type = $result[0]->plaintext;
        if($media_type == 'Title'){
            $media_type = 'Film';
        }
    }
    //echo $media_type."\n\n\n";
    $entry->media_type = $media_type;
}

foreach($index as $entry){
    #Get episode number
    get_episode_number($entry);
    #Get full name
    get_entry_full($entry);
    #Get item type
    get_entry_type($entry);
}

#encode and save to file
//print_r($index);
$json_data = json_encode($index, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
//echo $json_data;
file_put_contents('episodes_processed.json', $json_data);