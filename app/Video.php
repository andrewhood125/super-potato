<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [ 'title' ];
    protected $hidden = [ 'pivot' ];
    public $timestamps = false;

    public function tags() {
        return $this->belongsToMany('App\Tag');
    }

    public function getRelatedVideosAttribute() {

        $relatedVideos = collect();

        foreach($this->tags as $tag) {
            foreach($tag->videos as $relatedVideo) {

                // Don't need to process $this
                if($relatedVideo->title == $this->title) continue;

                $v = $relatedVideos->get($relatedVideo->title);

                if($v) {
                    // Add another tag that these videos share in common
                    $v->tagsInCommon->push($tag->tag);
                } else {
                    // Add relatedVideo with first tag in common
                    $relatedVideo->tagsInCommon = collect($tag->tag);
                    $relatedVideos->put($relatedVideo->title, $relatedVideo);
                }

            }
        }

        return $relatedVideos->sortByDesc( function($video) {
            return $video->tagsInCommon->count();
        });
    }
}
