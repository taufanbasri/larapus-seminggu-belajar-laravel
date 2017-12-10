@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <ul class="breadcrumb">
          <li><a href="{{ url('/home') }}">Dashboard</a></li>
          <li><a href="{{ url('/admin/books') }}">Buku</a></li>
          <li class="active">Ubah Buku</li>
        </ul>

        <div class="panel panel-default">
          <div class="panel-heading">
            <h2 class="panel-title">Ubah Buku</h2>
          </div>
          <div class="panel-body">
            {!! Form::model($member, ['url' => route('members.update', $member->id), 'method' => 'put', 'class'=>'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
              @include('members._form')
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
