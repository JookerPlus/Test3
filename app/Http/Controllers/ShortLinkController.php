<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShortLinkController extends Controller
{
    public function createShortLink(Request $request)
    {
        $host = request()->getSchemeAndHttpHost();
        $user_link = $request->post('link');
        $link = DB::table('short_link')
            ->where('link', $user_link)
            ->value('short_link');
        if ($link) {
            echo $host, '/', $link;
            die();
        }
        $short_link = $this->getShortLink();
        DB::table('short_link')
            ->insert(['short_link' => $short_link, 'link' => $user_link]);
        echo $host, '/', $short_link;
    }

    public function useRedirect(string $short_link)
    {
        $link = DB::table('short_link')
            ->where('short_link', $short_link)
            ->value('link');
        if ($link) {
            return redirect($link);
        }
    }

    public function getShortLink()
    {
        //Попытка создать подобие рандомной строки из 6 символов, включающей в себя цифры и символы
        $string = '000000';
        $string_position = [0, 1, 2, 3, 4, 5];
        $number_array = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $letter_upper_array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $letter_lower_array = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $value_rand_array = [$number_array, $letter_lower_array, $letter_upper_array];
        while ($string_position) {
            $current_array = $value_rand_array[array_rand($value_rand_array)];
            $current_position = array_rand($string_position);
            $string[$current_position] = $current_array[array_rand($current_array)];
            unset($string_position[$current_position]);
        }
        //Проверка на уникальность
        if (DB::table('short_link')
            ->where('short_link', $string)
            ->value('short_link')) {
            $this->getShortLink();
        }
        return $string;
    }
}
