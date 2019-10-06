@extends('template.main')

@section('title')
  <title>TitipYuk Admin - Kategori</title>
@endsection

@section('content')
<div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i>
          Kategori yang ada di TitipYuk
          <a href="#" class="btn btn-success">Tambah Kategori</a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Nama Kategori</th>
                  <th>Ubah</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Ubah</th>
                </tr>
              </tfoot>
              <tbody>
                  @foreach ($kategoris as $item)    
                  <tr>
                    <td>{{$item->nama_kategori}}</td>
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