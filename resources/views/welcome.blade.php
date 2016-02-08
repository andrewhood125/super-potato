@extends('layouts.app')

@section('body')

  <div class="container">

    <div class="row">
      <div class="col-md-7 col-md-offset-2">
        <div class="page-header">
            <form>
              <h3><small>If you like</small> <input class="typeahead" type="text" id="mainSearch" /> <small>you might also like</small></h3>
            </form>
        </div>
      </div>
    </div>

  </div>

@stop

@section('script')

    var videos = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '/videos',
            cache: false
        }
    });

    $('#mainSearch').bind('typeahead:select', function(ev, suggestion) {
        window.location.href = "/videos/" + suggestion.id;
    });


    $('#mainSearch').typeahead({
        hint: false,
        highlight: true
    }, {
        name: 'videos',
        display: 'title',
        source: videos
    });


@endsection
