@extends('template.main')

@section('title')
  <title>TitipYuk Admin - Dashboard</title>
@endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Overview</li>
  </ol>

  @if(session()->has('error'))
  <div class="col-xl-12 mb-3">
      <div class="card text-white bg-danger o-hidden h-100">
        <div class="card-body">
        <div class="mr-5">{{session()->get('error')}}</div>
        </div>
        
      </div>
    </div>
  @endif
  <!-- Icon Cards-->
  
  <h1>Selamat Datang Admin</h1>
  <hr>
  <p>Silahkan memilih menu dikiri untuk mengubah data dan konfirmasi manual.</p>

@endsection