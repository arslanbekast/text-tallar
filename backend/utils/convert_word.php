<?php

// Функция преобразования слова по паттернам регулярного выражения
function convert_word($word) {
    $regexp_array = [
            ['/йа/iu','йъа'],
            ['/йу/iu','йъу'],
            ['/^я/iu','йа'],
            ['/^ё/iu','йо'],
            ['/^ю/iu','йу'],
            ['/^е/iu','йе'],
            ['/[йуеыаоэяию-]я/iu','/я/iu','йа'],
            ['/[йуеыаоэяию-]ё/iu','/ё/iu','йо'],
            ['/[йуеыаоэяию-]е/iu','/е/iu','йе'],
            ['/[йуеыаоэяию-]ю/iu','/ю/iu','йу'],
            ['/[^ к]ъя/iu','/ъя/iu','йа'],
            ['/[^ к]ъё/iu','/ъё/iu','йо'],
            ['/[^ к]ъю/iu','/ъю/iu','йу'],
            ['/[^ к]ъе/iu','/ъе/iu','йе'],
            ['/[^ х]ья/iu','/ья/iu','йа'],
            ['/[^ х]ьё/iu','/ьё/iu','йо'],
            ['/[^ х]ью/iu','/ью/iu','йу'],
            ['/[^ х]ье/iu','/ье/iu','йе']
        ];
    
    $word_all_replaced = $word;
    $words_array = Array();
    $words_all_array = Array();
    $matches_array = Array();
    $ye_replaced = False;
    
    foreach ( $regexp_array as $regexp_item ) {
        
        $search_regexp = $regexp_item[0];
        $pattern = count($regexp_item) == 2 ? $regexp_item[0] : $regexp_item[1];
        $replacement = count($regexp_item) == 2 ? $regexp_item[1] : $regexp_item[2];
        
        if ( preg_match_all($search_regexp, $word, $matches, PREG_OFFSET_CAPTURE) ) { 

            foreach ( $matches[0] as $matche ) {
                $matches_array[] = $matche;
                $match_pos = $matche[1] / 2;
                $word_left = mb_substr($word, 0, $match_pos);
                $word_right = mb_substr($word, $match_pos+mb_strlen($matche[0]));
                $words_array[] = $word_left . preg_replace($pattern, $replacement, $matche[0], 1) . $word_right;
                
            }

            // Манипуляции для всех замен в слове
            $offset = 0;
            if ($ye_replaced && $pattern == '/е/iu') $offset = 4;
            preg_match_all($search_regexp, $word_all_replaced, $matches_all_replaced, PREG_OFFSET_CAPTURE, $offset);
            
            $matche_replaced_more = False;
            foreach ( $matches_all_replaced[0] as $matche ) {
                $match_pos = $matche[1] / 2;
                $matche_replaced = preg_replace($pattern, $replacement, $matche[0], 1);
                if ($matche_replaced_more) $match_pos += 1;
                $word_all_replaced = mb_substr($word_all_replaced, 0, $match_pos).
                                        $matche_replaced.
                                        mb_substr($word_all_replaced, $match_pos+mb_strlen($matche[0]));

                if (!in_array($word_all_replaced, $words_array)) $words_all_array[] = $word_all_replaced;
                

                if (mb_strlen($matche[0]) < mb_strlen($matche_replaced) ) $matche_replaced_more = True;
            }

            // for ($i=0; $i < count($matches_array); $i++) { 
            //     for ($j=$i; $j < count($matches_array); $j++) {
                    
            //     }
            // } 
            
            if ($pattern == '/^е/iu') $ye_replaced = True;

        }

    }
    if ( count($words_array) > 1 ) $words_array = array_merge($words_array, $words_all_array);

    return $words_array;
    }
    

?>