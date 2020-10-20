<?php
include_once('./simple_html_dom.php');

$tld = "https://www.facultyofhorror.com/episode-index/";

function extractFromPage($src)
{
    $html = file_get_html($src, false);
    $answer = array();
    if(!empty($html)) {
        foreach($html->find('.name_directory_name_box') as $divClass) {
            //title
            $entry = $divClass->find("strong",0)->plaintext;
            //episodes
            $count = 0;
            $episode_titles = array();
            $episode_links = array();
            //get firstmost divisor
            $top_div = $divClass->find('div', 0);
            //get all list items under it
            foreach($top_div->find('ul li a') as $list_entry){
                $title = $list_entry->innertext;
                //item is valid if its not a subtitle
                if($title != ''){
                    if($title[0] != '<'){
                        $episode_titles[] = $title;
                        $episode_links[] = $list_entry->href;
                        $count++;
                    }
                }
            }
            $obj = new \stdClass();
            $obj->index_item = $entry;
            $obj->episode_count = $count;
            $episodes = array();
            for($i = 0; $i < $count; $i++){
                $episode_info = array();
                $episode_info['title'] = $episode_titles[$i];
                $episode_info['link'] = $episode_links[$i];
                $episodes[] = $episode_info;
            }
            $obj->episodes = $episodes;
            $answer[] = $obj;     
        }
    }
    //print_r($answer);
    return $answer;
}

$index = array();
$main_hmtl = file_get_html($tld, false);
#Get all
foreach($main_hmtl->find('.name_directory_index a') as $index_tab)
{
    $letter_index = extractFromPage($index_tab->href);
    $index = array_merge($index, $letter_index);
}
#Get one
// $letter_index = extractFromPage("https://www.facultyofhorror.com/episode-index/?name_directory_startswith=A");
// $index = array_merge($index, $letter_index);

#encode and save to file
//print_r($index);
$json_data = json_encode($index, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
//echo $json_data;
file_put_contents('episodes_raw.json', $json_data);