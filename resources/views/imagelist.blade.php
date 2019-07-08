@extends('master')
@section('content')
	<div class="container">
		<h4 class="text-center">Matched Images</h4>
		<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Website</th>
		      <th scope="col">Image</th>
		    </tr>
		  </thead>
		  <tbody>
		  	@if(count($matchedImages)>0)
		  	@foreach ($matchedImages as $match)
		    <tr>
		    	<td>{{$loop->iteration}}</td>
		    	<td>{{$match['website']}}</td>
		    	<td>
		    		<a href="{{$match['images']}}" data-toggle="lightbox">
						{{$match['src']}}
					</a>
				</td>
		    </tr>
		    @endforeach
		    @else
		    <tr style="text-align: center;">
		    	<td colspan="3">No image found</td>
		    </tr>
		    @endif
		  </tbody>
		</table>
	</div>

	<div class="container mt-3">
		<h4 class="text-center">Possible Matches</h4>
		<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Website</th>
		      <th scope="col">Image</th>
		    </tr>
		  </thead>
		  <tbody>
		  	@if(count($possibleMatches)>0)
		    @foreach ($possibleMatches as $match)
		    <tr>
		    	<td>{{$loop->iteration}}</td>
		    	<td>{{$match['website']}}</td>
		    	<td>
		    		<a href="{{$match['images']}}" data-toggle="lightbox">
						{{$match['src']}}
					</a>
				</td>
		    </tr>
		    @endforeach
		    @else
		    <tr style="text-align: center;">
		    	<td colspan="3">No image found</td>
		    </tr>
		    @endif
		</table>
	</div>
<script src="{{URL::to('public/ekko-lightbox.js')}}"></script>
<script type="text/javascript">
  $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox({
            alwaysShowClose: true
      });
  });
</script>
@stop