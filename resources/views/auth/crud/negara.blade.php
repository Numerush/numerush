@extends('template.main')

@section('title')
  <title>TitipYuk Admin - Negara</title>
@endsection

@section('content')
<div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i>
          Negara yang ada di TitipYuk
          <a href="#" class="btn btn-success">Tambah Negara</a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Nama Negara</th>
                  <th>Bendera</th>
                  <th>Wallpaper</th>
                  <th>Ubah</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Nama Negara</th>
                    <th>Bendera</th>
                    <th>Wallpaper</th>
                    <th>Ubah</th>
                </tr>
              </tfoot>
              <tbody>
                  @foreach ($negaras as $item)    
                  <tr>
                    <td>{{$item->nama_negara}}</td>
                    <td><img class="img-thumbnail" style="width:75px;height:50px" 
                        src="{{url($item->bendera_path)}}" alt=""></td>
                    <td><img class="img-thumbnail" style="width:100px;height:100px" 
                        src="{{url($item->wallpaper)}}" alt=""></td>
                    <td><a href="#" class="btn btn-primary">Ubah</a></td>
                  </tr>                
                  @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
      </div>    
@endsection