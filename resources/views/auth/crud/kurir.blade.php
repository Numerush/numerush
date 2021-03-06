@extends('template.main')

@section('title')
  <title>TitipYuk Admin - Kurir</title>
@endsection

@section('content')
<div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i>
          Kurir yang ada di TitipYuk
          <a href="#" class="btn btn-success">Tambah Kurir</a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Nama Kurir</th>
                  <th>Kode Kurir</th>
                  <th>Ubah</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Nama Kurir</th>
                    <th>Kode Kurir</th>
                    <th>Ubah</th>
                </tr>
              </tfoot>
              <tbody>
                  @foreach ($kurirs as $item)    
                  <tr>
                    <td>{{$item->nama_jasa}}</td>
                    <td>{{$item->kode}}</td>
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