@extends('master')
@section('content')
	<form method="post" action="{{URL::to('matchimage')}}" enctype="multipart/form-data">
		<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
		<div class="fileinput fileinput-new" data-provides="fileinput">
			<label for="exampleInputEmail1">Select Image</label><br>
			<div class="fileinput-new img-thumbnail" style="width: 200px; height: 150px;">
				<img src="public/img/placeholder.jpg" data-src="public/placeholder.jpg"  alt="..." class="img-fluid">
			</div>
			<div class="fileinput-preview fileinput-exists img-thumbnail" style="max-width: 200px; max-height: 150px;"></div>
			<div>
				<span class="btn btn-outline-secondary btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span>
					<input type="file" name="imagefile" required="">
				</span>
				<a href="#" class="btn btn-outline-secondary fileinput-exists" data-dismiss="fileinput">Remove</a>
			</div>
		</div>
		<!-- <input type="file" accept="image/*" capture="camera" id="imagefile" name="imagefile" v-on:change="showCaptured();"> -->
		<div class="form-group">
			<label for="exampleInputEmail1">Submit Url</label>
			<div class="row">
				<div class="col-8 submiturl">
					<div class="form-group" id="firstUrl">
						<input type="url" class="form-control" name="submiturl[]" placeholder="Enter Submit Url">
					</div>
				</div>
				<div class="col-3 position-relative">
					<div class="form-group position-absolute fixed-bottom">
						<button id="add" type="button" class="btn btn-primary">Add Another Url</button>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="exampleInputEmail1">Search Tags</label>
			<div class="row">
				<div class="col-8 tagdiv">
					<div class="form-group" id="firsttag">
						<input type="text" class="form-control" name="searchtags[]" placeholder="Enter Search Tag">
					</div>
				</div>
				<div class="col-3 position-relative">
					<div class="form-group position-absolute fixed-bottom">
						<button id="addtag" type="button" class="btn btn-primary">Add Another Tag</button>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="radio-inline mr-3"><input type="radio" name="option" value="1" checked>  Using MD5</label>
			<label class="radio-inline"><input type="radio" value="2" name="option">  Using Jessenger</label>
		</div>
		<div class="form-group">
	  		<button type="submit" class="btn btn-primary">Submit</button>
	  	</div>
	</form>

<style type="text/css">
	label {
		font-weight: bold;
	}
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/4.0.0/js/jasny-bootstrap.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#add").click(function(){
			// var clone = $("#firstUrl").clone().appendTo(".submiturl");
			var clone = $("#firstUrl").clone();
			$(clone).find('input').val("");
			$(".submiturl").append(clone);
		});

		$("#addtag").click(function(){
			var clone = $("#firsttag").clone();
			$(clone).find('input').val("");
			$(".tagdiv").append(clone);
		});
		
	});
</script>
@stop