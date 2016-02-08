<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Tag;
use App\Video;

class VideosController extends Controller
{

    public function search(Request $request) {
        return Video::where('title', 'like', $request->input('q') . '%')->get();
    }

    public function relatedVideos($id) {
        $video = Video::with('tags.videos')->findOrFail($id);
        return $video->relatedVideos->take(5);
    }

    public function welcome() {
        $video = Video::all()->random(1);
        return redirect()->route('videos.show', $video->id);
    }

    public function attach(Request $request) {
        if($request->has('video')) {
            $video = Video::firstOrCreate([ 'title' => $request->input('video') ]);
        }

        if($request->has('video_id')) {
            $video = Video::findOrFail($request->input('video_id'));
        }

        if($request->has('tags')) {
            foreach($request->input('tags') as $tag) {
                $tag = Tag::firstOrCreate([ 'tag' => $tag ]);
                $video->tags()->sync([$tag->id], false);
            }
        }

        if($request->has('tag')) {
            $tag = Tag::firstOrCreate([ 'tag' => $request->input('tag') ]);
            $video->tags()->sync([$tag->id], false);
        }
    }

    public function detach(Request $request) {
        $video = Video::findOrFail($request->input('video_id'));
        $tag = Tag::where('tag', $request->input('tag'))->firstOrFail();
        $video->tags()->detach($tag->id);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Video::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $video = Video::firstOrCreate([ 'title' => $request->input('title') ]);
        return redirect()->route('videos.show', $video->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('videos.show')->with('video', Video::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
