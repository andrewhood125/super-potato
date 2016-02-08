<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Client;
use App\Tag;
use App\Video;

class CrawlImdbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:imdb {--depth=1} {url=http://www.imdb.com/title/tt0101862/}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl this title on imdb and it\'s related videos.';


    protected $seen = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->seen = collect();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->scrapeTitle($this->argument('url'), $this->option('depth'), 'HEAD -> ');
    }

    public function getTitle($html) {
        if($this->option('verbose')) {
            $this->comment('getTitle');
        }
        $title = null;
        if(preg_match(
            '/h1.*?itemprop.*&nbsp;/',
            $html,
            $dirtyTitle)) {
            $title = substr($dirtyTitle[0], 28, -6);
        } else {
            $this->error('Couldn\'t get title');
            if($this->option('verbose')) {
                $this->error($html);
            }
        }

        return $title;
    }

    public function getNames($html) {
        if($this->option('verbose')) {
            $this->comment('getNames');
        }
        $names = collect();
        if(preg_match_all(
            '/<span.*?class.*?itemprop="name".*?\/span>/',
            $html,
            $dirtyNames)) {
            foreach($dirtyNames[0] as $dirtyName) {
                if(preg_match('/>.*?</',
                    $dirtyName,
                    $dustyName)) {
                    $names->push(substr($dustyName[0],1,-1));
                } else {
                    $this->error('Couldn\'t get dustyName\'s');
                    $this->error($dirtyName);
                }
            }
        } else {
            $this->error('Couldn\'t get names');
            if($this->option('verbose')) {
                $this->error($html);
            }
        }

        return $names;
    }

    public function getRelatedVideoUrls($html) {
        if($this->option('verbose')) {
            $this->comment('getRelatedVideoUrls');
        }
        $relatedVideoUrls = collect();
        if(preg_match_all(
            '/href=".*?tt_rec_tti"/',
            $html,
            $relatedVideoRelativeUrls)) {
            foreach($relatedVideoRelativeUrls[0] as $relativeUrl) {
                $relatedVideoUrls->push('http://www.imdb.com' . substr($relativeUrl,6, -17));
            }
        } else {
            $this->error('Couldn\'t get related video url\'s:');
            if($this->option('verbose')) {
                $this->error($html);
            }
        }

        return $relatedVideoUrls;
    }

    public function getKeywords($html) {
        if($this->option('verbose')) {
            $this->comment('getKeywords');
        }
        $keywords = collect();
        if(preg_match_all(
            '/data-item-keyword=".*?"/',
            $html,
            $dirtyKeywords)) {
            foreach($dirtyKeywords[0] as $dirtyKeyword) {
                $keywords->push(substr($dirtyKeyword, 19, -1));
            }
        } else {
            $this->error('Couldn\'t get keywords:');
            if($this->option('verbose')) {
                $this->error($html);
            }
        }
        return $keywords;
    }

    public function scrapeTitle($url, $depth, $relationship) {
        if($this->option('verbose')) {
            $this->comment('scrapeTitle: ' . $url);
        }
        if($this->seen->contains($url)) return;
        $this->seen->push($url);
        $client = new Client();
        $response = $client->request('GET', $url);
        $html = $response->getBody()->getContents();

        $title = $this->getTitle($html);

        if($title == null) return;

        $this->comment($relationship . $title . ' -> '  . $depth);

        $names = $this->getNames($html);
        $relatedVideoUrls = $this->getRelatedVideoUrls($html);

        usleep(20000);

        $response = $client->get($url . 'keywords');
        $keywords = $this->getKeywords($response->getBody()->getContents());

        $this->insertVideosAndTags($title, $names->all());
        $this->insertVideosAndTags($title, $keywords->all());

        if($depth > 0) {
            foreach($relatedVideoUrls as $relatedVideo) {
                $this->scrapeTitle($relatedVideo, $depth-1, $relationship . $title . ' -> ');
            }
        }
    }

    public function insertVideosAndTags($title, $tags) {

        $video = Video::firstOrCreate([ 'title' => $title ]);
        foreach($tags as $tag) {
            $tag = Tag::firstOrCreate([ 'tag' => $tag ]);
            $video->tags()->sync([$tag->id], false);
        }
    }

}
