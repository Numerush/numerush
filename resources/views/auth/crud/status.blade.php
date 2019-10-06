@extends('template.main')

@section('title')
  <title>TitipYuk Admin - Status</title>
@endsection

@section('content')
<div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i>
          Status yang ada di TitipYuk
          <a href="#" class="btn btn-success">Tambah Status</a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID Status</th>
                  <th>Nama Status</th>
                  <th>Ubah</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>ID Status</th>
                    <th>Nama Status</th>
                    <th>Ubah</th>
                </tr>
              </tfoot>
              <tbody>
                  @foreach ($status as $item)    
                  <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->status}}</td>
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