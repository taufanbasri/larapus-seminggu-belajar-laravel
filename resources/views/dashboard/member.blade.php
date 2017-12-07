@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                  Selamat datang di Larapus.
                  <table class="table">
                    <tbody>
                      <tr>
                        <td class="text-muted">Buku dipinjam</td>
                        <td>
                          @if ($borrowLogs->count() == 0)
                            Tidak ada buku dipinjam
                          @endif

                          <ul>
                            @foreach ($borrowLogs as $borrowLog)
                              <li>{{ $borrowLog->book->title }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
