@extends('layouts.docs')

@section('scripts')
@parent
<script>

$(function(){

  $('table').addClass('table table-bordered table-hover table-condensed');
  $('.section pre code').each(function(i, e) {hljs.highlightBlock(e)});
  
})
</script>
@stop

@section('content')
{{ $content }}
@stop