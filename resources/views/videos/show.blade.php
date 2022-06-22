@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="card text-center">
            <div class="card-header">
                <h1>{{ $video->title }}</h1>
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    <p class="card-text">{{ $video->description }}</p>
                </h5>

                <video controls id="video-player" width="100%">
                    <source src="{{route('fileVideo', ['filename' =>$video->video_path])}}"></source>

                    Tu navegador no es compatible con HTML5
                </video><br>
                <a href="#" class="btn btn-primary">Editar</a>
                <a href="#" class="btn btn-danger">Eliminar</a>
            </div>

            <div class="card-footer text-muted">
                Subido por {{ $video->name }} hace {{ $video->created_at }}
            </div>
        </div>

    <hr/>
    <h4>Comentarios</h4>
    @if(Auth::check())
        <form class="col-md-4" method="post" action="{{route('comentarios.store')}}">
            {!! csrf_field() !!}
            <input type="hidden" name="video_id" value="{{$video->id}}" required>
            <p>
           <textarea class="form-control" name="body" required>

           </textarea>
            </p>
            <input type="submit" value="Comentar" class="btn btn-success">

        </form>
        <div class="clearfix"></div>
        <hr/>
    @endif


  <div id="comments-list">
       @foreach($comments as $comment)
           <div class="comment-item col-md-12 pull-left">
               <div class="panel panel-default comment-data">
                   <div class="panel-heading">
                       <div class="panel-title">
                           <strong>Pepe</strong>  {{$comment->created_at}}
                       </div>
                   </div>
                   <div class="panel-body">
                       {{$comment->body}}
                    </div>
               </div>
           </div>
       @endforeach
   </div>


</div>

    {{-- $user = \Auth::user();
 $video->user_id = $user->id; --}}
@endsection
