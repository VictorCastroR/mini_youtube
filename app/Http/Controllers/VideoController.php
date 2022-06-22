<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\VsVideo;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vs_videos = VsVideo::where('status','=',1)->get();
        $videos = $this->cargarDT($vs_videos );
        return view('videos.index')->with('videos',$videos);
    }

    public function cargarDT($consulta)
    {
        $videos = [];

        foreach ($consulta as $key => $value){

            $ruta = "eliminar".$value['id'];
            $eliminar =     route('delete-video', $value['id']);
            $actualizar =  route('videos.edit', $value['id']);

            $acciones = '
                <div class="btn-acciones">
                    <div class="btn-circle">
                        <a href="'.$actualizar.'" role="button" class="btn btn-success" title="Actualizar">
                            <i class="far fa-edit"></i>
                        </a>
                         <a href="#'.$ruta.'" role="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#'.$ruta.'">
                            <i class="far fa-trash-alt"></i>
                        </a>

                    </div>
                </div>

                 <!-- Modal -->
            <div class="modal fade" id="'.$ruta.'" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">¿Seguro que deseas eliminar este video?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-primary">
                        <small>
                            '.$value['id'].', '.$value['title'].'                 </small>
                      </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                      <a href="'.$eliminar.'" type="button" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

            ';
            $miniatura = "<img src='miniatura/".$value['image']."'>";

            $videos[$key] = array(
                $acciones,
                $miniatura,
                $value['id'],
                $value['title'],
                $value['description'],
                $value['image'],
                $value['video_path'],
                $value['name'],
                $value['email']

            );

        }

        return $videos;
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
        $video = new Video();
        $user = \Auth::user();
        $video->user_id = $user->id;
        $video->title = $request->input('title');
        $video->description = $request->input('description');
        $video->status =1;

        //Subir la miniatura
        $image = $request->file('image');
        if($image){
            $image_path = time().$image->getClientOriginalName();
            \Storage::disk('images')->put($image_path,
                \File::get($image));
            $video->image = $image_path;
        }

        //Subir video
        $video_file = $request->file('video');
        if($video_file){
            $video_path = time().$video_file->getClientOriginalName();
            \Storage::disk('videos')->put($video_path,
                \File::get($video_file));
            $video->video_path = $video_path;
        }
        $video->save();
        return redirect()->route('videos.index')->with(array(
        'message'=> 'El video se ha subido correctamente'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $video = VsVideo::find($id);
        $comments = Comentario::where('video_id', '=', $id)->get();
        return view('videos.show')->with('video',$video)
            ->with('comments', $comments);

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
    public function delete_video($video_id){
        $video = Video::find($video_id);
        if($video){
            $video->status = 0;
            $video->update();
            return redirect()->route('videos.index')->with(array(
                "message" => "El video se ha eliminado correctamente"
            ));
        }else{
            return redirect()->route('videos.index')->with(array(
                "message" => "El video que trata de eliminar no existe"
            ));
        }
    }
    public function getImage($filename){
       $file = \Storage::disk('images')->get($filename);
       return new Response($file, 200);
    }

    public function getVideo($filename){
        $file = \Storage::disk('videos')->get($filename);
        return new Response($file, 200);
     }


}
