<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;

class Karaoke extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:karaoke';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         $client = new Client();
        $client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
        $crawler = $client->request('GET', 'https://id.foody.vn/account/login?returnUrl=https://www.foody.vn/ha-noi/entertain/karaoke?q=');
        $form = $crawler->selectButton('bt_submit')->form();
        $crawler = $client->submit($form,array('Email'=>'vitc0nls94@gmail.com','Password'=>'anhyeuem'));
        $crawler = $client->request('GET', "https://www.foody.vn/ha-noi/entertain/karaoke?q=");
        $category =  $crawler->filter('.result-name>.resname>h2>a')->each(function ($node) {
             $url = $node->attr('href');
            // var_dump(substr($url,1,11)). "\n";
             if($url !== NUll &&  substr($url,1,11) !== "thuong-hieu"){
                echo $url ."\n";
                // self::post($url);
                 self::location($url);
            } 
        });
        
        // $address =  $crawler->filter('.result-name>.resname>.result-address>.address>span')->each(function ($node) {
        //     if($node->attr('class')){
        //         echo  $node->text() ."\n";
        //     }
        //     });
    }

    public function post($url){
      
        $client = new Client();
        $client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
        $crawler = $client->request('GET', 'https://www.foody.vn'.$url);
        $linkImg =  $crawler->filter('.microsite-box-content.predefine-album-box>.micro-album>.img-box>a')->each(function ($node) {
             $url =  $node->attr('href'); 
             if($url !== "javascript:void(0);"){
                 echo $url . "  \n";
                self::imgKaraoke($url);
                }
           });
         
    }
    public function imgKaraoke($url){
        $client = new Client();
        $client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
        $crawler = $client->request('GET', 'https://www.foody.vn'.$url);
        $linkImg =  $crawler->filter('.micro-home-album-img>.thumb-image>a')->each(function ($node) {
            $url =  $node->attr('href'); 
            if($url !== "' + item.FullSizeImageUrl + '"){
                return $url;
            }
          });
          dump($linkImg);
    }

    public function location($url){
        $client = new Client();
        $client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
        $crawler = $client->request('GET', 'https://www.foody.vn'.$url);
        $linkImg =  $crawler->filter('.disableSection>div>.res-common-add>span>a')->each(function ($node) {
            $url =  $node->attr('href'); 
            if($node->attr('target')){
            self::googleMap($url);
            }
          });
    }

    public function googleMap($url){
        echo 'https://www.foody.vn'.$url ." Location \n";
        $client = new Client();
        $client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
        $crawler = $client->request('GET', 'https://www.foody.vn'.$url);
        $linkImg =  $crawler->filter("#map")->each(function ($node) use ($client,$crawler) {
            $linkImg =  $linkImg->filter("#map")->each(function ($node) {
                dump($linkImg);
            });
          });
    }
}
