<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Goutte\Client;
use App\Tag;
use App\Video;

class CrawlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:rotten-tomatoes {url=http://www.rottentomatoes.com/m/1037864-father_of_the_bride/ : The url to the movei on rotten tomatoes (http://www.rottentomatoes.com/m/1037864-father_of_the_bride/) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawl rotten tomatoes and add videos with tags';

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
        $movie_urls = [$this->argument('url')];

        $client = new Client();
        $crawler = $client->request('GET', $this->argument('url'));

        $crawler->filter('a[href^="/m/"]')->each(function ($node) use(&$movie_urls) {
            array_push($movie_urls, 'http://www.rottentomatoes.com'.$node->attr('href'));
        });

        while(count($movie_urls) > 0 ) {
            $new_movie_urls = [];
            foreach($movie_urls as $url) {
                array_push($new_movie_urls, $this->scrape($url, true));
            }
            $movie_urls = $new_movie_urls;
            var_dump($movie_urls);
        }

    }

    public function scrapeMovies($movie_urls) {
        foreach($movie_urls as $url) {
            $this->scrapeMovies($this->scrape($url, true));
        }
    }

    public function scrape($url, $get_urls) {
        $this->comment('Scraping '.$url);
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $title = '';
        $tags = [];
        $movie_urls = [];
        $crawler->filter('h1.title')->each(function ($node) use (&$title) {
            $index = strpos($node->html(), '<');
            $title = trim(substr($node->html(), 0, $index));
        });

        $crawler->filter('span[itemprop=genre]')->each(function ($node) use (&$tags){
            array_push($tags, $node->text());
        });
        $crawler->filter('span[itemprop=name]')->each(function ($node) use (&$tags){
            array_push($tags, trim($node->text()) );
        });

        $this->insertVideosAndTags($title, $tags);

        if($get_urls) {
            $crawler->filter('a[href^="/m/"]')->each(function ($node) use(&$movie_urls) {
                array_push($movie_urls, 'http://www.rottentomatoes.com'.$node->attr('href'));
            });
        }

        return $movie_urls;
    }

    public function insertVideosAndTags($title, $tags) {
        $this->comment($title.' with '. implode(',',$tags));

        $video = Video::firstOrCreate([ 'title' => $title ]);
        foreach($tags as $tag) {
            $tag = Tag::firstOrCreate([ 'tag' => $tag ]);
            $video->tags()->sync([$tag->id], false);
        }
    }

}
