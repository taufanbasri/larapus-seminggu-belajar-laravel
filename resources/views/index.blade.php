@extends('reservation::layouts.module')

@section('content')
  <ul class="breadcrumb">
    <li><a href="">Dashboard</a></li>
    <li class="active">Down Payment</li>
  </ul>
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="content-top">
        <div class="page-title">
          <a href="{{ route('down-payment.create') }}" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add</a>
          <h2>Down Payment</h2>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Guest Name</th>
              <th>Amount</th>
              <th>Total Due</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($downPayments as $index => $dp)
              <tr>
                <td>{{ $index+1 }}</td>
                <td>{{ $dp->reservation->guest->name }}</td>
                <td>{{ $dp->amount }}</td>
                <td>{{ ($dp->reservation->price * $dp->reservation->quantity) - $dp->amount }}</td>
                <td>
                  <div class="row   pull-right">
                    <div class="col-sm-4">
                      <a class="btn btn-success" href="{{ route('dp.print', $dp->id) }}" target="_blank"><i class="fa fa-print"></i> Print</a>
                    </div>
                    <div class="col-sm-4">
                      <a class="btn btn-warning" href="{{ route('down-payment.edit', $dp->id) }}"><i class="fa fa-edit"></i> Edit</a>
                    </div>
                    <div class="col-sm-4">
                      <a class="btn btn-danger" href="{{ route('down-payment.destroy', $dp->id) }}" data-method="delete" data-confirm="Are you sure want to delete?"><i class="fa fa-trash-o"></i> Delete</a>
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>

        </table>
      </div><!-- table-responsive -->

      <ul class="pagination pagination-split nomargin">
          {{-- {{ $rooms->links() }} --}}
      </ul>

    </div>
  </div>
@endsection
