@extends('template.main')

@section('title')
  <title>TitipYuk Admin - Transaction Verification</title>
@endsection

@section('content')
<div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i>
          Transaksi user yang ada di TitipYuk
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Nama Pembeli</th>
                  <th>Nama Shopper</th>
                  <th>Total Harga</th>
                  <th>Total Harga Kirim</th>
                  <th>Metode Bayar</th>
                  <th>Bukti Bayar</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Nama Pembeli</th>
                    <th>Nama Shopper</th>
                    <th>Total Harga</th>
                    <th>Total Harga Kirim</th>
                    <th>Metode Bayar</th>
                    <th>Bukti Bayar</th>
                </tr>
              </tfoot>
              <tbody>
                  @foreach ($titipans as $item)    
                  <tr>
                    <td>{{$item->user->name}}</td>
                    <td>{{$item->shopper->name}}</td>
                    <td>{{$item->total_harga}}</td>
                    <td>{{$item->total_harga_kirim}}</td>
                    <td>{{$item->metode_bayar}}</td>
                    <td>{{$item->bukti_bayar}}</td>
                    <td><a href="#" data-toggle="modal" 
                        data-target="#verifModal" 
                        data-id="{{$item->id}}" 
                        class="btn btn-success">Verifikasi</a></td>
                  </tr>                
                  @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
      </div>    
@endsection

@section('modal')
    <!-- Verif Modal-->
    <div class="modal fade" id="verifModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Verified This Transaction?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Verified" below if you are sure to verify this transaction.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <form action="{{url('admin/verified/transaction')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="titipan_id" id="idTitipan">
                    <button type="submit" class="btn btn-primary">Verified</a>
                </form>
            </div>
            </div>
        </div>
        </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(function(){
            $('#verifModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id') ;

            var modal = $(this);
            modal.find('#idTitipan').val(id);
            });
        });
    </script>
@endsection