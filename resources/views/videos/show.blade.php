@extends('layouts.app')

@section('body')

  <div class="container">

    <div class="row">
      <div class="col-md-7 col-md-offset-2">
        <div class="page-header">
          <div class="row">
            <div class="col-xs-9">
              <h3>
                <small>If you like</small>
                <input type="text"
                    id="mainSearch"
                    class="typeahead"
                    placeholder="{{ $video->title }}"
                    value="{{ $video->title }}" />
                <span id="titleSpan">{{ $video->title }}</span>
                <small>you might also like</small>
              </h3>
            </div>
            <div class="col-xs-2 col-xs-offset-1">
              <h3 class="text-right"><a href="{{ route('welcome') }}" class="btn btn-info glyphicon glyphicon-repeat"></a></h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-7 col-md-offset-2">
        <ul id="relatedMovies" class="list-group">
            <p id="relatedMoviesSpinner" class="center">
                <i class="fa fa-cog fa-spin fa-4x"></i>
            </p>
        </ul>
      </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <form id="newTagForm">
                <div class="col-sm-3 newTag">
                    <input id="newTag" type="text" class="typeahead form-control" placeholder="New Tag..">
                </div>
            </form>
            <div class="tags">
                @forelse($video->tags as $tag)
                    <span class="label label-primary tag">
                        <span>{{ $tag->tag }}</span>
                        <span class="badge removeTag">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                        </span>
                    </span>
                @empty
                    <span class="label label-primary tag">No tags yet</span>
                @endforelse
            </div>
        </div>
    </div>

  </div>

@stop

@section('script')

      var tags = new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tag'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: {
            cache: true,
            url: '/api/tags/search?q=%QUERY',
            wildcard: '%QUERY'
          }
      });

      $('#newTag').typeahead({
          hint: false,
          highlight: true
      }, {
          name: 'tags',
          display: 'tag',
          source: tags
      });

    $('#newTagForm').submit(function(event) {
        // prevent submit
        event.preventDefault();
        var tag = $('#newTag').typeahead('val');
        // Add to tags
        var tagHtml = '<span class="label label-primary tag">' +
                        '<span>' + tag + '</span>' +
                        '<span class="badge removeTag">' +
                            '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' +
                        '</span>' +
                      '</span>';

        $('.tags').prepend(tagHtml);
        // Clear input
        // Persist
        $.post('/videos/attach', {
            video_id: {{ $video->id }},
            tag: tag
        });
        // focus
        $('#newTag').focus();
        $('.removeTag').on('click', removeTag);
        $('.typeahead').typeahead('close');
        $('#newTag').typeahead('val', '');
        document.getElementById("newTagForm").reset();
    });

    var removeTag = function(event) {
        // Get tag text
        var tag = $(this).prev().text();
        // remove tag
        $(this).parent().remove();
        // persist
        $.post('/videos/detach', {
            video_id: {{ $video->id }},
            tag: tag
        });
    };

    $('.removeTag').click(removeTag);

    var adjustWidth = function(e) {
        if (e.which !== 0) { // only characters
            var c = '';
            if(e.charCode !== 0) {
                c = String.fromCharCode(e.keyCode|e.charCode);
            }
            $span = $('#titleSpan');
            $span.text($(this).val() + c) ; // the hidden span takes
            // the value of the input
            $inputSize = ($span.width() > 35) ? $span.width() : 35;
            $(this).css("width", $inputSize) ; // apply width of the span to the input
        }
    };

      var videos = new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: {
            url: '/api/videos/search?q=%QUERY',
            wildcard: '%QUERY'
          }
      });

    $('#mainSearch').bind('typeahead:select', function(ev, suggestion) {
        window.location.href = "/videos/" + suggestion.id;
    });

    $('#mainSearch').focus(function() {
        $('#mainSearch').typeahead('val', '');
    });

      $('#mainSearch').typeahead({
          hint: false,
          highlight: true
      }, {
          name: 'videos',
          display: 'title',
          source: videos
      });

    $('#mainSearch').keypress(adjustWidth);

    $('#mainSearch').css("width", $('#titleSpan').width());

    $.get('/api/videos/{{ $video->id }}/relatedVideos', function(relatedVideos) {
        $('#relatedMoviesSpinner').remove();
        for(title in relatedVideos) {
            var video = relatedVideos[title];
            $('#relatedMovies').append(
                '<li class="list-group-item">' +
                    '<span class="badge" data-toggle="tooltip" data-placement="right" title="<ul class=\'tags-in-common list-unstyled\'>' +
                        '<li>' + video.tagsInCommon.join('</li><li>') + '</li>' +
                        '</ul>">' + video.tagsInCommon.length + '</span>' +
                    '<a href="/videos/' + video.id + '">' + video.title + '</a>' +
                '</li>');

        }
        $('[data-toggle="tooltip"]').tooltip({html:true})
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        $('#relatedMoviesSpinner').html('<p class="fa fa-times fa-4x">Couldn\'t find any related videos.</p>');
    });

@stop
