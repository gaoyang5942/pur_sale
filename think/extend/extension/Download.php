<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/29
 * Time: 13:31
 */
namespace extension;

use think\Log;
use GuzzleHttp\Client;

Class Download
{


    /**
     * 远程地址下载
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function download($url)
    {
        // 本地存储路径
        $path = BASE_ROOT . 'public/Uploads/' . date('Ymd/');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $path_parts = pathinfo($url);
        $complete_url = strtr(rawurlencode($url), ['%2F' => '/', '%3A' => ':']);
        $file_name = time() . $path_parts['basename'];
        $i = 0;
        do {
            $error = 0;
            try {
                $client = new Client(['verify' => false]);

                $client->get($complete_url,
                    ['save_to' => $path . $file_name]);
            } catch (\Exception $ex) {
                Log::info('下载图片失败， 图片的url为' . $complete_url);
                $error = 1;
            }
            $i++;
        } while ($i < 3 && $error == 1);

        return ($error == 1 ? '' : '/Uploads/' . date('Ymd/') . $file_name);
    }
}